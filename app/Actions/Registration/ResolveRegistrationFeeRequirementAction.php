<?php

namespace App\Actions\Registration;

use App\Enums\BillingMode;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Payment;
use App\Models\PaymentDestination;
use App\Models\Registration;
use DomainException;
use Illuminate\Support\Facades\DB;

class ResolveRegistrationFeeRequirementAction
{
    public function __construct(
        private readonly EnsureEventPassAction $ensureEventPass,
    ) {}

    public function handle(Registration $registration): ?Payment
    {
        return DB::transaction(function () use ($registration): ?Payment {
            $registration = Registration::query()
                ->with([
                    'membership.edition.paymentDestinations',
                    'package.paymentDestination',
                    'activeFeeExemption',
                ])
                ->lockForUpdate()
                ->findOrFail($registration->id);

            if ($registration->status === RegistrationStatus::CANCELLED) {
                throw new DomainException(
                    'A cancelled registration cannot resolve its fee requirement.',
                );
            }

            if ($registration->status === RegistrationStatus::CONFIRMED) {
                $this->ensureEventPass->handle($registration);

                return $registration->payments()
                    ->where('status', PaymentStatus::VERIFIED->value)
                    ->latest('verified_at')
                    ->first();
            }

            if (
                $registration->billing_mode === BillingMode::FREE
                || $registration->activeFeeExemption !== null
            ) {
                $registration->forceFill([
                    'status' => RegistrationStatus::CONFIRMED,
                    'confirmed_at' => $registration->confirmed_at ?? now(),
                ])->save();

                $this->ensureEventPass->handle($registration);

                activity('registration')
                    ->performedOn($registration)
                    ->event('registration_fee_resolved_without_payment')
                    ->withProperties([
                        'resolution' => $registration->billing_mode === BillingMode::FREE
                            ? 'FREE'
                            : 'COMPLIMENTARY',
                    ])
                    ->log('Registration fee requirement resolved without payment');

                return null;
            }

            $existingPayment = $registration->payments()
                ->where('status', '!=', PaymentStatus::CANCELLED->value)
                ->latest('created_at')
                ->first();

            if ($existingPayment !== null) {
                $registration->forceFill([
                    'status' => $existingPayment->status === PaymentStatus::VERIFIED
                        ? RegistrationStatus::CONFIRMED
                        : RegistrationStatus::PAYMENT_PENDING,
                    'confirmed_at' => $existingPayment->status === PaymentStatus::VERIFIED
                        ? ($registration->confirmed_at ?? $existingPayment->verified_at ?? now())
                        : $registration->confirmed_at,
                ])->save();

                if ($registration->status === RegistrationStatus::CONFIRMED) {
                    $this->ensureEventPass->handle($registration);
                }

                return $existingPayment;
            }

            $destination = $this->resolvePaymentDestination($registration);

            $payment = $registration->payments()->create([
                'payment_destination_id' => $destination->id,
                'package_id_snapshot' => $registration->package_id,
                'package_name_snapshot' => $registration->package_name_snapshot,
                'expected_amount' => $registration->fee_amount,
                'currency_code' => strtoupper($registration->currency_code),
                'payment_destination_snapshot_json' => [
                    'code' => $destination->code,
                    'label' => $destination->label,
                    'bank_name' => $destination->bank_name,
                    'account_number' => $destination->account_number,
                    'account_holder' => $destination->account_holder,
                    'instructions_i18n' => $destination->instructions_i18n,
                ],
                'status' => PaymentStatus::PENDING,
            ]);

            $registration->forceFill([
                'status' => RegistrationStatus::PAYMENT_PENDING,
            ])->save();

            activity('payment')
                ->performedOn($payment)
                ->event('payment_obligation_created')
                ->withProperties([
                    'registration_id' => $registration->id,
                    'conference_edition_id' => $registration->membership->edition_id,
                    'currency_code' => $payment->currency_code,
                ])
                ->log('Payment obligation created');

            return $payment->refresh();
        });
    }

    private function resolvePaymentDestination(
        Registration $registration,
    ): PaymentDestination {
        $packageDestination = $registration->package->paymentDestination;

        if (
            $packageDestination !== null
            && $packageDestination->active
            && $packageDestination->edition_id === $registration->membership->edition_id
        ) {
            return $packageDestination;
        }

        $defaultDestination = $registration->membership->edition
            ->paymentDestinations()
            ->where('active', true)
            ->where('is_default', true)
            ->orderBy('display_order')
            ->first();

        if ($defaultDestination === null) {
            throw new DomainException(
                'A PAID registration requires an active payment destination.',
            );
        }

        return $defaultDestination;
    }
}
