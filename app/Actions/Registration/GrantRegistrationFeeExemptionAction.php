<?php

namespace App\Actions\Registration;

use App\Enums\BillingMode;
use App\Enums\PaymentStatus;
use App\Models\Registration;
use App\Models\RegistrationFeeExemption;
use App\Models\User;
use App\Support\Authorization\EditionScopedAuthorizer;
use DomainException;
use Illuminate\Support\Facades\DB;

class GrantRegistrationFeeExemptionAction
{
    public function __construct(
        private readonly EditionScopedAuthorizer $authorizer,
    ) {}

    public function handle(
        Registration $registration,
        User $actor,
        string $reasonText,
        ?string $reasonCode = null,
    ): RegistrationFeeExemption {
        $editionId = $registration->membership()->value('edition_id');

        if (! is_string($editionId)) {
            throw new DomainException(
                'Registration is not attached to a valid Conference Edition.',
            );
        }

        $this->authorizer->authorize($actor, 'registration.fee_exempt', $editionId);

        return DB::transaction(function () use ($registration, $actor, $reasonText, $reasonCode): RegistrationFeeExemption {
            $registration = Registration::query()
                ->with('activeFeeExemption')
                ->lockForUpdate()
                ->findOrFail($registration->id);

            if ($registration->billing_mode !== BillingMode::PAID) {
                throw new DomainException(
                    'Fee exemption applies only to a normally PAID registration.',
                );
            }

            if ($registration->activeFeeExemption !== null) {
                throw new DomainException(
                    'This registration already has an active fee exemption.',
                );
            }

            if (trim($reasonText) === '') {
                throw new DomainException(
                    'Fee exemption requires an explicit reason.',
                );
            }

            $hasSubmittedFinancialEvidence = $registration->payments()
                ->where(function ($query): void {
                    $query
                        ->whereNotNull('submitted_at')
                        ->orWhere('status', PaymentStatus::VERIFIED->value);
                })
                ->exists();

            if ($hasSubmittedFinancialEvidence) {
                throw new DomainException(
                    'A registration with submitted or verified payment evidence cannot be converted to complimentary participation.',
                );
            }

            $registration->payments()
                ->where('status', PaymentStatus::PENDING->value)
                ->update([
                    'status' => PaymentStatus::CANCELLED->value,
                    'cancelled_at' => now(),
                    'updated_at' => now(),
                ]);

            $exemption = $registration->feeExemptions()->create([
                'reason_code' => $reasonCode,
                'reason_text' => trim($reasonText),
                'granted_by_user_id' => $actor->id,
                'granted_at' => now(),
            ]);

            activity('payment')
                ->causedBy($actor)
                ->performedOn($registration)
                ->event('registration_fee_exempted')
                ->withProperties([
                    'fee_exemption_id' => $exemption->id,
                    'reason_code' => $reasonCode,
                ])
                ->log('Registration fee exemption granted');

            return $exemption->refresh();
        });
    }
}
