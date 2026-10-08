<?php

namespace App\Actions\Submission;

use App\Enums\OrcidVerificationState;
use App\Enums\RegistrationStatus;
use App\Models\ContributorAffiliation;
use App\Models\Institution;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\User;
use App\Support\Scholarly\OrcidNormalizer;
use DomainException;
use Illuminate\Support\Facades\DB;

class UpdateDraftSubmissionContributorsAction
{
    /**
     * Replace the complete Contributors + Affiliations payload of an existing DRAFT Submission.
     *
     * The payload is a full replacement list: contributors omitted from the list are removed,
     * retained contributors keep their UUID, and new contributors are created. Server-owned
     * fields are rejected instead of silently ignored.
     *
     * Lock ordering: submissions → registrations → submission_contributors.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handle(User $actor, Submission $submission, array $payload): Submission
    {
        $contributors = $this->normalizePayload($payload);

        return DB::transaction(function () use ($actor, $submission, $contributors): Submission {
            $authoritative = Submission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($authoritative->academic_status !== 'DRAFT') {
                throw new DomainException(
                    'Only a DRAFT Submission may have its Contributors metadata edited.',
                );
            }

            $registration = Registration::query()
                ->whereKey($authoritative->registration_id)
                ->lockForUpdate()
                ->firstOrFail();

            $membership = $registration->membership()->first();

            if (! $membership) {
                throw new DomainException(
                    'The Registration does not have an Edition participation context.',
                );
            }

            if ($membership->edition_id !== $authoritative->edition_id) {
                throw new DomainException(
                    'The Registration does not belong to the Submission Conference Edition.',
                );
            }

            if ($membership->user_id !== $actor->id) {
                throw new DomainException(
                    'The Submission is not owned by the acting user.',
                );
            }

            if ($registration->status === RegistrationStatus::CANCELLED) {
                throw new DomainException(
                    'A cancelled Registration is not a valid participation context.',
                );
            }

            $package = $registration->package()->first();

            if (! $package || $package->edition_id !== $authoritative->edition_id) {
                throw new DomainException(
                    'The Registration does not have a valid participation package context.',
                );
            }

            $existingById = SubmissionContributor::query()
                ->where('submission_id', $authoritative->id)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $maximumSequence = (int) $existingById->max('sequence');

            $seenIds = [];
            $modelsByIndex = [];

            foreach ($contributors as $index => $contributor) {
                if ($contributor['id'] === null) {
                    continue;
                }

                $model = $existingById->get($contributor['id']);

                if (! $model instanceof SubmissionContributor) {
                    throw new DomainException(
                        'The supplied contributor identifier does not belong to this Submission.',
                    );
                }

                if (array_key_exists($contributor['id'], $seenIds)) {
                    throw new DomainException(
                        'The contributor payload repeats an existing contributor identifier.',
                    );
                }

                $seenIds[$contributor['id']] = true;
                $modelsByIndex[$index] = $model;
            }

            $institutionIds = [];

            foreach ($contributors as $contributor) {
                foreach ($contributor['affiliations'] as $affiliation) {
                    if ($affiliation['institution_id'] !== null) {
                        $institutionIds[$affiliation['institution_id']] = true;
                    }
                }
            }

            $institutions = Institution::query()
                ->whereIn('id', array_keys($institutionIds))
                ->get()
                ->keyBy('id');

            foreach (array_keys($institutionIds) as $institutionId) {
                if (! $institutions->has($institutionId)) {
                    throw new DomainException(
                        'The supplied Institution does not exist.',
                    );
                }
            }

            $omittedIds = [];

            foreach ($existingById->all() as $contributorId => $existingContributor) {
                if (! array_key_exists($contributorId, $seenIds)) {
                    $omittedIds[] = $existingContributor->id;
                }
            }

            if ($omittedIds !== []) {
                ContributorAffiliation::query()
                    ->whereIn('submission_contributor_id', $omittedIds)
                    ->delete();

                SubmissionContributor::query()
                    ->whereIn('id', $omittedIds)
                    ->delete();
            }

            $temporaryBase = max($maximumSequence, count($contributors));

            foreach ($contributors as $index => $contributor) {
                $temporarySequence = $temporaryBase + $index + 1;

                if ($contributor['id'] === null) {
                    $modelsByIndex[$index] = SubmissionContributor::query()->create([
                        'submission_id' => $authoritative->id,
                        'linked_user_id' => null,
                        'display_name' => $contributor['display_name'],
                        'given_name' => $contributor['given_name'],
                        'family_name' => $contributor['family_name'],
                        'single_name' => $contributor['single_name'],
                        'email' => $contributor['email'],
                        'country_code' => $contributor['country_code'],
                        'orcid_uri' => $contributor['orcid_uri'],
                        'orcid_verification_state' => $contributor['orcid_verification_state'],
                        'is_corresponding' => $contributor['is_corresponding'],
                        'sequence' => $temporarySequence,
                        'contributor_role' => 'AUTHOR',
                    ]);

                    continue;
                }

                $modelsByIndex[$index]->update([
                    'sequence' => $temporarySequence,
                ]);
            }

            $finalIds = [];
            $affiliationCounts = [];
            $correspondingCount = 0;

            foreach ($contributors as $index => $contributor) {
                $model = $modelsByIndex[$index];

                $model->update([
                    'display_name' => $contributor['display_name'],
                    'given_name' => $contributor['given_name'],
                    'family_name' => $contributor['family_name'],
                    'single_name' => $contributor['single_name'],
                    'email' => $contributor['email'],
                    'country_code' => $contributor['country_code'],
                    'orcid_uri' => $contributor['orcid_uri'],
                    'orcid_verification_state' => $contributor['orcid_verification_state'],
                    'is_corresponding' => $contributor['is_corresponding'],
                    'sequence' => $index + 1,
                ]);

                ContributorAffiliation::query()
                    ->where('submission_contributor_id', $model->id)
                    ->delete();

                foreach ($contributor['affiliations'] as $affiliationIndex => $affiliation) {
                    $institution = $affiliation['institution_id'] === null
                        ? null
                        : $institutions->get($affiliation['institution_id']);

                    $institutionNameSnapshot = $institution === null
                        ? $affiliation['institution_name']
                        : $institution->preferred_name;

                    $model->affiliations()->create([
                        'institution_id' => $affiliation['institution_id'],
                        'institution_name_snapshot' => $institutionNameSnapshot,
                        'ror_uri_snapshot' => $institution?->ror_uri,
                        'subdivision_text' => $affiliation['subdivision'],
                        'country_code' => $institution === null
                            ? $affiliation['country_code']
                            : $institution->country_code,
                        'city_text' => $affiliation['city'],
                        'sequence' => $affiliationIndex + 1,
                    ]);
                }

                if ($contributor['is_corresponding']) {
                    $correspondingCount++;
                }

                $finalIds[] = $model->id;
                $affiliationCounts[] = count($contributor['affiliations']);
            }

            activity('submission')
                ->causedBy($actor)
                ->performedOn($authoritative)
                ->event('submission_contributors_updated')
                ->withProperties([
                    'conference_edition_id' => $authoritative->edition_id,
                    'registration_id' => $authoritative->registration_id,
                    'paper_code' => $authoritative->paper_code,
                    'contributor_count' => count($contributors),
                    'contributor_ids' => $finalIds,
                    'corresponding_count' => $correspondingCount,
                    'affiliation_counts' => $affiliationCounts,
                ])
                ->log('Draft submission contributors updated');

            return $authoritative->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return list<array{
     *     id: string|null,
     *     display_name: string,
     *     given_name: string|null,
     *     family_name: string|null,
     *     single_name: bool,
     *     email: string,
     *     country_code: string,
     *     orcid_uri: string|null,
     *     orcid_verification_state: string|null,
     *     is_corresponding: bool,
     *     affiliations: list<array{
     *         institution_id: string|null,
     *         institution_name: string|null,
     *         subdivision: string|null,
     *         city: string|null,
     *         country_code: string|null
     *     }>
     * }>
     */
    private function normalizePayload(array $payload): array
    {
        foreach (array_keys($payload) as $key) {
            if ($key !== 'contributors') {
                throw new DomainException(
                    "The contributor mutation payload must not contain the [{$key}] field.",
                );
            }
        }

        if (! array_key_exists('contributors', $payload)) {
            throw new DomainException(
                'The contributor mutation payload requires the [contributors] key.',
            );
        }

        $contributors = $payload['contributors'];

        if (! is_array($contributors)) {
            throw new DomainException(
                'The contributors payload must be a list.',
            );
        }

        if (! array_is_list($contributors)) {
            throw new DomainException(
                'The contributors payload must be a list.',
            );
        }

        $normalized = [];
        $correspondingCount = 0;

        foreach ($contributors as $contributor) {
            $normalizedContributor = $this->normalizeContributor($contributor);

            if ($normalizedContributor['is_corresponding']) {
                $correspondingCount++;
            }

            $normalized[] = $normalizedContributor;
        }

        if ($correspondingCount > 1) {
            throw new DomainException(
                'A DRAFT contributor payload may mark at most one corresponding contributor.',
            );
        }

        return $normalized;
    }

