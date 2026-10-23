<?php

namespace App\Actions\Academic;

use App\Models\ConferenceEdition;
use App\Models\ReviewerTopicEligibility;
use App\Models\Track;
use App\Models\User;
use App\Support\Academic\AcademicAccess;
use App\Support\Authorization\EditionScopedAuthorizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class ManageReviewerTopicEligibilityAction
{
    public function __construct(private readonly EditionScopedAuthorizer $authorizer, private readonly AcademicAccess $access) {}

    /** @param array<string, mixed> $data */
    public function grant(User $actor, ConferenceEdition $edition, array $data): void
    {
        DB::transaction(function () use ($actor, $edition, $data): void {
            $edition = ConferenceEdition::query()->lockForUpdate()->findOrFail($edition->id);
            $this->access->verified($actor);
            $this->authorizer->authorize($actor, 'review.assign', $edition->id);
            $data = Validator::make($data, [
                'reviewer_email' => 'required|email|max:255',
                'eligibility_id' => 'nullable|uuid',
                'track_ids' => 'required|array|min:1', 'track_ids.*' => 'required|uuid|distinct',
            ])->validate();
            $reviewer = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($data['reviewer_email']))])->lockForUpdate()->first();
            if ($reviewer === null || ! $reviewer->hasVerifiedEmail()) {
                throw ValidationException::withMessages(['reviewer_email' => 'REVIEWER_NOT_VERIFIED']);
            }
            $tracks = Track::query()->whereIn('id', $data['track_ids'])->where('edition_id', $edition->id)
                ->where('active', true)->orderBy('id')->lockForUpdate()->get();
            if ($tracks->count() !== count($data['track_ids']) || $edition->lifecycle_status === 'ARCHIVED') {
                throw ValidationException::withMessages(['track_ids' => 'TOPIC_INVALID']);
            }
            if (isset($data['eligibility_id']) && ! ReviewerTopicEligibility::query()
                ->whereKey($data['eligibility_id'])->where('edition_id', $edition->id)
                ->where('reviewer_id', $reviewer->id)->whereIn('track_id', $tracks->modelKeys())->exists()) {
                throw ValidationException::withMessages(['eligibility_id' => 'ELIGIBILITY_STALE']);
            }
            foreach ($tracks as $track) {
                $record = ReviewerTopicEligibility::query()->firstOrNew([
                    'edition_id' => $edition->id, 'reviewer_id' => $reviewer->id, 'track_id' => $track->id,
                ]);
                if ($record->exists && $record->active) {
                    continue;
                }
                $event = $record->exists ? 'reviewer_topic_reactivated' : 'reviewer_topic_granted';
                $record->fill(['assigned_by' => $actor->id, 'active' => true])->save();
                $this->audit($actor, $record, $event);
            }
        }, 3);
    }

    public function deactivate(User $actor, ReviewerTopicEligibility $eligibility): void
    {
        DB::transaction(function () use ($actor, $eligibility): void {
            $current = ReviewerTopicEligibility::query()->findOrFail($eligibility->id);
            ConferenceEdition::query()->lockForUpdate()->findOrFail($current->edition_id);
            $record = ReviewerTopicEligibility::query()->lockForUpdate()->findOrFail($current->id);
            $this->access->verified($actor);
            $this->authorizer->authorize($actor, 'review.assign', $record->edition_id);
            if ($record->active) {
                $record->update(['active' => false]);
                $this->audit($actor, $record, 'reviewer_topic_deactivated');
            }
        }, 3);
    }

    private function audit(User $actor, ReviewerTopicEligibility $record, string $event): void
    {
        activity('academic')->causedBy($actor)->performedOn($record)->event($event)->withProperties([
            'conference_edition_id' => $record->edition_id, 'reviewer_id' => $record->reviewer_id,
            'track_id' => $record->track_id, 'active' => $record->active, 'at' => now()->toIso8601String(),
        ])->log($event);
    }
}
