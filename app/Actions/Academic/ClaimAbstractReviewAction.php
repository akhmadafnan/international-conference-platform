<?php

namespace App\Actions\Academic;

use App\Models\ConferenceEdition;
use App\Models\ReviewAssignment;
use App\Models\ReviewerTopicEligibility;
use App\Models\Submission;
use App\Models\Track;
use App\Models\User;
use App\Support\Academic\AbstractPoolEligibility;
use App\Support\Academic\AbstractSnapshotIntegrity;
use App\Support\Academic\AcademicAccess;
use App\Support\Academic\ReviewerDesignation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class ClaimAbstractReviewAction
{
    public function __construct(
        private readonly AbstractPoolEligibility $pool,
        private readonly AbstractSnapshotIntegrity $integrity,
        private readonly AcademicAccess $access,
    ) {}

    /** @param array<string, mixed> $data */
    public function handle(User $reviewer, Submission $submission, array $data): ReviewAssignment
    {
        return DB::transaction(function () use ($reviewer, $submission, $data): ReviewAssignment {
            $s = Submission::query()->whereKey($submission->id)->lockForUpdate()->firstOrFail();
            $edition = ConferenceEdition::query()->whereKey($s->edition_id)->lockForUpdate()->firstOrFail();
            $reviewer = User::query()->whereKey($reviewer->id)->lockForUpdate()->firstOrFail();
            $this->access->verified($reviewer);
            $this->access->unconflicted($reviewer, $s);
            $data = Validator::make($data, [
                'version' => 'required|integer|min:1', 'snapshot_id' => 'required|uuid',
                'no_conflict_declared' => 'required|accepted',
            ])->validate();
            if (! ReviewerDesignation::assigned($reviewer, $edition->id)) {
                throw ValidationException::withMessages(['claim' => 'REVIEWER_NOT_ELIGIBLE']);
            }
            $track = Track::query()->whereKey($s->track_id)->where('edition_id', $edition->id)->where('active', true)->lockForUpdate()->first();
            $eligibility = ReviewerTopicEligibility::query()->where('edition_id', $edition->id)
                ->where('reviewer_id', $reviewer->id)->where('track_id', $s->track_id)->where('active', true)->lockForUpdate()->first();
            $snapshot = $this->integrity->current($s);
            if ($track === null || $eligibility === null || $snapshot === null
                || $s->current_abstract_version !== (int) $data['version'] || $snapshot->id !== $data['snapshot_id']) {
                throw ValidationException::withMessages(['claim' => 'ABSTRACT_STALE_OR_INELIGIBLE']);
            }
            $assignments = ReviewAssignment::query()->where('submission_id', $s->id)
                ->where('academic_stage', ReviewAssignment::ABSTRACT_STAGE)->orderBy('id')->lockForUpdate()->get();
            $existing = $assignments->first(fn ($a) => $a->reviewer_id === $reviewer->id && $a->snapshot_id === $snapshot->id);
            if ($existing !== null && $existing->assignment_source === 'SELF_CLAIM'
                && in_array($existing->status, ['ACCEPTED', 'COMPLETED'], true)
                && $this->pool->eligibleQuery($reviewer, $edition)->whereKey($s->id)->exists()) {
                return $existing;
            }
            if ($existing !== null || ! $this->pool->allows($reviewer, $s)) {
                throw ValidationException::withMessages(['claim' => 'ABSTRACT_UNAVAILABLE']);
            }
            // Shared affiliation requires the existing Committee acknowledgement path.
            if ($this->access->sameAffiliation($reviewer, $s)) {
                throw ValidationException::withMessages(['claim' => 'COMMITTEE_AFFILIATION_CHECK_REQUIRED']);
            }
            $assignment = ReviewAssignment::query()->create([
                'submission_id' => $s->id, 'snapshot_id' => $snapshot->id, 'reviewer_id' => $reviewer->id,
                'assigned_by' => $reviewer->id, 'academic_stage' => ReviewAssignment::ABSTRACT_STAGE,
                'assignment_source' => 'SELF_CLAIM', 'status' => 'ACCEPTED', 'anonymity' => 'SINGLE_ANONYMOUS',
                'same_affiliation' => false, 'screening_note' => 'Reviewer explicitly declared no conflict during Self-Claim.',
            ]);
            $s->update(['academic_status' => 'UNDER_REVIEW']);
            activity('academic')->causedBy($reviewer)->performedOn($assignment)->event('abstract_review_claimed')->withProperties([
                'conference_edition_id' => $edition->id, 'submission_id' => $s->id, 'snapshot_id' => $snapshot->id,
                'abstract_version' => $snapshot->version, 'academic_stage' => ReviewAssignment::ABSTRACT_STAGE,
                'reviewer_id' => $reviewer->id, 'no_conflict_declared' => true,
            ])->log('abstract_review_claimed');

            return $assignment;
        }, 3);
    }
}