    /**
     * @return array{
     *     id: string|null,
     *     display_name: string,
     *     given_name: string|null,
     *     family_name: string|null,
     *     single_name: bool,
     *     email: string,
     *     country_code: string,
     *     orcid_uri: string|null,
     *     orcid_verification_state: string|null,
     *     is_corresponding: bool,
     *     affiliations: list<array{
     *         institution_id: string|null,
     *         institution_name: string|null,
     *         subdivision: string|null,
     *         city: string|null,
     *         country_code: string|null
     *     }>
     * }
     */
    private function normalizeContributor(mixed $contributor): array
    {
        if (! is_array($contributor)) {
            throw new DomainException(
                'Each contributor payload entry must be an array.',
            );
        }

        foreach (array_keys($contributor) as $key) {
            if (! is_string($key)) {
                throw new DomainException(
                    'The contributor payload keys must be strings.',
                );
            }

            if (! in_array($key, ['id', 'display_name', 'given_name', 'family_name', 'single_name', 'email', 'country_code', 'orcid', 'is_corresponding', 'affiliations'], true)) {
                throw new DomainException(
                    "The contributor payload must not contain the [{$key}] field.",
                );
            }
        }

        foreach (['display_name', 'single_name', 'email', 'country_code', 'is_corresponding', 'affiliations'] as $requiredKey) {
            if (! array_key_exists($requiredKey, $contributor)) {
                throw new DomainException(
                    "The contributor payload requires the [{$requiredKey}] key.",
                );
            }
        }

        $id = null;

        if (array_key_exists('id', $contributor) && $contributor['id'] !== null) {
            if (! is_string($contributor['id'])) {
                throw new DomainException(
                    'The contributor [id] must be a string or null.',
                );
            }

            $id = trim($contributor['id']);

            if ($id === '') {
                throw new DomainException(
                    'The contributor [id] must be a non-blank string or null.',
                );
            }
        }

        if (! is_bool($contributor['single_name'])) {
            throw new DomainException(
                'The contributor [single_name] must be a boolean.',
            );
        }

        if (! is_bool($contributor['is_corresponding'])) {
            throw new DomainException(
                'The contributor [is_corresponding] must be a boolean.',
            );
        }

        $singleName = $contributor['single_name'];
        $givenName = $this->normalizeOptionalText($contributor['given_name'] ?? null, 'contributor [given_name]');
        $familyName = $this->normalizeOptionalText($contributor['family_name'] ?? null, 'contributor [family_name]');

        if ($singleName) {
            if ($givenName !== null || $familyName !== null) {
                throw new DomainException(
                    'A single-name contributor must keep [given_name] and [family_name] null.',
                );
            }
        } elseif ($givenName === null && $familyName === null) {
            throw new DomainException(
                'A contributor with [single_name] false must supply at least one of [given_name] or [family_name].',
            );
        }

        $email = $this->normalizeRequiredText($contributor['email'], 'contributor [email]');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException(
                'The contributor [email] must be a valid email address.',
            );
        }

