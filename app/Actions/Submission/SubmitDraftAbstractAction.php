<?php

declare(strict_types=1);

namespace App\Actions\Submission;

use App\Enums\RegistrationStatus;
use App\Enums\WorkflowWindowCode;
use App\Models\ConferenceEdition;
use App\Models\EditionWorkflowWindow;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use App\Support\Submission\InitialSubmissionSnapshotBuilder;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * P03-H: authoritative, exactly-once DRAFT -> SUBMITTED transition.
 *
 * Lock order: Submission -> Registration -> Membership -> Package -> Edition
 * -> abstract Window. Other draft writers take the Submission lock first.
 * The read-only P03-G evaluator is repeated AFTER locks, inside this transaction.
 */
final class SubmitDraftAbstractAction
{
    public function __construct(
        private readonly EvaluateDraftSubmissionReadinessAction $readiness,
        private readonly InitialSubmissionSnapshotBuilder $snapshots,
    ) {}

    public function handle(User $actor, Submission $submission): Submission
    {
        return DB::transaction(function () use ($actor, $submission): Submission {
            $draft = Submission::query()->whereKey($submission->id)->lockForUpdate()->firstOrFail();

            if ($draft->academic_status !== 'DRAFT') {
                throw new DomainException('Only a DRAFT Submission can be officially submitted.');
            }

            $registration = Registration::query()
                ->whereKey($draft->registration_id)->lockForUpdate()->firstOrFail();
            $membership = $registration->membership()->lockForUpdate()->first();
            $package = $registration->package()->lockForUpdate()->first();

            if ($membership === null
                || $membership->edition_id !== $draft->edition_id
                || $membership->user_id !== $actor->id
                || $registration->status === RegistrationStatus::CANCELLED
                || $package === null
                || $package->edition_id !== $draft->edition_id) {
                throw new DomainException('The Submission does not have an authorized Edition participation context.');
            }

            // Presenter billing/payment and Registration CONFIRMED are NOT prerequisites.
            $edition = ConferenceEdition::query()
                ->whereKey($draft->edition_id)->lockForUpdate()->firstOrFail();
            $this->assertAbstractWindowOpen($edition);

            $result = $this->readiness->handle($actor, $draft);
            if ($result->status === 'BLOCKED') {
                throw new DomainException('Submission metadata is not ready for official submission.');
            }

            if ($draft->snapshots()->where('snapshot_type', 'INITIAL_SUBMISSION')->exists()) {
                throw new DomainException('Initial submission evidence already exists; duplicate submission is prohibited.');
            }

            $metadata = $this->snapshots->build($draft);
            $checksum = $this->snapshots->checksum($metadata);
            $submittedAt = now();

            $snapshot = $draft->snapshots()->create([
                'snapshot_type' => 'INITIAL_SUBMISSION',
                'version' => 1,
                'metadata_json' => $metadata,
                'checksum' => $checksum,
                'created_by_user_id' => $actor->id,
            ]);

            $draft->update([
                'academic_status' => 'SUBMITTED',
                'submitted_at' => $submittedAt,
                'current_abstract_version' => 1,
            ]);

            // No contributor emails, references, private paths, or other PII in audit.
            activity('submission')
                ->causedBy($actor)
                ->performedOn($draft)
                ->event('submission_officially_submitted')
                ->withProperties([
                    'conference_edition_id' => $edition->id,
                    'registration_id' => $registration->id,
                    'paper_code' => $draft->paper_code,
                    'submission_snapshot_id' => $snapshot->id,
                    'snapshot_type' => 'INITIAL_SUBMISSION',
                    'snapshot_version' => 1,
                ])
                ->log('Abstract officially submitted');

            return $draft->refresh();
        }, 3);
    }

    /**
     * P03-H's PO-approved strict window rule does not change the generic
     * Phase02 AssertEditionWorkflowWindowOpenAction (which is permissive if absent).
     */
    private function assertAbstractWindowOpen(ConferenceEdition $edition): void
    {
        $window = EditionWorkflowWindow::query()
            ->where('edition_id', $edition->id)
            ->where('window_code', WorkflowWindowCode::ABSTRACT_SUBMISSION->value)
            ->lockForUpdate()
            ->first();

        if ($window === null || ! $window->active) {
            throw new DomainException('Official abstract submission requires an active Edition submission window.');
        }

        /** @var CarbonInterface|null $opensAt */
        $opensAt = $window->opens_at;
        /** @var CarbonInterface|null $closesAt */
        $closesAt = $window->closes_at;
        if ($opensAt === null || $closesAt === null || $opensAt->gt($closesAt)) {
            throw new DomainException('The Edition abstract submission window is not configured with valid time boundaries.');
        }

        $now = now();
        if ($now->lt($opensAt) || $now->gt($closesAt)) {
            throw new DomainException('Official abstract submission is outside the Edition submission period.');
        }
    }
}
