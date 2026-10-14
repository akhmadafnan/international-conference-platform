<?php

declare(strict_types=1);

namespace App\Support\Submission;

use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\SubmissionFile;
use DomainException;
use JsonException;

/**
 * Private immutable evidence of initial official submission.
 * Not a public API projection. Never embed file storage paths or binary content.
 */
final class InitialSubmissionSnapshotBuilder
{
    /** @return array<string, mixed> */
    public function build(Submission $submission): array
    {
        $edition = $submission->edition()->firstOrFail();
        $track = $submission->track()->first();

        $translations = $submission->translations()
            ->orderBy('locale')->get()
            ->map(fn ($row): array => [
                'locale' => $row->locale,
                'title' => $row->title,
                'subtitle' => $row->subtitle,
                'abstract' => $row->abstract_text,
            ])->all();

        $keywords = $submission->keywords()
            ->orderBy('locale')->orderBy('sequence')->get()
            ->map(fn ($row): array => [
                'locale' => $row->locale,
                'sequence' => (int) $row->sequence,
                'value' => $row->value,
            ])->all();

        $contributors = [];
        foreach ($submission->contributors()->with('affiliations')->get() as $row) {
            $affiliations = [];
            foreach ($row->affiliations as $affiliation) {
                $affiliations[] = [
                    'id' => $affiliation->id,
                    'sequence' => (int) $affiliation->sequence,
                    'institution_id' => $affiliation->institution_id,
                    'institution_name' => $affiliation->institution_name_snapshot,
                    'ror_uri' => $affiliation->ror_uri_snapshot,
                    'subdivision' => $affiliation->subdivision_text,
                    'city' => $affiliation->city_text,
                    'country_code' => $affiliation->country_code,
                ];
            }

            $contributors[] = [
                'id' => $row->id,
                'sequence' => (int) $row->sequence,
                'display_name' => $row->display_name,
                'given_name' => $row->given_name,
                'family_name' => $row->family_name,
                'single_name' => (bool) $row->single_name,
                'email' => $row->email,
                'country_code' => $row->country_code,
                'orcid_uri' => $row->orcid_uri,
                'orcid_verification_state' => $row->orcid_verification_state,
                'is_corresponding' => (bool) $row->is_corresponding,
                'contributor_role' => $row->contributor_role,
                'affiliations' => $affiliations,
            ];
        }
        $references = $submission->references()->orderBy('sequence')->get()
            ->map(fn ($row): array => [
                'id' => $row->id,
                'sequence' => (int) $row->sequence,
                'raw_citation' => $row->raw_citation,
                'doi' => $row->doi,
                'url' => $row->url,
                'structured' => $row->structured_json,
            ])->all();

        return [
            'schema' => 'iches.initial_submission.v1',
            'submission_id' => $submission->id,
            'edition_id' => $submission->edition_id,
            'edition' => [
                'code' => $edition->edition_code,
                'year' => (int) $edition->year,
                'host_name' => $edition->host_name,
            ],
            'registration_id' => $submission->registration_id,
            'paper_code' => $submission->paper_code,
            'primary_locale' => (string) $submission->getRawOriginal('primary_locale'),
            'track_id' => $submission->track_id,
            'track' => $track === null ? null : [
                'code' => $track->code,
                'name_i18n' => $track->name_i18n,
            ],
            'abstract_version' => 1,
            'translations' => $translations,
            'keywords' => $keywords,
            'contributors' => $contributors,
            'references' => $references,
            'abstract_file' => $this->currentAbstractFile($submission),
        ];
    }

    /**
     * Checksum uses sorted associative keys recursively, preserving list order.
     * MySQL may reorder JSON object keys; hash of canonical loaded JSON is stable.
     *
     * @param  array<string, mixed>  $metadata
     *
     * @throws JsonException
     */
    public function checksum(array $metadata): string
    {
        $json = json_encode(
            $this->canonicalize($metadata),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );

        return hash('sha256', $json);
    }

    /** @return array<string, mixed>|null */
    private function currentAbstractFile(Submission $submission): ?array
    {
        $files = $submission->files()
            ->where('file_role', 'ABSTRACT_FILE')
            ->orderByDesc('manuscript_version')
            ->with('storedFile')->get();

        if ($files->isEmpty()) {
            return null;
        }

        $active = $files->filter(fn (SubmissionFile $file): bool => $file->status === 'ACTIVE')->values();
        $selected = $active->first();
        if ($active->count() !== 1 || $selected?->id !== $files->first()->id) {
            throw new DomainException('The abstract file version lineage is not valid for official evidence.');
        }

        $stored = $selected->storedFile;
        if (! $stored instanceof StoredFile
            || (int) $selected->manuscript_version < 1
            || $stored->disk !== 'private'
            || $stored->visibility_class !== 'PRIVATE'
            || (int) $stored->size_bytes < 1
            || preg_match('/^[a-f0-9]{64}$/i', (string) $stored->checksum_sha256) !== 1
            || ! in_array($stored->mime_type, [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ], true)) {
            throw new DomainException('Abstract file metadata is invalid for initial submission evidence.');
        }

        return [
            'submission_file_id' => $selected->id,
            'stored_file_id' => $stored->id,
            'manuscript_version' => (int) $selected->manuscript_version,
            'mime_type' => $stored->mime_type,
            'size_bytes' => (int) $stored->size_bytes,
            'sha256' => $stored->checksum_sha256,
        ];
    }

    private function canonicalize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (! array_is_list($value)) {
            ksort($value, SORT_STRING);
        }

        foreach ($value as $key => $child) {
            $value[$key] = $this->canonicalize($child);
        }

        return $value;
    }
}
