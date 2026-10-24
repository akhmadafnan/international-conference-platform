<?php

namespace App\Support\Academic;

use App\Models\ConferenceEdition;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/** Safe discovery projection. Claim also locks and revalidates this predicate. */
final class AbstractPoolEligibility
{
    public function __construct(private readonly ActiveConferenceEditionContext $context, private readonly AbstractSnapshotIntegrity $integrity) {}

    /** @return Builder<Submission> */
    public function query(User $reviewer, ConferenceEdition $edition): Builder
    {
        return $this->eligibleQuery($reviewer, $edition)->where('academic_status', 'SUBMITTED')
            ->whereNotExists(fn (QueryBuilder $q) => $q->selectRaw('1')->from('review_assignments')
                ->whereColumn('submission_id', 'submissions.id')->where('academic_stage', ReviewAssignment::ABSTRACT_STAGE)
                ->where(function (QueryBuilder $assignment): void {
                    $assignment->whereIn('status', ReviewAssignment::ACTIVE_STATUSES)
                        ->orWhere(fn (QueryBuilder $current) => $current->whereNotIn('status', ['CANCELLED', 'DECLINED', 'COI'])
                            ->whereExists(fn (QueryBuilder $snapshot) => $snapshot->selectRaw('1')->from('submission_snapshots')
                                ->whereColumn('submission_snapshots.id', 'review_assignments.snapshot_id')
                                ->whereColumn('submission_snapshots.version', 'submissions.current_abstract_version')));
                }));
    }

    /** Base guards also used to revalidate an already-owned idempotent retry.
     * @return Builder<Submission>
     */
    public function eligibleQuery(User $reviewer, ConferenceEdition $edition): Builder
    {
        if ($this->context->id() !== $edition->id || getPermissionsTeamId() !== $edition->id) {
            throw new AuthorizationException('The active Conference Edition does not match the pool.');
        }
        $reviewer = User::query()->findOrFail($reviewer->id);
        $query = Submission::query()->select([
            'submissions.id', 'submissions.edition_id', 'submissions.track_id', 'submissions.paper_code',
            'submissions.academic_status', 'submissions.current_abstract_version',
        ])->where('submissions.edition_id', $edition->id)->whereIn('academic_status', ['SUBMITTED', 'UNDER_REVIEW']);
        if (! ReviewerDesignation::verifiedUsers($edition->id)->whereKey($reviewer->id)->exists()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('edition', fn ($q) => $q->where('lifecycle_status', '!=', 'ARCHIVED'))
            ->whereIn('submissions.id', $this->integrity->validSubmissionIds($edition))
            ->whereHas('registration.membership', fn ($q) => $q->where('edition_id', $edition->id)->where('user_id', '!=', $reviewer->id))
            ->whereHas('track', fn ($q) => $q->where('edition_id', $edition->id)->where('active', true)
                ->whereExists(fn (QueryBuilder $eligible) => $eligible->selectRaw('1')->from('reviewer_topic_eligibilities')
                    ->whereColumn('reviewer_topic_eligibilities.track_id', 'tracks.id')
                    ->where('reviewer_topic_eligibilities.edition_id', $edition->id)
                    ->where('reviewer_id', $reviewer->id)->where('reviewer_topic_eligibilities.active', true)))
            ->whereDoesntHave('contributors', fn ($q) => $q->where('linked_user_id', $reviewer->id)->orWhereRaw('LOWER(email) = ?', [mb_strtolower($reviewer->email)]))
            ->whereNotExists(fn (QueryBuilder $q) => $q->selectRaw('1')->from('review_assignments')
                ->whereColumn('submission_id', 'submissions.id')->where('reviewer_id', $reviewer->id)->where('status', 'COI'))
            // Only the latest screening for the current version is authoritative.
            // A prior PASS is valid history, never a required pool prerequisite.
            ->whereNotExists(fn (QueryBuilder $q) => $q->selectRaw('1')->from('academic_screenings')
                ->whereColumn('submission_id', 'submissions.id')->whereColumn('abstract_version', 'submissions.current_abstract_version')
                ->whereIn('outcome', ['CORRECTION_REQUIRED', 'INELIGIBLE'])
                ->whereRaw('academic_screenings.id = (SELECT latest_screening.id FROM academic_screenings AS latest_screening WHERE latest_screening.submission_id = submissions.id AND latest_screening.abstract_version = submissions.current_abstract_version ORDER BY latest_screening.created_at DESC, latest_screening.id DESC LIMIT 1)'));
    }

    public function allows(User $reviewer, Submission $submission): bool
    {
        return $this->query($reviewer, $submission->edition()->firstOrFail())->whereKey($submission->id)->exists();
    }
}