        $orcid = null;

        if (array_key_exists('orcid', $contributor) && $contributor['orcid'] !== null) {
            if (! is_string($contributor['orcid'])) {
                throw new DomainException(
                    'The contributor [orcid] must be a string or null.',
                );
            }

            $orcid = $contributor['orcid'];
        }

        $orcidUri = OrcidNormalizer::normalize($orcid);

        $affiliations = $contributor['affiliations'];

        if (! is_array($affiliations) || ! array_is_list($affiliations)) {
            throw new DomainException(
                'The contributor [affiliations] payload must be a list.',
            );
        }

        $normalizedAffiliations = [];

        foreach ($affiliations as $affiliation) {
            $normalizedAffiliations[] = $this->normalizeAffiliation($affiliation);
        }

        return [
            'id' => $id,
            'display_name' => $this->normalizeRequiredText($contributor['display_name'], 'contributor [display_name]'),
            'given_name' => $givenName,
            'family_name' => $familyName,
            'single_name' => $singleName,
            'email' => $email,
            'country_code' => $this->normalizeCountryCode($contributor['country_code'], 'contributor [country_code]'),
            'orcid_uri' => $orcidUri,
            'orcid_verification_state' => $orcidUri === null
                ? null
                : OrcidVerificationState::UNVERIFIED->value,
            'is_corresponding' => $contributor['is_corresponding'],
            'affiliations' => $normalizedAffiliations,
        ];
    }

    /**
     * @return array{
     *     institution_id: string|null,
     *     institution_name: string|null,
     *     subdivision: string|null,
     *     city: string|null,
     *     country_code: string|null
     * }
     */
    private function normalizeAffiliation(mixed $affiliation): array
    {
        if (! is_array($affiliation)) {
            throw new DomainException(
                'Each affiliation payload entry must be an array.',
            );
        }

        foreach (array_keys($affiliation) as $key) {
            if (! is_string($key)) {
                throw new DomainException(
                    'The affiliation payload keys must be strings.',
                );
            }

            if (! in_array($key, ['institution_id', 'institution_name', 'subdivision', 'city', 'country_code'], true)) {
                throw new DomainException(
                    "The affiliation payload must not contain the [{$key}] field.",
                );
            }
        }

        $institutionId = null;

        if (array_key_exists('institution_id', $affiliation) && $affiliation['institution_id'] !== null) {
            if (! is_string($affiliation['institution_id'])) {
                throw new DomainException(
                    'The affiliation [institution_id] must be a string or null.',
                );
            }

            $institutionId = trim($affiliation['institution_id']);

            if ($institutionId === '') {
                throw new DomainException(
                    'The affiliation [institution_id] must be a non-blank string or null.',
                );
            }
        }

        $institutionName = null;
        $countryCode = null;

        if ($institutionId === null) {
            if (! array_key_exists('institution_name', $affiliation)) {
                throw new DomainException(
                    'A manual affiliation payload requires the [institution_name] key.',
                );
            }

            if (! array_key_exists('country_code', $affiliation)) {
                throw new DomainException(
                    'A manual affiliation payload requires the [country_code] key.',
                );
            }

            $institutionName = $this->normalizeRequiredText(
                $affiliation['institution_name'],
                'affiliation [institution_name]',
            );

            $countryCode = $this->normalizeCountryCode(
                $affiliation['country_code'],
                'affiliation [country_code]',
            );
        }

        return [
            'institution_id' => $institutionId,
            'institution_name' => $institutionName,
            'subdivision' => $this->normalizeOptionalText($affiliation['subdivision'] ?? null, 'affiliation [subdivision]'),
            'city' => $this->normalizeOptionalText($affiliation['city'] ?? null, 'affiliation [city]'),
            'country_code' => $countryCode,
        ];
    }

    private function normalizeRequiredText(mixed $value, string $label): string
    {
        if (! is_string($value)) {
            throw new DomainException(
                "The {$label} must be a string.",
            );
        }

        $value = trim($value);

        if ($value === '') {
            throw new DomainException(
                "The {$label} must not be blank.",
            );
        }

        return $value;
    }

    private function normalizeOptionalText(mixed $value, string $label): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new DomainException(
                "The {$label} must be a string or null.",
            );
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function normalizeCountryCode(mixed $value, string $label): string
    {
        $value = $this->normalizeRequiredText($value, $label);

        if (preg_match('/^[A-Za-z]{2}$/', $value) !== 1) {
            throw new DomainException(
                "The {$label} must be an uppercase two-letter country code.",
            );
        }

        return strtoupper($value);
    }
}
