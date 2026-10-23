<?php

declare(strict_types=1);

namespace App\Actions\Academic;

use App\Enums\RegistrationStatus;
use App\Models\AcademicDecision;
use App\Models\AcademicReview;
use App\Models\AcademicScreening;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\SubmissionSnapshot;
use App\Models\User;
use App\Support\Academic\AcademicAccess;
use App\Support\Submission\InitialSubmissionSnapshotBuilder;
use App\Support\Submission\SubmissionReadinessPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

/** All writers lock Submission first, including review/assignment writers. */
final class AcademicWorkflow
{
    public function __construct(private readonly AcademicAccess $access, private readonly PresentationLoa $loa) {}

    /** @param array<string, mixed> $data */
    public function screen(User $actor, Submission $submission, array $data): void
    {
        DB::transaction(function () use ($actor, $submission, $data): void {
            $s = $this->lock($submission);
            $this->access->authorize($actor, 'submission.admin_screen', $s);
            $this->access->unconflicted($actor, $s);
            $data = Validator::make($data, ['version' => 'required|integer|min:1', 'outcome' => ['required', Rule::in(['PASS', 'CORRECTION_REQUIRED', 'INELIGIBLE'])], 'note' => 'required|string|max:8000'])->validate();
            $this->version($s, (int) $data['version']);
            $this->ensure($s->academic_status === 'SUBMITTED', 'state');
            $this->ensure(! $this->assignments($s)->exists(), 'state');
            $latest = $this->screening($s);
            if ($latest?->outcome === $data['outcome'] && $latest->note === $data['note']) {
                return;
            }
            $record = AcademicScreening::query()->create(['submission_id' => $s->id, 'abstract_version' => $s->current_abstract_version, 'outcome' => $data['outcome'], 'note' => $data['note'], 'actor_id' => $actor->id]);
            $this->audit($actor, $s, 'submission_administratively_screened', ['screening_id' => $record->id, 'outcome' => $record->outcome]);
        });
    }

