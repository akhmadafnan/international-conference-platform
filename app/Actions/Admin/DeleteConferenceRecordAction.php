<?php

namespace App\Actions\Admin;

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class DeleteConferenceRecordAction
{
    public function handle(User $actor, ConferenceSeries|ConferenceEdition $record, string $confirmationCode): void
    {
        if (! $actor->isSuperAdmin()) {
            throw new AuthorizationException;
        }

        try {
            DB::transaction(function () use ($actor, $record, $confirmationCode): void {
                // Re-read and lock the record: never trust the route-bound snapshot.
                $locked = $record instanceof ConferenceSeries
                    ? ConferenceSeries::query()->lockForUpdate()->findOrFail((string) $record->getKey())
                    : ConferenceEdition::query()->lockForUpdate()->findOrFail((string) $record->getKey());
                $isSeries = $locked instanceof ConferenceSeries;
                $code = (string) $locked->getAttribute($isSeries ? 'code' : 'edition_code');

                if ($confirmationCode !== $code) {
                    throw ValidationException::withMessages(['confirmation_code' => 'confirmation']);
                }

                if ($locked instanceof ConferenceSeries) {
                    if ($locked->editions()->exists()) {
                        throw ValidationException::withMessages(['delete' => 'seriesHasEditions']);
                    }
                } else {
                    if ($locked->lifecycle_status !== 'DRAFT') {
                        throw ValidationException::withMessages(['delete' => 'editionNotDraft']);
                    }
                    if ($this->editionHasRelatedData((string) $locked->getKey())) {
                        throw ValidationException::withMessages(['delete' => 'editionHasData']);
                    }
                }

                $id = (string) $locked->getKey();
                // Delete this model only. Database RESTRICT remains the final
                // guard against concurrent inserts and unanticipated references.
                if (! $locked->delete()) {
                    throw ValidationException::withMessages(['delete' => 'failed']);
                }

                // Retain the deleted identity in both polymorphic subject fields
                // and properties; deleting the model never deletes audit history.
                activity('conference')
                    ->causedBy($actor)
                    ->performedOn($locked)
                    ->event($isSeries ? 'conference_series_deleted' : 'conference_edition_deleted')
                    ->withProperties([
                        'id' => $id,
                        'code' => $code,
                        ...($locked instanceof ConferenceEdition ? ['series_id' => $locked->series_id, 'lifecycle_status' => $locked->lifecycle_status] : []),
                    ])
                    ->log($isSeries ? 'Conference Series deleted' : 'Conference Edition deleted');
            });
        } catch (QueryException $exception) {
            $sqlState = (string) ($exception->errorInfo[0] ?? '');
            $driverCode = (int) ($exception->errorInfo[1] ?? 0);
            $foreignKeyViolation = $sqlState === '23503'
                || ($sqlState === '23000' && $driverCode === 1451)
                || (in_array($driverCode, [19, 787], true) && str_contains($exception->getMessage(), 'FOREIGN KEY constraint failed'));

            if (! $foreignKeyViolation) {
                throw $exception;
            }
            throw ValidationException::withMessages(['delete' => $record instanceof ConferenceSeries ? 'seriesHasEditions' : 'editionHasData']);
        }
    }

    private function editionHasRelatedData(string $editionId): bool
    {
        // Every current direct Edition FK, including references that have no
        // inverse Eloquent relation. Indirect registrations/payments are guarded
        // by their required membership/package parents.
        foreach ([
            'submissions', 'edition_memberships', 'participation_packages',
            'activities', 'tracks', 'venues', 'edition_workflow_windows',
            'important_dates', 'number_sequences', 'payment_destinations',
            'generated_documents',
        ] as $table) {
            if (DB::table($table)->where('edition_id', $editionId)->exists()) {
                return true;
            }
        }

        // Permission-team references have no Edition FK and must not become
        // orphaned. Read them without changing roles or permissions.
        if (config('permission.teams')) {
            $teamColumn = (string) config('permission.column_names.team_foreign_key');
            foreach (['roles', 'model_has_roles', 'model_has_permissions'] as $key) {
                if (DB::table((string) config('permission.table_names.'.$key))->where($teamColumn, $editionId)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }
}
