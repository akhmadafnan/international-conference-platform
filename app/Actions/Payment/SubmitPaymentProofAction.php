<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\StoredFile;
use App\Models\User;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

class SubmitPaymentProofAction
{
    public function handle(
        Payment $payment,
        StoredFile $storedFile,
        User $actor,
        string $submittedAmount,
        ?string $senderName = null,
        ?CarbonInterface $transferDate = null,
    ): PaymentProof {
        return DB::transaction(function () use (
            $payment,
            $storedFile,
            $actor,
            $submittedAmount,
            $senderName,
            $transferDate,
        ): PaymentProof {
            $payment = Payment::query()
                ->with('registration.membership')
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if (! in_array($payment->status, [
                PaymentStatus::PENDING,
                PaymentStatus::CORRECTION_REQUIRED,
            ], true)) {
                throw new DomainException(
                    'Payment proof may only be submitted for a pending or correction-required payment.',
                );
            }

            if ($payment->registration->membership->user_id !== $actor->id) {
                throw new DomainException(
                    'Payment proof may only be submitted by the registered participant.',
                );
            }

            if (strtoupper($storedFile->visibility_class) === 'PUBLIC') {
                throw new DomainException(
                    'Payment proof must use protected/private file storage.',
                );
            }

            $normalizedAmount = trim($submittedAmount);
            $isZero = preg_match('/^0+(?:\.0+)?$/', $normalizedAmount) === 1;
            $isPositive = preg_match('/^\d+(?:\.\d+)?$/', $normalizedAmount) === 1
                && ! $isZero;

            if (! $isPositive) {
                throw new DomainException(
                    'Submitted payment amount must be greater than zero.',
                );
            }

            $latestVersion = (int) $payment->proofs()->max('version');

            $payment->proofs()
                ->whereNull('superseded_at')
                ->update([
                    'superseded_at' => now(),
                    'updated_at' => now(),
                ]);

            $proof = $payment->proofs()->create([
                'stored_file_id' => $storedFile->id,
                'version' => $latestVersion + 1,
                'submitted_by_user_id' => $actor->id,
                'submitted_at' => now(),
            ]);

            $payment->forceFill([
                'submitted_amount' => $normalizedAmount,
                'sender_name' => $senderName,
                'transfer_date' => $transferDate,
                'status' => PaymentStatus::SUBMITTED,
                'submitted_at' => now(),
                'correction_reason' => null,
            ])->save();

            activity('payment')
                ->causedBy($actor)
                ->performedOn($payment)
                ->event('payment_proof_submitted')
                ->withProperties([
                    'payment_proof_id' => $proof->id,
                    'version' => $proof->version,
                ])
                ->log('Payment proof submitted');

            return $proof->refresh();
        });
    }
}
