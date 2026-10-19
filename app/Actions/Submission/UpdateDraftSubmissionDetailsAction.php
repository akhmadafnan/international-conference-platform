<?php

namespace App\Actions\Submission;

use App\Enums\Locale;
use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Track;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class UpdateDraftSubmissionDetailsAction
{
    /**
     * Replace the editable Details scholarly metadata of an existing DRAFT Submission.
     *
     * Lock ordering: submissions → registrations.
     *
     * @param  array<string, mixed>  $details
     */
    public function handle(User $actor, Submission $submission, array $details): Submission
    {
        $payload = $this->normalizeDetailsPayload($details);

        return DB::transaction(function () use ($actor, $submission, $payload): Submission {
            $authoritative = Submission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($authoritative->academic_status !== 'DRAFT') {
                throw new DomainException(
                    'Only a DRAFT Submission may have its Details metadata edited.',
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

            $trackId = $payload['track_id'];

            if ($trackId !== null) {
                $track = Track::query()->whereKey($trackId)->first();

                if (! $track) {
                    throw new DomainException(
                        'The supplied Track does not exist.',
                    );
                }

                if ($track->edition_id !== $authoritative->edition_id) {
                    throw new DomainException(
                        'The supplied Track does not belong to the Submission Conference Edition.',
                    );
                }

                if (! $track->active) {
                    throw new DomainException(
                        'The supplied Track is not active.',
                    );
                }
            }

            $authoritative->update([
                'primary_locale' => $payload['primary_locale'],
                'track_id' => $trackId,
            ]);

            $authoritative->translations()->delete();
            $authoritative->keywords()->delete();

            foreach ($payload['translations'] as $locale => $translation) {
                $authoritative->translations()->create([
                    'locale' => $locale,
                    'title' => $translation['title'],
                    'subtitle' => $translation['subtitle'],
                    'abstract_text' => $translation['abstract'],
                ]);
            }

            foreach ($payload['keywords'] as $locale => $keywords) {
                foreach ($keywords as $index => $keyword) {
                    $authoritative->keywords()->create([
                        'locale' => $locale,
                        'value' => $keyword,
                        'sequence' => $index + 1,
                    ]);
                }
            }

            activity('submission')
                ->causedBy($actor)
                ->performedOn($authoritative)
                ->event('submission_details_updated')
                ->withProperties([
                    'conference_edition_id' => $authoritative->edition_id,
                    'registration_id' => $authoritative->registration_id,
                    'paper_code' => $authoritative->paper_code,
                    'primary_locale' => $authoritative->getRawOriginal('primary_locale'),
                    'track_id' => $authoritative->track_id,
                    'scholarly_locales' => array_keys($payload['translations']),
                    'keyword_counts' => array_map('count', $payload['keywords']),
                ])
                ->log('Draft submission details updated');

            return $authoritative->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $details
     * @return array{
     *     primary_locale: string,
     *     track_id: string|null,
     *     translations: array<string, array{title: string, subtitle: string|null, abstract: string}>,
     *     keywords: array<string, list<string>>
     * }
     */
    private function normalizeDetailsPayload(array $details): array
    {
        foreach (['primary_locale', 'track_id', 'translations', 'keywords'] as $requiredKey) {
            if (! array_key_exists($requiredKey, $details)) {
                throw new DomainException(
                    "The Details payload requires the [{$requiredKey}] key.",
                );
            }
        }

        $primaryLocale = $details['primary_locale'];

        if (! is_string($primaryLocale) || ! in_array($primaryLocale, Locale::values(), true)) {
            throw new DomainException(
                'The primary scholarly locale must use a supported locale.',
            );
        }

        $trackId = $details['track_id'];

        if ($trackId !== null && ! is_string($trackId)) {
            throw new DomainException(
                'The Track identifier must be a string or null.',
            );
        }

        $translations = $details['translations'];

        if (! is_array($translations)) {
            throw new DomainException(
                'Scholarly translations must be an array keyed by scholarly locale.',
            );
        }

        $normalizedTranslations = [];

        foreach ($translations as $locale => $translation) {
            if (! is_string($locale) || ! in_array($locale, Locale::values(), true)) {
                throw new DomainException(
                    'Scholarly translations only support the id, en, and ar locales.',
                );
            }

            if (! is_array($translation)) {
                throw new DomainException(
                    "The [{$locale}] scholarly translation must be an array.",
                );
            }

            $normalizedTranslations[$locale] = [
                'title' => $this->normalizeRequiredTranslationText($translation, 'title', $locale),
                'subtitle' => $this->normalizeOptionalSubtitle($translation, $locale),
                'abstract' => $this->normalizeRequiredTranslationText($translation, 'abstract', $locale),
            ];
        }

        if (! array_key_exists($primaryLocale, $normalizedTranslations)) {
            throw new DomainException(
                'The Details payload requires a scholarly translation for the primary locale.',
            );
        }

        $keywords = $details['keywords'];

        if (! is_array($keywords)) {
            throw new DomainException(
                'Keywords must be an array keyed by scholarly locale.',
            );
        }

        $normalizedKeywords = [];

        foreach ($keywords as $locale => $values) {
            if (! is_string($locale) || ! in_array($locale, Locale::values(), true)) {
                throw new DomainException(
                    'Keywords only support the id, en, and ar locales.',
                );
            }

            if (! is_array($values)) {
                throw new DomainException(
                    "The [{$locale}] keyword list must be an array.",
                );
            }

            $normalizedValues = [];

            foreach (array_values($values) as $value) {
                if (! is_string($value)) {
                    throw new DomainException(
                        "The [{$locale}] keyword values must be strings.",
                    );
                }

                $value = trim($value);

                if ($value === '') {
                    throw new DomainException(
                        "The [{$locale}] keyword values must not be blank.",
                    );
                }

                $normalizedValues[] = $value;
            }

            $normalizedKeywords[$locale] = $normalizedValues;
        }

        return [
            'primary_locale' => $primaryLocale,
            'track_id' => $trackId,
            'translations' => $normalizedTranslations,
            'keywords' => $normalizedKeywords,
        ];
    }

    /**
     * @param  array<string, mixed>  $translation
     */
    private function normalizeRequiredTranslationText(array $translation, string $key, string $locale): string
    {
        if (! array_key_exists($key, $translation)) {
            throw new DomainException(
                "The [{$locale}] scholarly translation requires a [{$key}].",
            );
        }

        $value = $translation[$key];

        if (! is_string($value)) {
            throw new DomainException(
                "The [{$locale}] scholarly translation [{$key}] must be a string.",
            );
        }

        $value = trim($value);

        if ($value === '') {
            throw new DomainException(
                "The [{$locale}] scholarly translation [{$key}] must not be blank.",
            );
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $translation
     */
    private function normalizeOptionalSubtitle(array $translation, string $locale): ?string
    {
        if (! array_key_exists('subtitle', $translation)) {
            return null;
        }

        $value = $translation['subtitle'];

        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new DomainException(
                "The [{$locale}] scholarly translation [subtitle] must be a string or null.",
            );
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