    /** @param array<string, mixed> $data */
    public function assign(User $actor, Submission $submission, array $data): ReviewAssignment
    {
        return DB::transaction(function () use ($actor, $submission, $data): ReviewAssignment {
            $s = $this->lock($submission);
            $this->access->authorize($actor, 'review.assign', $s);
            $this->access->unconflicted($actor, $s);
            $data = Validator::make($data, ['version' => 'required|integer|min:1', 'reviewer_email' => 'required|email', 'coi_screened' => 'required|accepted', 'same_affiliation_acknowledged' => 'sometimes|boolean', 'screening_note' => 'required|string|max:8000'])->validate();
            $this->version($s, (int) $data['version']);
            $this->ensure(in_array($s->academic_status, ['SUBMITTED', 'UNDER_REVIEW'], true) && $this->screening($s)?->outcome === 'PASS', 'screening');
            $reviewer = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower($data['reviewer_email'])])->first();
            if (! $reviewer instanceof User || ! $reviewer->hasVerifiedEmail()) {
                throw ValidationException::withMessages(['academic' => 'reviewer']);
            }
            $this->access->unconflicted($reviewer, $s);
            $same = $this->access->sameAffiliation($reviewer, $s);
            $this->ensure(! $same || ($data['same_affiliation_acknowledged'] ?? false), 'affiliation');
            $snapshot = $this->snapshot($s);
            $existing = ReviewAssignment::query()->where('snapshot_id', $snapshot->id)->where('reviewer_id', $reviewer->id)->first();
            if ($existing !== null) {
                $this->ensure(in_array($existing->status, ['INVITED', 'ACCEPTED', 'COMPLETED'], true), 'duplicate');

                return $existing;
            }
            $assignment = ReviewAssignment::query()->create(['submission_id' => $s->id, 'snapshot_id' => $snapshot->id, 'reviewer_id' => $reviewer->id, 'assigned_by' => $actor->id, 'status' => 'INVITED', 'anonymity' => 'SINGLE_ANONYMOUS', 'same_affiliation' => $same, 'screening_note' => $data['screening_note']]);
            $s->update(['academic_status' => 'UNDER_REVIEW']);
            $this->audit($actor, $s, 'reviewer_assigned', ['assignment_id' => $assignment->id, 'reviewer_id' => $reviewer->id, 'same_affiliation' => $same]);

            return $assignment;
        });
    }

    public function cancel(User $actor, ReviewAssignment $assignment, string $reason): void
    {
        DB::transaction(function () use ($actor, $assignment, $reason): void {
            $s = $this->lock($assignment->submission()->firstOrFail());
            $this->access->authorize($actor, 'review.assign', $s);
            $this->access->unconflicted($actor, $s);
            $a = ReviewAssignment::query()->lockForUpdate()->findOrFail($assignment->id);
            $this->ensure(trim($reason) !== '' && mb_strlen($reason) <= 8000, 'reason');
            if ($a->status === 'CANCELLED') {
                return;
            }
            $this->ensure(in_array($a->status, ['INVITED', 'ACCEPTED'], true), 'state');
            $a->update(['status' => 'CANCELLED', 'response_note' => $reason]);
            $this->audit($actor, $s, 'review_assignment_cancelled', ['assignment_id' => $a->id]);
        });
    }

    public function respond(User $actor, ReviewAssignment $assignment, string $response, string $note): void
    {
        DB::transaction(function () use ($actor, $assignment, $response, $note): void {
            $s = $this->lock($assignment->submission()->firstOrFail());
            $a = ReviewAssignment::query()->lockForUpdate()->findOrFail($assignment->id);
            $this->reviewer($actor, $a, $s);
            $this->ensure(in_array($response, ['ACCEPTED', 'DECLINED', 'COI'], true), 'state');
            if ($a->status === $response) {
                return;
            }
            $this->ensure($a->status === 'INVITED' || ($a->status === 'ACCEPTED' && $response === 'COI'), 'state');
            $this->ensure(mb_strlen($note) <= 8000 && ($response === 'ACCEPTED' || trim($note) !== ''), 'reason');
            $a->update(['status' => $response, 'response_note' => $note]);
            $this->audit($actor, $s, 'review_assignment_'.strtolower($response), ['assignment_id' => $a->id]);
        });
    }

    /** @param array<string, mixed> $data */
    public function review(User $actor, ReviewAssignment $assignment, array $data): void
    {
        DB::transaction(function () use ($actor, $assignment, $data): void {
            $s = $this->lock($assignment->submission()->firstOrFail());
            $a = ReviewAssignment::query()->lockForUpdate()->findOrFail($assignment->id);
            $this->reviewer($actor, $a, $s);
            $data = Validator::make($data, ['final' => 'required|boolean', 'recommendation' => ['nullable', Rule::in(['ACCEPT', 'REVISION', 'REJECT'])], 'author_comments' => 'nullable|string|max:32000', 'confidential_comments' => 'nullable|string|max:32000'])->validate();
            $existing = $a->review()->first();
            if ($existing?->status === 'SUBMITTED') {
                $this->ensure($data['final'] && $existing->recommendation === ($data['recommendation'] ?? null) && (string) $existing->author_comments === (string) ($data['author_comments'] ?? '') && (string) $existing->confidential_comments === (string) ($data['confidential_comments'] ?? ''), 'locked');

                return;
            }
            $this->ensure($a->status === 'ACCEPTED', 'state');
            $this->ensure(! $data['final'] || (! empty($data['recommendation']) && trim((string) ($data['author_comments'] ?? '')) !== ''), 'reviewIncomplete');
            $review = AcademicReview::query()->updateOrCreate(['assignment_id' => $a->id], ['status' => $data['final'] ? 'SUBMITTED' : 'DRAFT', 'recommendation' => $data['recommendation'] ?? null, 'author_comments' => $data['author_comments'] ?? null, 'confidential_comments' => $data['confidential_comments'] ?? null, 'submitted_at' => $data['final'] ? now() : null]);
            if ($data['final']) {
                $a->update(['status' => 'COMPLETED']);
            }
            $this->audit($actor, $s, $data['final'] ? 'academic_review_submitted' : 'academic_review_draft_saved', ['assignment_id' => $a->id, 'review_id' => $review->id]);
        });
    }

    /** @param array<string, mixed> $data */
    public function decide(User $actor, Submission $submission, array $data): AcademicDecision
    {
        $written = null;
        try {
            return DB::transaction(function () use ($actor, $submission, $data, &$written): AcademicDecision {
                $s = $this->lock($submission);
                $this->access->authorize($actor, 'academic.decide', $s);
                $this->access->unconflicted($actor, $s);
                $data = Validator::make($data, ['version' => 'required|integer|min:1', 'outcome' => ['required', Rule::in(['ACCEPTED', 'REVISION_REQUIRED', 'REJECTED'])], 'author_message' => 'required|string|max:16000', 'rationale' => 'required|string|max:16000'])->validate();
                $this->version($s, (int) $data['version']);
                $old = AcademicDecision::query()->where('submission_id', $s->id)->where('abstract_version', $s->current_abstract_version)->first();
                if ($old !== null) {
                    $this->ensure($old->outcome === $data['outcome'] && $old->author_message === $data['author_message'] && $old->rationale === $data['rationale'], 'locked');

                    return $old;
                }
                $this->ensure($s->academic_status === 'UNDER_REVIEW' && $this->screening($s)?->outcome === 'PASS', 'screening');
                $this->ensure($this->assignments($s)->where('status', 'COMPLETED')->whereHas('review', fn ($q) => $q->where('status', 'SUBMITTED'))->count() >= $this->reviewerCount($s), 'reviewsRequired');
                $this->ensure(! $this->assignments($s)->whereIn('status', ['INVITED', 'ACCEPTED'])->exists(), 'reviewsPending');
                $this->ensure($data['outcome'] !== 'REVISION_REQUIRED' || ! AcademicDecision::query()->where('submission_id', $s->id)->where('outcome', 'REVISION_REQUIRED')->exists(), 'revisionLimit');
                $decision = AcademicDecision::query()->create(['submission_id' => $s->id, 'abstract_version' => $s->current_abstract_version, 'outcome' => $data['outcome'], 'author_message' => $data['author_message'], 'rationale' => $data['rationale'], 'actor_id' => $actor->id]);
                $s->update(['academic_status' => $data['outcome'], 'accepted_at' => $data['outcome'] === 'ACCEPTED' ? now() : null, 'rejected_at' => $data['outcome'] === 'REJECTED' ? now() : null]);
                $this->audit($actor, $s, 'academic_decision_recorded', ['decision_id' => $decision->id, 'outcome' => $decision->outcome]);
                if ($decision->outcome === 'ACCEPTED') {
                    $this->loa->issue($actor, $s, $decision, $written);
                }

                // Acceptance never marks PAID, confirms registration, or grants an Event Pass.
                return $decision;
            });
        } catch (Throwable $e) {
            if ($written !== null) {
                Storage::disk('private')->delete($written);
            }
            throw $e;
        }
    }

    /** Administrative correction and academic revision have distinct evidence.
     * @param  array<string, mixed>  $data
     */
    public function revise(User $actor, Submission $submission, array $data): void
    {
        DB::transaction(function () use ($actor, $submission, $data): void {
            $s = $this->lock($submission);
            $this->access->verified($actor);
            abort_unless($s->registration()->whereHas('membership', fn ($q) => $q->where('user_id', $actor->id))->exists(), 404);
            $registration = $s->registration()->lockForUpdate()->firstOrFail();
            $this->ensure($registration->status !== RegistrationStatus::CANCELLED
                && $registration->membership?->edition_id === $s->edition_id
                && $registration->package?->edition_id === $s->edition_id, 'state');
            $data = Validator::make($data, ['version' => 'required|integer|min:1', 'title' => 'required|string|max:500', 'abstract' => 'required|string|max:64000', 'keywords' => 'required|array', 'keywords.*' => 'required|string|max:255', 'response' => 'required|string|max:16000'])->validate();
            $this->version($s, (int) $data['version']);
            $administrative = $s->academic_status === 'SUBMITTED' && $this->screening($s)?->outcome === 'CORRECTION_REQUIRED';
            $this->ensure($administrative || $s->academic_status === 'REVISION_REQUIRED', 'state');
            $policy = SubmissionReadinessPolicy::fromEditionSettings($s->edition->settings_json);
            if ($policy === null) {
                throw ValidationException::withMessages(['academic' => 'configuration']);
            }
            $words = array_map('trim', $data['keywords']);
            $this->ensure(count($words) >= $policy->minKeywords && count($words) <= $policy->maxKeywords && count(array_unique(array_map('mb_strtolower', $words))) === count($words) && ! in_array('', $words, true), 'keywords');
            $snapshot = $this->snapshot($s)->metadata_json;
            $locale = (string) $s->getRawOriginal('primary_locale');
            $s->translations()->where('locale', $locale)->update(['title' => $data['title'], 'abstract_text' => $data['abstract']]);
            // The new ordered keyword set is canonical; previous values remain in immutable snapshots.
            foreach ($s->keywords()->where('locale', $locale)->orderBy('sequence')->get() as $row) {
                $row->delete();
            }
            foreach ($words as $index => $word) {
                $s->keywords()->create(['locale' => $locale, 'sequence' => $index + 1, 'value' => $word]);
            }
            foreach ($snapshot['translations'] as &$translation) {
                if ($translation['locale'] === $locale) {
                    $translation['title'] = $data['title'];
                    $translation['abstract'] = $data['abstract'];
                }
            }
            unset($translation);
            $snapshot['keywords'] = array_values(array_filter($snapshot['keywords'], fn ($row) => $row['locale'] !== $locale));
            foreach ($words as $index => $word) {
                $snapshot['keywords'][] = ['locale' => $locale, 'sequence' => $index + 1, 'value' => $word];
            }
            $version = $s->current_abstract_version + 1;
            $snapshot['abstract_version'] = $version;
            $snapshot['author_response'] = $data['response'];
            $snapshot['schema'] = 'iches.abstract_correction.v1';
            $s->snapshots()->create(['snapshot_type' => $administrative ? 'ADMIN_CORRECTION' : 'ABSTRACT_REVISION', 'version' => $version, 'metadata_json' => $snapshot, 'checksum' => app(InitialSubmissionSnapshotBuilder::class)->checksum($snapshot), 'created_by_user_id' => $actor->id]);
            $s->update(['academic_status' => 'SUBMITTED', 'current_abstract_version' => $version]);
            $this->audit($actor, $s, $administrative ? 'administrative_correction_submitted' : 'abstract_revision_submitted');
        });
    }

    public function reviewer(User $actor, ReviewAssignment $assignment, Submission $submission): void
    {
        $this->access->verified($actor);
        abort_unless($assignment->reviewer_id === $actor->id, 404);
        $this->access->unconflicted($actor, $submission);
        $this->ensure($assignment->anonymity === 'SINGLE_ANONYMOUS', 'anonymity');
        $this->ensure(! in_array($assignment->status, ['CANCELLED', 'DECLINED', 'COI'], true), 'state');
    }

    public function reviewerCount(Submission $s): int
    {
        $count = $s->edition->settings_json['abstract_review']['reviewer_count'] ?? 1;
        $this->ensure(is_int($count) && $count >= 1, 'configuration');

        return $count;
    }

    public function screening(Submission $s): ?AcademicScreening
    {
        return AcademicScreening::query()->where('submission_id', $s->id)->where('abstract_version', $s->current_abstract_version)->latest()->latest('id')->first();
    }

    public function snapshot(Submission $s): SubmissionSnapshot
    {
        return $s->snapshots()->where('version', $s->current_abstract_version)->whereIn('snapshot_type', ['INITIAL_SUBMISSION', 'ABSTRACT_REVISION', 'ADMIN_CORRECTION'])->firstOrFail();
    }

    /** @return Builder<ReviewAssignment> */
    public function assignments(Submission $s): Builder
    {
        return ReviewAssignment::query()->where('submission_id', $s->id)->where('snapshot_id', $this->snapshot($s)->id);
    }

    private function lock(Submission $s): Submission
    {
        $locked = Submission::query()->whereKey($s->id)->lockForUpdate()->firstOrFail();
        $this->ensure($locked->edition->lifecycle_status !== 'ARCHIVED', 'state');

        return $locked;
    }

    private function version(Submission $s, int $version): void
    {
        $this->ensure($s->current_abstract_version === $version, 'stale');
    }

    private function ensure(bool $condition, string $code): void
    {
        if (! $condition) {
            throw ValidationException::withMessages(['academic' => $code]);
        }
    }

    /** @param array<string, mixed> $properties */
    private function audit(User $actor, Submission $s, string $event, array $properties = []): void
    {
        activity('academic')->causedBy($actor)->performedOn($s)->event($event)->withProperties(['conference_edition_id' => $s->edition_id, 'abstract_version' => $s->current_abstract_version, ...$properties])->log($event);
    }
}
