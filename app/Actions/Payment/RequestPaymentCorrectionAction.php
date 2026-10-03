<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use App\Support\Authorization\EditionScopedAuthorizer;
use DomainException;
use Illuminate\Support\Facades\DB;

class RequestPaymentCorrectionAction
{
    public function __construct(
        private readonly EditionScopedAuthorizer $authorizer,
    ) {}

    public function handle(
        Payment $payment,
        User $actor,
        string $reason,
    ): Payment {
        $targetRegistration = $payment->registration()->firstOrFail();
        $editionId = $targetRegistration->membership()->value('edition_id');

        if (! is_string($editionId)) {
            throw new DomainException(
                'Payment is not attached to a valid Conference Edition.',
            );
        }

        $this->authorizer->authorize($actor, 'payment.verify', $editionId);

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
