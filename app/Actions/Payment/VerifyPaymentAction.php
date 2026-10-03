<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Payment;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class VerifyPaymentAction
{
    public function handle(
        Payment $payment,
        User $actor,
    ): Payment {
        Gate::forUser($actor)->authorize('payment.verify');

        return DB::transaction(function () use ($payment, $actor): Payment {
            $payment = Payment::query()
                ->with('registration')
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if ($payment->status !== PaymentStatus::SUBMITTED) {
                throw new DomainException(
                    'Only a submitted payment can be verified.',
                );
            }

            if (! $payment->proofs()->whereNull('superseded_at')->exists()) {
                throw new DomainException(
                    'Payment verification requires a current payment proof.',
                );
            }

            $verifiedAt = now();

            $payment->forceFill([
                'status' => PaymentStatus::VERIFIED,
                'verified_by_user_id' => $actor->id,
                'verified_at' => $verifiedAt,
                'correction_reason' => null,
            ])->save();

            $payment->registration->forceFill([
                'status' => RegistrationStatus::CONFIRMED,
                'confirmed_at' => $payment->registration->confirmed_at ?? $verifiedAt,
            ])->save();

            activity('payment')
                ->causedBy($actor)
                ->performedOn($payment)
                ->event('payment_verified')
                ->withProperties([
                    'registration_id' => $payment->registration_id,
                ])
                ->log('Payment verified');

            return $payment->refresh();
        });
    }
}
