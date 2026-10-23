<?php

namespace App\Actions\Academic;

use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\Track;
use App\Models\User;
use App\Support\Academic\AcademicAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class CorrectSubmittedTopicRoutingAction
{
    public function __construct(private readonly AcademicAccess $access) {}

    public function handle(User $actor, Submission $submission, string $trackId, string $reason): void
    {
        DB::transaction(function () use ($actor, $submission, $trackId, $reason): void {
            $s = Submission::query()->lockForUpdate()->findOrFail($submission->id);
            $this->access->authorize($actor, 'review.assign', $s);
            $this->access->unconflicted($actor, $s);
            $data = Validator::make(['track_id' => $trackId, 'reason' => trim($reason)], [
                'track_id' => 'required|uuid', 'reason' => 'required|string|max:2000',
            ])->validate();
            if ($s->academic_status !== 'SUBMITTED' || $s->edition->lifecycle_status === 'ARCHIVED'
                || ReviewAssignment::query()->where('submission_id', $s->id)->exists()) {
                throw ValidationException::withMessages(['routing' => 'ROUTING_LOCKED']);
            }
            $track = Track::query()->whereKey($data['track_id'])->where('edition_id', $s->edition_id)->where('active', true)->lockForUpdate()->first();
            if ($track === null) {
                throw ValidationException::withMessages(['track_id' => 'TOPIC_INVALID']);
            }
            if ($s->track_id === $track->id) {
                return;
            }
            $old = $s->track_id;
            $s->update(['track_id' => $track->id]);
            activity('academic')->causedBy($actor)->performedOn($s)->event('submitted_topic_routing_corrected')->withProperties([
                'conference_edition_id' => $s->edition_id, 'old_track_id' => $old, 'new_track_id' => $track->id,
                'actor_id' => $actor->id, 'at' => now()->toIso8601String(), 'reason' => $data['reason'],
                'abstract_version' => $s->current_abstract_version,
            ])->log('Submitted topic routing corrected; scholarly snapshot unchanged');
        }, 3);
    }
}
