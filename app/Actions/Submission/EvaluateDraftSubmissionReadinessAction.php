<?php

declare(strict_types=1);

namespace App\Actions\Submission;

use App\Enums\Locale;
use App\Enums\RegistrationStatus;
use App\Models\ConferenceEdition;
use App\Models\ContributorAffiliation;
use App\Models\Registration;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\SubmissionFile;
use App\Models\User;
use App\Support\Scholarly\OrcidNormalizer;
use App\Support\Submission\SubmissionReadinessPolicy;
use App\Support\Submission\SubmissionReadinessResult;
use DomainException;

final class EvaluateDraftSubmissionReadinessAction
{
    /**
     * Authoritative read-only evaluation for the author of an existing DRAFT.
     * No locks, transactions, logging, storage reads, or mutations are performed.
     * P03-H must independently re-evaluate in its submit-time transaction.
     */
    public function handle(User $actor, Submission $submission): SubmissionReadinessResult
    {
        $draft = Submission::query()->findOrFail($submission->id);
        $this->assertAccess($actor, $draft);

        $findings = [];
        $edition = ConferenceEdition::query()->findOrFail($draft->edition_id);
        $policy = SubmissionReadinessPolicy::fromEditionSettings($edition->settings_json);

        if ($policy === null) {
            $this->add($findings, 'EDITION_POLICY_UNCONFIGURED', 'BLOCKED', 'edition');
        }

        $this->inspectScholarlyText($draft, $policy, $findings);
        $this->inspectTrack($draft, $edition, $policy, $findings);
        $this->inspectContributors($draft, $findings);
        $this->inspectAbstractFile($draft, $policy, $findings);
        $this->inspectReferences($draft, $findings);

        return SubmissionReadinessResult::fromFindings($findings);
    }

