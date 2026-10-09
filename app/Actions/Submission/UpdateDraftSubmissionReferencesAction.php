<?php

namespace App\Actions\Submission;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\SubmissionReference;
use App\Models\User;
use App\Support\Scholarly\DoiNormalizer;
use DomainException;
use Illuminate\Support\Facades\DB;

class UpdateDraftSubmissionReferencesAction
{
    /**
     * Maximum stored length of the `submission_references.url` string column.
     */
    private const URL_MAX_LENGTH = 255;

    /**
     * Maximum stored byte length of the `submission_references.raw_citation` text column.
     */
    private const CITATION_MAX_BYTES = 65535;

    /**
     * The client-writable reference payload keys.
     *
     * @var list<string>
     */
    private const ENTRY_KEYS = ['id', 'raw_citation', 'doi', 'url'];

    /**
     * Replace the complete References list of an existing DRAFT Submission.
     *
     * The payload is a full replacement list: references omitted from the list are
     * deleted, retained references keep their UUID, and new references are created
     * with a server-generated UUIDv7. Server-owned fields are rejected instead of
     * silently ignored.
     *
     * The raw citation stays the canonical source evidence; DOI and URL are optional.
     * Structured enrichment is never client-writable and is invalidated whenever the
     * citation, DOI, or URL meaningfully changes, so no stale enrichment survives.
     *
     * Lock ordering: submissions → registrations → submission_references.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handle(User $actor, Submission $submission, array $payload): Submission
    {
        $references = $this->normalizePayload($payload);

        return DB::transaction(function () use ($actor, $submission, $references): Submission {
            $authoritative = Submission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($authoritative->academic_status !== 'DRAFT') {
                throw new DomainException(
                    'Only a DRAFT Submission may have its References metadata edited.',
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

            $existingById = SubmissionReference::query()
                ->where('submission_id', $authoritative->id)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $maximumSequence = (int) $existingById->max('sequence');

            $seenIds = [];

            /** @var array<int, SubmissionReference> $modelsByIndex */
            $modelsByIndex = [];

            foreach ($references as $index => $reference) {
                if ($reference['id'] === null) {
                    continue;
                }

                $model = $existingById->get($reference['id']);

                if (! $model instanceof SubmissionReference) {
                    throw new DomainException(
                        'The supplied reference identifier does not belong to this Submission.',
                    );
                }

                if (array_key_exists($reference['id'], $seenIds)) {
                    throw new DomainException(
                        'The reference payload repeats an existing reference identifier.',
                    );
                }

                $seenIds[$reference['id']] = true;
                $modelsByIndex[$index] = $model;
            }

            $omittedIds = [];

            foreach ($existingById->all() as $referenceId => $existingReference) {
                if (! array_key_exists($referenceId, $seenIds)) {
                    $omittedIds[] = $existingReference->id;
                }
            }

            if ($omittedIds !== []) {
                SubmissionReference::query()
                    ->whereIn('id', $omittedIds)
                    ->delete();
            }

            $temporaryBase = max($maximumSequence, count($references));

            foreach ($references as $index => $reference) {
                $temporarySequence = $temporaryBase + $index + 1;

                if ($reference['id'] === null) {
                    $modelsByIndex[$index] = SubmissionReference::query()->create([
                        'submission_id' => $authoritative->id,
                        'sequence' => $temporarySequence,
                        'raw_citation' => $reference['raw_citation'],
                        'doi' => $reference['doi'],
                        'url' => $reference['url'],
                    ]);

                    continue;
                }

                $modelsByIndex[$index]->update([
                    'sequence' => $temporarySequence,
                ]);
            }

            $finalIds = [];

            foreach ($references as $index => $reference) {
                $model = $modelsByIndex[$index];

                $attributes = [
                    'raw_citation' => $reference['raw_citation'],
                    'doi' => $reference['doi'],
                    'url' => $reference['url'],
                    'sequence' => $index + 1,
                ];

                if ($reference['id'] === null || ! $this->sourceUnchanged($model, $reference)) {
                    $attributes['structured_json'] = null;
                }

                $model->update($attributes);

                $finalIds[] = $model->id;
            }

