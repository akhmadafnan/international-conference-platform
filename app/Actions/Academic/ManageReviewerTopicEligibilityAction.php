<?php

namespace App\Actions\Academic;

use App\Models\ConferenceEdition;
use App\Models\ReviewerTopicEligibility;
use App\Models\Track;
use App\Models\User;
use App\Support\Academic\AcademicAccess;
use App\Support\Academic\ReviewerDesignation;
use App\Support\Authorization\EditionScopedAuthorizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class ManageReviewerTopicEligibilityAction
{
    public function __construct(private readonly EditionScopedAuthorizer $authorizer, private readonly AcademicAccess $access) {}

    /** @param array<string, mixed> $data */
    public function sync(User $actor, ConferenceEdition $edition, array $data): void
    {
        DB::transaction(function () use ($actor, $edition, $data): void {
            $edition = ConferenceEdition::query()->lockForUpdate()->findOrFail($edition->id);
            $this->access->verified($actor->fresh());
            $this->authorizer->authorize($actor, 'review.assign', $edition->id);
            $data = Validator::make($data, [
                'reviewer_id' => 'required|uuid',
                'track_ids' => 'present|array', 'track_ids.*' => 'required|uuid|distinct',
                'expected_state' => 'required|string|max:64',
            ])->validate();
            $reviewer = User::query()->whereKey($data['reviewer_id'])->lockForUpdate()->first();
            if ($reviewer === null || ! $reviewer->hasVerifiedEmail() || ! ReviewerDesignation::assigned($reviewer, $edition->id)) {
                throw ValidationException::withMessages(['reviewer_id' => 'REVIEWER_NOT_ELIGIBLE']);
            }
            $tracks = Track::query()->where('edition_id', $edition->id)->where('active', true)
                ->orderBy('id')->lockForUpdate()->get();
            if ($edition->lifecycle_status === 'ARCHIVED' || array_diff($data['track_ids'], $tracks->modelKeys()) !== []) {
                throw ValidationException::withMessages(['track_ids' => 'TOPIC_INVALID']);
            }
            $records = ReviewerTopicEligibility::query()->where('edition_id', $edition->id)
                ->where('reviewer_id', $reviewer->id)->orderBy('id')->lockForUpdate()->get();
            $adding = $data['expected_state'] === 'new';
            if ((! $adding && $records->isEmpty()) || ($records->isEmpty() && $data['track_ids'] === [])) {
                throw ValidationException::withMessages(['track_ids' => 'TOPIC_REQUIRED']);
            }
            $current = $records->where('active', true)->whereIn('track_id', $tracks->modelKeys())->pluck('track_id')->all();
            $selected = $data['track_ids'];
            sort($current);
            sort($selected);
            $expected = $adding ? 'new' : $this->stateToken($edition->id, $reviewer->id);
            // An exact retry is harmless, but a stale replacement must never overwrite a newer selection.
            if (($adding && $records->isNotEmpty() && $current !== $selected)
                || (! hash_equals($expected, $data['expected_state']) && $current !== $selected)) {
                throw ValidationException::withMessages(['expected_state' => 'ELIGIBILITY_STALE']);
            }
            foreach ($tracks as $track) {
                $record = $records->firstWhere('track_id', $track->id);
                $active = in_array($track->id, $selected, true);
                if ($record === null && ! $active) {
                    continue;
                }
                if ($record !== null && $record->active === $active) {
                    continue;
                }
                $event = ! $active ? 'reviewer_topic_deactivated' : ($record === null ? 'reviewer_topic_granted' : 'reviewer_topic_reactivated');
                $record ??= new ReviewerTopicEligibility(['edition_id' => $edition->id, 'reviewer_id' => $reviewer->id, 'track_id' => $track->id]);
                $record->active = $active;
                if ($active) {
                    $record->assigned_by = $actor->id;
                }
                $record->save();
                $this->audit($actor, $record, $event);
            }
        }, 3);
    }

    public function stateToken(string $editionId, string $reviewerId): string
    {
        $records = ReviewerTopicEligibility::query()->where('edition_id', $editionId)->where('reviewer_id', $reviewerId)->orderBy('id')->get();
        $lastAudit = DB::table('activity_log')->where('subject_type', (new ReviewerTopicEligibility)->getMorphClass())
            ->whereIn('subject_id', $records->modelKeys())->max('id');

        return hash_hmac('sha256', json_encode([
            $editionId, $reviewerId, $records->map(fn ($record) => $record->getRawOriginal())->all(), $lastAudit,
            Track::query()->where('edition_id', $editionId)->where('active', true)->orderBy('id')->pluck('id')->all(),
        ], JSON_THROW_ON_ERROR), (string) config('app.key'));
    }

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
            if (! ReviewerDesignation::assigned($reviewer, $edition->id)) {
                throw ValidationException::withMessages(['reviewer_email' => 'REVIEWER_NOT_ELIGIBLE']);
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
