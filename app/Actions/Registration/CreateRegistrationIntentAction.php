<?php

namespace App\Actions\Registration;

use App\Enums\BillingMode;
use App\Enums\WorkflowWindowCode;
use App\Enums\MembershipStatus;
use App\Enums\RegistrationActivityStatus;
use App\Enums\RegistrationStatus;
use App\Models\ConferenceEdition;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class CreateRegistrationIntentAction
{
    public function __construct(
        private readonly NextEditionNumberAction $nextEditionNumber,
        private readonly AssertEditionWorkflowWindowOpenAction $workflowWindowGuard,
    ) {}

    public function handle(
        User $user,
        ConferenceEdition $edition,
        ParticipationPackage $package,
        ?string $participantCategory = null,
    ): Registration {
        $this->workflowWindowGuard->handle(
            $edition,
            WorkflowWindowCode::REGISTRATION,
        );

        return DB::transaction(function () use ($user, $edition, $package, $participantCategory): Registration {
            $package->refresh();

            if ($package->edition_id !== $edition->id) {
                throw new DomainException(
                    'The selected participation package does not belong to the active Conference Edition.',
                );
            }

            if (! $package->active) {
                throw new DomainException(
                    'The selected participation package is not active.',
                );
            }

            $this->assertPackageBillingConfiguration($package);

            $membership = EditionMembership::query()->firstOrCreate(
                [
                    'edition_id' => $edition->id,
                    'user_id' => $user->id,
                ],
                [
                    'membership_status' => MembershipStatus::ACTIVE,
                    'joined_at' => now(),
                ],
            );

            if ($membership->registration()->exists()) {
                throw new DomainException(
                    'This user already has a registration for the Conference Edition.',
                );
            }

            $registration = Registration::query()->create([
                'membership_id' => $membership->id,
                'registration_code' => $this->nextEditionNumber->handle(
                    $edition,
                    'REGISTRATION',
                    $edition->edition_code.'-REG-',
                ),
                'package_id' => $package->id,
                'package_name_snapshot' => $this->resolvePackageName($package),
                'participant_category' => $participantCategory,
                'billing_mode' => $package->billing_mode,
                'fee_amount' => $package->price,
                'currency_code' => strtoupper($package->currency_code),
                'status' => RegistrationStatus::PENDING,
            ]);

            $package->entitlements()
                ->with('activity')
                ->get()
                ->each(function ($entitlement) use ($registration): void {
                    if (! $entitlement->activity || ! $entitlement->activity->active) {
                        return;
                    }

                    $registration->activities()->create([
                        'activity_id' => $entitlement->activity_id,
                        'entitlement_source' => 'PACKAGE',
                        'status' => RegistrationActivityStatus::ENTITLED,
                        'assigned_notes' => $entitlement->notes,
                    ]);
                });

            activity('registration')
                ->causedBy($user)
                ->performedOn($registration)
                ->event('registration_intent_created')
                ->withProperties([
                    'conference_edition_id' => $edition->id,
                    'package_id' => $package->id,
                    'billing_mode' => $package->billing_mode->value,
                ])
                ->log('Registration intent created');

            return $registration->refresh();
        });
    }

    private function assertPackageBillingConfiguration(
        ParticipationPackage $package,
    ): void {
        $price = trim((string) $package->price);
        $isZero = preg_match('/^0+(?:\.0+)?$/', $price) === 1;
        $isPositive = preg_match('/^\d+(?:\.\d+)?$/', $price) === 1
            && ! $isZero;

        if ($package->billing_mode === BillingMode::FREE && ! $isZero) {
            throw new DomainException(
                'A FREE participation package must have a zero price.',
            );
        }

        if ($package->billing_mode === BillingMode::PAID && ! $isPositive) {
            throw new DomainException(
                'A PAID participation package must have a positive price.',
            );
        }

        if (strlen($package->currency_code) !== 3) {
            throw new DomainException(
                'Participation package currency must use a three-letter ISO code.',
            );
        }
    }

    private function resolvePackageName(
        ParticipationPackage $package,
    ): string {
        $names = $package->name_i18n;

        foreach (['id', 'en', 'ar'] as $locale) {
            $name = $names[$locale] ?? null;

            if (is_string($name) && trim($name) !== '') {
                return trim($name);
            }
        }

        foreach ($names as $name) {
            if (trim($name) !== '') {
                return trim($name);
            }
        }

        throw new DomainException(
            'Participation package must have at least one display name.',
        );
    }
}
