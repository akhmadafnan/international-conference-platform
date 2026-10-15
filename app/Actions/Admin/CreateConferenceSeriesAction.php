<?php

namespace App\Actions\Admin;

use App\Models\ConferenceSeries;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

final class CreateConferenceSeriesAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $actor, array $data): ConferenceSeries
    {
        if (! $actor->isSuperAdmin()) {
            throw new AuthorizationException;
        }

        return DB::transaction(function () use ($actor, $data): ConferenceSeries {
            $series = ConferenceSeries::query()->create([
                'code' => $data['code'],
                'name' => $data['name'],
                'acronym' => $data['acronym'] ?? null,
                'about_i18n' => $data['about_i18n'] ?? null,
                'status' => 'ACTIVE',
            ]);

            activity('conference')
                ->causedBy($actor)
                ->performedOn($series)
                ->event('conference_series_created')
                ->withProperties([
                    'code' => $series->code,
                    'name' => $series->name,
                    'acronym' => $series->acronym,
                    'about_i18n' => $series->about_i18n,
                    'status' => $series->status,
                ])
                ->log('Conference Series created');

            return $series;
        });
    }
}
