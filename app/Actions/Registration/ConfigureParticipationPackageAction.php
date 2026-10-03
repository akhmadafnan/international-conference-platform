<?php

namespace App\Actions\Registration;

use App\Enums\BillingMode;
use App\Models\ConferenceEdition;
use App\Models\ParticipationPackage;
use App\Models\PaymentDestination;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class ConfigureParticipationPackageAction
{
    /**
     * @param array{
     *     code: string,
     *     name_i18n: array<string, string>,
     *     description_i18n?: array<string, string>|null,
     *     billing_mode: BillingMode|string,
     *     price: string,
     *     currency_code?: string,
     *     payment_destination_id?: string|null,
     *     active?: bool,
     *     display_order?: int
     * } $data
     */
    public function handle(
        ConferenceEdition $edition,
        User $actor,
        array $data,
        ?ParticipationPackage $package = null,
    ): ParticipationPackage {
        return DB::transaction(function () use ($edition, $actor, $data, $package): ParticipationPackage {
            if ($package !== null && $package->edition_id !== $edition->id) {
                throw new DomainException(
                    'Participation package does not belong to the Conference Edition.',
                );
            }

            $code = strtoupper(trim((string) ($data['code'] ?? '')));
            $names = $data['name_i18n'] ?? [];
            $billingMode = $data['billing_mode'] instanceof BillingMode
                ? $data['billing_mode']
                : BillingMode::tryFrom((string) ($data['billing_mode'] ?? ''));
            $active = (bool) ($data['active'] ?? true);

            if ($code === '') {
                throw new DomainException('Participation package code is required.');
            }

            if (! is_array($names) || ! $this->hasDisplayName($names)) {
                throw new DomainException(
                    'Participation package requires at least one localized display name.',
                );
            }

            if ($billingMode === null) {
                throw new DomainException(
                    'Participation package billing mode must be FREE or PAID.',
                );
            }

            $currency = strtoupper(trim((string) ($data['currency_code'] ?? 'IDR')));

            if (strlen($currency) !== 3) {
                throw new DomainException(
                    'Participation package currency must use a three-letter ISO code.',
                );
            }

            $price = trim((string) ($data['price'] ?? '0'));
            $isZero = preg_match('/^0+(?:\.\d+)?$/', $price) === 1
                && preg_match('/[1-9]/', $price) !== 1;
            $isPositive = preg_match('/^\d+(?:\.\d+)?$/', $price) === 1
                && ! $isZero;

            $destinationId = $data['payment_destination_id'] ?? null;

            if ($billingMode === BillingMode::FREE) {
                $price = '0.00';
                $destinationId = null;
            } elseif (! $isPositive) {
                throw new DomainException(
                    'A PAID participation package must have a positive price.',
                );
            }

            if (
                $billingMode === BillingMode::PAID
                && $active
                && $this->resolveDestination($edition, $destinationId) === null
            ) {
                throw new DomainException(
                    'An active PAID package requires an active package-specific or Edition-default payment destination.',
                );
            }

            $attributes = [
                'code' => $code,
                'name_i18n' => $names,
                'description_i18n' => $data['description_i18n'] ?? null,
                'billing_mode' => $billingMode,
                'price' => $price,
                'currency_code' => $currency,
                'payment_destination_id' => $destinationId,
                'active' => $active,
                'display_order' => max(0, (int) ($data['display_order'] ?? 0)),
            ];

            if ($package === null) {
                $package = $edition->participationPackages()->create($attributes);
                $event = 'participation_package_created';
            } else {
                $package->fill($attributes)->save();
                $event = 'participation_package_updated';
            }

            activity('registration')
                ->causedBy($actor)
                ->performedOn($package)
                ->event($event)
                ->withProperties([
                    'conference_edition_id' => $edition->id,
                    'code' => $package->code,
                    'billing_mode' => $package->billing_mode->value,
                    'active' => $package->active,
                ])
                ->log('Participation package configured');

            return $package->refresh();
        });
    }

    /**
     * @param array<string, string> $names
     */
    private function hasDisplayName(array $names): bool
    {
        foreach ($names as $name) {
            if (is_string($name) && trim($name) !== '') {
                return true;
            }
        }

        return false;
    }

    private function resolveDestination(
        ConferenceEdition $edition,
        ?string $destinationId,
    ): ?PaymentDestination {
        if ($destinationId !== null) {
            return $edition->paymentDestinations()
                ->whereKey($destinationId)
                ->where('active', true)
                ->first();
        }

        return $edition->paymentDestinations()
            ->where('active', true)
            ->where('is_default', true)
            ->first();
    }
}
