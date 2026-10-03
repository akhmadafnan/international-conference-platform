<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class RequestPaymentCorrectionAction
{
    public function handle(
        Payment $payment,
        User $actor,
        string $reason,
    ): Payment {
        return DB::transaction(function () use ($payment, $actor, $reason): Payment {
            $payment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if ($payment->status !== PaymentStatus::SUBMITTED) {
                throw new DomainException(
                    'Payment correction may only be requested for a submitted payment.',
                );
            }

            if (trim($reason) === '') {
                throw new DomainException(
                    'Payment correction requires an explicit reason.',
                );
            }

            $payment->forceFill([
                'status' => PaymentStatus::CORRECTION_REQUIRED,
                'correction_reason' => trim($reason),
            ])->save();

            activity('payment')
                ->causedBy($actor)
                ->performedOn($payment)
                ->event('payment_correction_requested')
                ->log('Payment correction requested');

            return $payment->refresh();
        });
    }
}
