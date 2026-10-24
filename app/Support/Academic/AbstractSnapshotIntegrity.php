<?php

namespace App\Support\Academic;

use App\Models\ConferenceEdition;
use App\Models\Submission;
use App\Models\SubmissionSnapshot;
use App\Support\Submission\InitialSubmissionSnapshotBuilder;

final class AbstractSnapshotIntegrity
{
    public const TYPES = ['INITIAL_SUBMISSION', 'ADMIN_CORRECTION', 'ABSTRACT_REVISION'];

    public function valid(SubmissionSnapshot $snapshot, Submission $submission): bool
    {
        $data = $snapshot->metadata_json;
        $locale = $submission->getRawOriginal('primary_locale');
        $translations = $data['translations'] ?? null;
        $primary = is_array($translations) ? collect($translations)->first(fn ($row) => is_array($row) && ($row['locale'] ?? null) === $locale) : null;

        return $snapshot->submission_id === $submission->id
            && $snapshot->version >= 1 && $snapshot->version === $submission->current_abstract_version
            && in_array($snapshot->snapshot_type, self::TYPES, true)
            && ($data['submission_id'] ?? null) === $submission->id
            && ($data['edition_id'] ?? null) === $submission->edition_id
            && ($data['registration_id'] ?? null) === $submission->registration_id
            && ($data['paper_code'] ?? null) === $submission->paper_code
            && ($data['primary_locale'] ?? null) === $locale
            && in_array($data['schema'] ?? null, ['iches.initial_submission.v1', 'iches.abstract_correction.v1'], true)
            && is_array($primary) && is_string($primary['title'] ?? null) && trim($primary['title']) !== ''
            && is_string($primary['abstract'] ?? null) && trim($primary['abstract']) !== ''
            && ($data['abstract_version'] ?? null) === $snapshot->version
            && hash_equals($snapshot->checksum, app(InitialSubmissionSnapshotBuilder::class)->checksum($data));
    }

    public function current(Submission $submission): ?SubmissionSnapshot
    {
        $snapshots = $submission->snapshots()->where('version', $submission->current_abstract_version)
            ->whereIn('snapshot_type', self::TYPES)->lockForUpdate()->get();

        if ($snapshots->count() !== 1) {
            return null;
        }
        $snapshot = $snapshots->sole();

        return $this->valid($snapshot, $submission) ? $snapshot : null;
    }

    /** @return list<string> */
    public function validSubmissionIds(ConferenceEdition $edition): array
    {
        // Validate immutable evidence internally; pool callers receive only Submission discovery columns.
        $groups = SubmissionSnapshot::query()->whereIn('snapshot_type', self::TYPES)
            ->whereHas('submission', fn ($q) => $q->where('edition_id', $edition->id)
                ->whereIn('academic_status', ['SUBMITTED', 'UNDER_REVIEW'])->whereColumn('submission_snapshots.version', 'submissions.current_abstract_version'))
            ->with('submission')->get()->groupBy('submission_id');
        $ids = [];
        foreach ($groups as $snapshots) {
            if ($snapshots->count() === 1) {
                $snapshot = $snapshots->sole();
                if ($this->valid($snapshot, $snapshot->submission)) {
                    $ids[] = $snapshot->submission_id;
                }
            }
        }

        return $ids;
    }
}
