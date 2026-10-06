<?php

namespace App\Actions\Payment;

use App\Actions\Registration\EnsureEventPassAction;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Payment;
use App\Models\User;
use App\Support\Authorization\EditionScopedAuthorizer;
use DomainException;
use Illuminate\Support\Facades\DB;

class VerifyPaymentAction
{
    public function __construct(
        private readonly EditionScopedAuthorizer $authorizer,
        private readonly EnsureEventPassAction $ensureEventPass,
    ) {}

    public function handle(
        Payment $payment,
        User $actor,
    ): Payment {
        $targetRegistration = $payment->registration()->firstOrFail();
        $editionId = $targetRegistration->membership()->value('edition_id');

        if (! is_string($editionId)) {
            throw new DomainException(
                'Payment is not attached to a valid Conference Edition.',
            );
        }

        $this->authorizer->authorize($actor, 'payment.verify', $editionId);

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

            $this->ensureEventPass->handle($payment->registration);

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
