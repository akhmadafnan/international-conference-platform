<?php

namespace App\Actions\Admin;

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateConferenceEditionAction
{
    /** @param array<string, mixed> $data */
    public function handle(User $actor, array $data): ConferenceEdition
    {
        if (! $actor->isSuperAdmin()) {
            throw new AuthorizationException;
        }

        return DB::transaction(function () use ($actor, $data): ConferenceEdition {
            $seriesId = (string) $data['series_id'];
            $series = ConferenceSeries::query()->lockForUpdate()->find($seriesId);
            if (! $series || $series->status !== 'ACTIVE') {
                throw ValidationException::withMessages(['series_id' => 'series']);
            }

            $edition = ConferenceEdition::query()->create([
                'series_id' => $series->id,
                'edition_code' => $data['edition_code'],
                'edition_number' => $data['edition_number'] ?? null,
                'year' => $data['year'],
                'host_name' => $data['host_name'],
                'mode' => $data['mode'],
                'timezone' => $data['timezone'],
                'starts_at' => CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $data['starts_at'], $data['timezone'])->utc(),
                'ends_at' => CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $data['ends_at'], $data['timezone'])->utc(),
                'theme_i18n' => $data['theme_i18n'] ?? null,
                'organizer_i18n' => $data['organizer_i18n'] ?? null,
                'lifecycle_status' => 'DRAFT',
            ]);

            activity('conference')
                ->causedBy($actor)
                ->performedOn($edition)
                ->event('conference_edition_created')
                ->withProperties([
                    'series_id' => $edition->series_id,
                    'edition_code' => $edition->edition_code,
                    'edition_number' => $edition->edition_number,
                    'year' => $edition->year,
                    'host_name' => $edition->host_name,
                    'mode' => $edition->mode,
                    'timezone' => $edition->timezone,
                    'starts_at' => $edition->starts_at->toIso8601String(),
                    'ends_at' => $edition->ends_at->toIso8601String(),
                    'theme_i18n' => $edition->theme_i18n,
                    'organizer_i18n' => $edition->organizer_i18n,
                    'lifecycle_status' => $edition->lifecycle_status,
                ])
                ->log('Conference Edition created');

            return $edition;
        });
    }
}