            activity('submission')
                ->causedBy($actor)
                ->performedOn($authoritative)
                ->event('submission_references_updated')
                ->withProperties([
                    'conference_edition_id' => $authoritative->edition_id,
                    'registration_id' => $authoritative->registration_id,
                    'paper_code' => $authoritative->paper_code,
                    'reference_count' => count($references),
                    'reference_ids' => $finalIds,
                ])
                ->log('Draft submission references updated');

            return $authoritative->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return list<array{id: string|null, raw_citation: string, doi: string|null, url: string|null}>
     */
    private function normalizePayload(array $payload): array
    {
        foreach (array_keys($payload) as $key) {
            if ($key !== 'references') {
                throw new DomainException(
                    "The reference mutation payload must not contain the [{$key}] field.",
                );
            }
        }

        if (! array_key_exists('references', $payload)) {
            throw new DomainException(
                'The reference mutation payload requires the [references] key.',
            );
        }

        $references = $payload['references'];

        if (! is_array($references) || ! array_is_list($references)) {
            throw new DomainException(
                'The references payload must be a list.',
            );
        }

        $normalized = [];

        foreach ($references as $reference) {
            $normalized[] = $this->normalizeReference($reference);
        }

        return $normalized;
    }

    /**
     * @return array{id: string|null, raw_citation: string, doi: string|null, url: string|null}
     */
    private function normalizeReference(mixed $reference): array
    {
        if (! is_array($reference)) {
            throw new DomainException(
                'Each reference payload entry must be an array.',
            );
        }

        foreach (array_keys($reference) as $key) {
            if (! is_string($key)) {
                throw new DomainException(
                    'The reference payload keys must be strings.',
                );
            }

            if (! in_array($key, self::ENTRY_KEYS, true)) {
                throw new DomainException(
                    "The reference payload must not contain the [{$key}] field.",
                );
            }
        }

        if (! array_key_exists('raw_citation', $reference)) {
            throw new DomainException(
                'The reference payload requires the [raw_citation] key.',
            );
        }

        $id = null;

        if (array_key_exists('id', $reference) && $reference['id'] !== null) {
            if (! is_string($reference['id'])) {
                throw new DomainException(
                    'The reference [id] must be a string or null.',
                );
            }

            $id = trim($reference['id']);

            if ($id === '') {
                throw new DomainException(
                    'The reference [id] must be a non-blank string or null.',
                );
            }
        }

        return [
            'id' => $id,
            'raw_citation' => $this->normalizeCitation($reference['raw_citation']),
            'doi' => $this->normalizeDoi($reference['doi'] ?? null),
            'url' => $this->normalizeUrl($reference['url'] ?? null),
        ];
    }

    private function normalizeCitation(mixed $value): string
    {
        if (! is_string($value)) {
            throw new DomainException(
                'The reference [raw_citation] must be a string.',
            );
        }

        $value = trim($value);

        if ($value === '') {
            throw new DomainException(
                'The reference [raw_citation] must not be blank.',
            );
        }

        if (strlen($value) > self::CITATION_MAX_BYTES) {
            throw new DomainException(
                'The reference [raw_citation] exceeds the maximum storable length.',
            );
        }

        return $value;
    }

    private function normalizeDoi(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new DomainException(
                'The reference [doi] must be a string or null.',
            );
        }

        return DoiNormalizer::normalize($value);
    }

    private function normalizeUrl(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new DomainException(
                'The reference [url] must be a string or null.',
            );
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (strlen($value) > self::URL_MAX_LENGTH) {
            throw new DomainException(
                'The reference [url] exceeds the maximum storable length.',
            );
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new DomainException(
                'The reference [url] must be an absolute HTTP(S) URL.',
            );
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if (! is_string($scheme) || ! in_array(strtolower($scheme), ['http', 'https'], true)) {
            throw new DomainException(
                'The reference [url] must be an absolute HTTP(S) URL.',
            );
        }

        return $value;
    }

    /**
     * @param  array{id: string|null, raw_citation: string, doi: string|null, url: string|null}  $reference
     */
    private function sourceUnchanged(SubmissionReference $model, array $reference): bool
    {
        return $model->raw_citation === $reference['raw_citation']
            && $model->doi === $reference['doi']
            && $model->url === $reference['url'];
    }
}
