<?php

namespace App\Support\Registration;

use App\Enums\BillingMode;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\GeneratedDocument;
use App\Models\Payment;
use App\Models\Registration;
use Carbon\CarbonInterface;

final class ParticipantRegistrationView
{
    /**
     * @return array<string, mixed>
     */
    public function build(Registration $registration): array
    {
        $registration->loadMissing([
            'membership.edition',
            'activeFeeExemption',
            'package',
        ]);

        $payment = $registration->payments()
            ->where('status', '!=', PaymentStatus::CANCELLED->value)
            ->latest('created_at')
            ->first();

        $eventPass = $registration->generatedDocuments()
            ->where('document_type', GeneratedDocument::TYPE_EVENT_PASS)
            ->where('status', GeneratedDocument::STATUS_ISSUED)
            ->whereNull('revoked_at')
            ->latest('issued_at')
            ->first();

        $lookup = $eventPass?->verificationTokens()
            ->where('purpose', 'EVENT_PASS_LOOKUP')
            ->where('active', true)
            ->whereNull('revoked_at')
            ->first();

        $destination = $payment?->getAttribute('payment_destination_snapshot_json');
        $submissions = $registration->submissions()->latest('created_at')->get();
        $draft = $submissions->firstWhere('academic_status', 'DRAFT');
        $submitted = $submissions->first(fn ($submission): bool => $submission->academic_status !== 'DRAFT');
        $accepted = $submissions->contains(fn ($submission): bool => $submission->academic_status === 'ACCEPTED'
            || ($submission->accepted_at !== null && $submission->academic_status !== 'REJECTED'));
        $validContext = $registration->status !== RegistrationStatus::CANCELLED
            && $registration->package !== null
            && $registration->package->edition_id === $registration->membership?->edition_id;
        $nextAction = $this->nextAction($registration, $payment, $eventPass);
        if ($registration->status === RegistrationStatus::CANCELLED) {
            $nextAction = 'REGISTRATION_CANCELLED';
        } elseif (! $accepted) {
            if ($draft !== null) {
                $nextAction = 'CONTINUE_ABSTRACT';
            } elseif ($submitted !== null) {
                $nextAction = $submitted->academic_status === 'REJECTED' ? 'VIEW_ACADEMIC_STATUS' : 'WAIT_ACADEMIC_REVIEW';
            } elseif ($eventPass === null && $payment === null && $validContext) {
                $nextAction = 'SUBMIT_ABSTRACT';
            }
        }
        if ($accepted && $payment === null && ! in_array($registration->status, [RegistrationStatus::CANCELLED, RegistrationStatus::CONFIRMED], true) && $eventPass === null) {
            $nextAction = $registration->billing_mode === BillingMode::PAID && $registration->activeFeeExemption === null
                ? 'WAIT_PAYMENT_SETUP' : 'WAIT_REGISTRATION_CONFIRMATION';
        }
        $registrationUrl = route('editions.registration.show', $registration->membership->edition);
        $nextActionUrl = match ($nextAction) {
            'SUBMIT_ABSTRACT' => route('submissions.index'),
            'CONTINUE_ABSTRACT' => $draft === null ? null : route('submissions.details', $draft),
            'WAIT_ACADEMIC_REVIEW', 'VIEW_ACADEMIC_STATUS' => $submitted === null ? null : route('submissions.details', $submitted),
            'EVENT_PASS_AVAILABLE' => $eventPass === null ? null : route('documents.event-pass.show', $eventPass),
            'SUBMIT_PAYMENT_PROOF', 'REPLACE_PAYMENT_PROOF' => $registrationUrl,
            default => null,
        };
        $billingState = $registration->status === RegistrationStatus::CANCELLED ? 'CANCELLED'
            : ($registration->activeFeeExemption !== null ? 'COMPLIMENTARY'
            : ($registration->billing_mode === BillingMode::FREE ? 'FREE'
                : ($payment !== null ? 'PAYMENT_RECORDED' : ($accepted ? 'AWAITING_PAYMENT_SETUP' : 'AFTER_ACCEPT'))));

        return [
            'id' => $registration->id,
            'code' => $registration->registration_code,
            'status' => $registration->status->value,
            'packageName' => $registration->package_name_snapshot,
            'billingMode' => $registration->billing_mode->value,
            'feeAmount' => $registration->fee_amount,
            'currencyCode' => $registration->currency_code,
            'participantCategory' => $registration->participant_category,
            'confirmedAt' => $this->toIso8601(
                $registration->getAttribute('confirmed_at'),
            ),
            'complimentary' => $registration->activeFeeExemption !== null,
            'nextAction' => $nextAction,
            'nextActionUrl' => $nextActionUrl,
            'billingState' => $billingState,
            'academicStatus' => $submitted->academic_status ?? $draft?->academic_status,
            'payment' => $payment === null ? null : [
                'id' => $payment->id,
                'status' => $payment->status->value,
                'expectedAmount' => $payment->expected_amount,
                'currencyCode' => $payment->currency_code,
                'submittedAmount' => $payment->submitted_amount,
                'senderName' => $payment->sender_name,
                'transferDate' => $this->toDateString(
                    $payment->getAttribute('transfer_date'),
                ),
                'correctionReason' => $payment->correction_reason,
                'destination' => is_array($destination) ? $destination : [],
                'proofUploadUrl' => route('payments.proofs.store', $payment),
            ],
            'eventPass' => $eventPass === null ? null : [
                'id' => $eventPass->id,
                'issuedAt' => $this->toIso8601(
                    $eventPass->getAttribute('issued_at'),
                ),
                'publicCode' => $lookup?->public_code,
                'showUrl' => route('documents.event-pass.show', $eventPass),
                'qrUrl' => route('documents.event-pass.qr', $eventPass),
            ],
            'registrationUrl' => route(
                'editions.registration.show',
                $registration->membership->edition,
            ),
        ];
    }

    private function toIso8601(mixed $value): ?string
    {
        return $value instanceof CarbonInterface
            ? $value->toIso8601String()
            : null;
    }

    private function toDateString(mixed $value): ?string
    {
        return $value instanceof CarbonInterface
            ? $value->toDateString()
            : null;
    }

    private function nextAction(
        Registration $registration,
        ?Payment $payment,
        ?GeneratedDocument $eventPass,
    ): string {
        if (
            $registration->status === RegistrationStatus::CONFIRMED
            && $eventPass !== null
        ) {
            return 'EVENT_PASS_AVAILABLE';
        }

        if ($payment?->status === PaymentStatus::CORRECTION_REQUIRED) {
            return 'REPLACE_PAYMENT_PROOF';
        }

        if ($payment?->status === PaymentStatus::SUBMITTED) {
            return 'WAIT_FINANCE_VERIFICATION';
        }

        if ($payment?->status === PaymentStatus::PENDING) {
            return 'SUBMIT_PAYMENT_PROOF';
        }

        if ($registration->status === RegistrationStatus::CONFIRMED) {
            return 'REGISTRATION_CONFIRMED';
        }

        return 'WAIT_REGISTRATION_CONFIRMATION';
    }
}