    private function assertAccess(User $actor, Submission $draft): void
    {
        if ($draft->academic_status !== 'DRAFT') {
            throw new DomainException('Only a DRAFT Submission can be evaluated.');
        }

        $registration = Registration::query()->findOrFail($draft->registration_id);
        $membership = $registration->membership()->first();
        $package = $registration->package()->first();

        if (! $membership
            || $membership->edition_id !== $draft->edition_id
            || $membership->user_id !== $actor->id
            || $registration->status === RegistrationStatus::CANCELLED
            || ! $package
            || $package->edition_id !== $draft->edition_id) {
            throw new DomainException('This Submission does not have an authorized Edition participation context.');
        }

        // Intentionally no fee, payment, Registration CONFIRMED, or package.active guard.
    }

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    private function inspectScholarlyText(
        Submission $draft,
        ?SubmissionReadinessPolicy $policy,
        array &$findings,
    ): void {
        $locale = (string) $draft->getRawOriginal('primary_locale');
        if (! in_array($locale, Locale::values(), true)) {
            $this->add($findings, 'PRIMARY_LOCALE_INVALID', 'BLOCKED', 'primary_locale');

            return;
        }

        $translation = $draft->translations()->where('locale', $locale)->first();

        if ($translation === null || trim((string) $translation->title) === '') {
            $this->add($findings, 'PRIMARY_TITLE_MISSING', 'BLOCKED', 'translations.'.$locale.'.title');
        }

        if ($translation === null || trim((string) $translation->abstract_text) === '') {
            $this->add($findings, 'PRIMARY_ABSTRACT_MISSING', 'BLOCKED', 'translations.'.$locale.'.abstract');
        }

        $keywords = $draft->keywords()->where('locale', $locale)->orderBy('sequence')->get();
        $count = $keywords->count();

        if ($policy !== null && ($count < $policy->minKeywords || $count > $policy->maxKeywords)) {
            $this->add($findings, 'KEYWORDS_COUNT_INVALID', 'BLOCKED', 'keywords.'.$locale, [
                'min' => $policy->minKeywords,
                'max' => $policy->maxKeywords,
                'actual' => $count,
            ]);
        }

        $seen = [];
        $sequenceInvalid = false;
        $duplicates = false;
        $blank = false;

        foreach ($keywords as $index => $keyword) {
            if ((int) $keyword->sequence !== $index + 1) {
                $sequenceInvalid = true;
            }

            $value = trim((string) $keyword->value);
            if ($value === '') {
                $blank = true;

                continue;
            }

            $normalized = mb_strtolower($value, 'UTF-8');
            if (isset($seen[$normalized])) {
                $duplicates = true;
            }
            $seen[$normalized] = true;
        }

        if ($sequenceInvalid) {
            $this->add($findings, 'KEYWORDS_SEQUENCE_INVALID', 'BLOCKED', 'keywords.'.$locale);
        }
        if ($duplicates) {
            $this->add($findings, 'KEYWORDS_DUPLICATE', 'BLOCKED', 'keywords.'.$locale);
        }
        if ($blank) {
            $this->add($findings, 'KEYWORDS_VALUE_INVALID', 'BLOCKED', 'keywords.'.$locale);
        }

        $translationCount = $draft->translations()->whereIn('locale', Locale::values())->count();
        if ($translationCount < count(Locale::values())) {
            $this->add($findings, 'OPTIONAL_TRANSLATIONS_MISSING', 'WARNING', 'translations', [
                'missing_count' => count(Locale::values()) - $translationCount,
            ]);
        }
    }

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    private function inspectTrack(
        Submission $draft,
        ConferenceEdition $edition,
        ?SubmissionReadinessPolicy $policy,
        array &$findings,
    ): void {
        if ($policy !== null && $policy->trackRequired && ! $edition->tracks()->where('active', true)->exists()) {
            $this->add($findings, 'EDITION_TRACKS_UNAVAILABLE', 'BLOCKED', 'edition.tracks');
        }

        if ($draft->track_id === null) {
            if ($policy !== null && $policy->trackRequired) {
                $this->add($findings, 'TRACK_REQUIRED', 'BLOCKED', 'track_id');
            }

            return;
        }

        $valid = $edition->tracks()->whereKey($draft->track_id)->where('active', true)->exists();
        if (! $valid) {
            $this->add($findings, 'TRACK_INVALID', 'BLOCKED', 'track_id');
        }
    }

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    private function inspectContributors(Submission $draft, array &$findings): void
    {
        $contributors = $draft->contributors()->with('affiliations')->get();
        if ($contributors->isEmpty()) {
            $this->add($findings, 'CONTRIBUTORS_MISSING', 'BLOCKED', 'contributors');

            return;
        }

        $corresponding = 0;
        foreach ($contributors as $index => $contributor) {
            $field = 'contributors.'.$index;
            if ((int) $contributor->sequence !== $index + 1) {
                $this->add($findings, 'CONTRIBUTOR_ORDER_INVALID', 'BLOCKED', 'contributors');
            }
            if ($contributor->is_corresponding) {
                $corresponding++;
            }
            $this->inspectContributor($contributor, $field, $findings);
        }

        if ($corresponding !== 1) {
            $this->add($findings, 'CORRESPONDING_AUTHOR_INVALID', 'BLOCKED', 'contributors', [
                'actual' => $corresponding,
            ]);
        }
    }

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    private function inspectContributor(SubmissionContributor $contributor, string $field, array &$findings): void
    {
        $name = trim((string) $contributor->display_name);
        if ($name === '' || (! $contributor->single_name
            && trim((string) $contributor->given_name) === ''
            && trim((string) $contributor->family_name) === '')) {
            $this->add($findings, 'CONTRIBUTOR_NAME_INVALID', 'BLOCKED', $field.'.name');
        }

        if (filter_var((string) $contributor->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->add($findings, 'CONTRIBUTOR_EMAIL_INVALID', 'BLOCKED', $field.'.email');
        }

        if (preg_match('/^[A-Z]{2}$/', (string) $contributor->country_code) !== 1) {
            $this->add($findings, 'CONTRIBUTOR_COUNTRY_INVALID', 'BLOCKED', $field.'.country_code');
        }

        if ($contributor->orcid_uri === null || trim($contributor->orcid_uri) === '') {
            $this->add($findings, 'CONTRIBUTOR_ORCID_MISSING', 'WARNING', $field.'.orcid');
        } else {
            try {
                OrcidNormalizer::normalize($contributor->orcid_uri);
            } catch (DomainException) {
                $this->add($findings, 'CONTRIBUTOR_ORCID_INVALID', 'BLOCKED', $field.'.orcid');
            }
        }

        $affiliations = $contributor->affiliations;
        if ($affiliations->isEmpty()) {
            $this->add($findings, 'CONTRIBUTOR_AFFILIATION_MISSING', 'BLOCKED', $field.'.affiliations');

            return;
        }

        foreach ($affiliations as $index => $affiliation) {
            $this->inspectAffiliation($affiliation, $field.'.affiliations.'.$index, $index + 1, $findings);
        }
    }

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    private function inspectAffiliation(
        ContributorAffiliation $affiliation,
        string $field,
        int $expectedSequence,
        array &$findings,
    ): void {
        if ((int) $affiliation->sequence !== $expectedSequence) {
            $this->add($findings, 'AFFILIATION_ORDER_INVALID', 'BLOCKED', $field);
        }

        if (trim((string) $affiliation->institution_name_snapshot) === '') {
            $this->add($findings, 'CONTRIBUTOR_AFFILIATION_INVALID', 'BLOCKED', $field);
        }

        if ($affiliation->institution_id === null
            && preg_match('/^[A-Z]{2}$/', (string) $affiliation->country_code) !== 1) {
            $this->add($findings, 'CONTRIBUTOR_AFFILIATION_INVALID', 'BLOCKED', $field);
        }

        if ($affiliation->ror_uri_snapshot === null || trim($affiliation->ror_uri_snapshot) === '') {
            $this->add($findings, 'AFFILIATION_ROR_MISSING', 'WARNING', $field.'.ror');
        }
    }

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    private function inspectAbstractFile(
        Submission $draft,
        ?SubmissionReadinessPolicy $policy,
        array &$findings,
    ): void {
        if ($policy === null || ! $policy->abstractFileRequired) {
            return;
        }

        $files = $draft->files()->where('file_role', 'ABSTRACT_FILE')
            ->orderByDesc('manuscript_version')->with('storedFile')->get();
        $active = $files->filter(static fn (SubmissionFile $file): bool => $file->status === 'ACTIVE')->values();

        if ($active->count() === 0) {
            $this->add($findings, 'ABSTRACT_FILE_REQUIRED', 'BLOCKED', 'files.abstract');

            return;
        }

        if ($active->count() !== 1 || $active->first()?->id !== $files->first()?->id) {
            $this->add($findings, 'ABSTRACT_FILE_METADATA_INVALID', 'BLOCKED', 'files.abstract');

            return;
        }

        /** @var SubmissionFile $file */
        $file = $active->first();
        $stored = $file->storedFile;
        if (! $stored instanceof StoredFile || (int) $file->manuscript_version < 1
            || $stored->disk !== 'private' || $stored->visibility_class !== 'PRIVATE'
            || trim((string) $stored->path) === '' || (int) $stored->size_bytes < 1
            || preg_match('/^[a-f0-9]{64}$/i', (string) $stored->checksum_sha256) !== 1
            || ! in_array($stored->mime_type, [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ], true)) {
            $this->add($findings, 'ABSTRACT_FILE_METADATA_INVALID', 'BLOCKED', 'files.abstract');
        }
    }

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    private function inspectReferences(Submission $draft, array &$findings): void
    {
        $missingDoi = $draft->references()->whereNull('doi')->count();
        if ($missingDoi > 0) {
            $this->add($findings, 'REFERENCE_DOI_MISSING', 'WARNING', 'references', ['actual' => $missingDoi]);
        }
    }

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     * @param  array<string, int>  $context
     */
    private function add(
        array &$findings,
        string $code,
        string $severity,
        string $field,
        array $context = [],
    ): void {
        $findings[] = compact('code', 'severity', 'field', 'context');
    }
}
