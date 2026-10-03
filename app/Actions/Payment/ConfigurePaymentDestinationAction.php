<?php

namespace App\Actions\Payment;

use App\Models\ConferenceEdition;
use App\Models\PaymentDestination;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class ConfigurePaymentDestinationAction
{
    public function handle(
        ConferenceEdition $edition,
        User $actor,
        array $data,
        ?PaymentDestination $destination = null,
    ): PaymentDestination {
        return DB::transaction(function () use ($edition, $actor, $data, $destination): PaymentDestination {
            if ($destination !== null && $destination->edition_id !== $edition->id) {
                throw new DomainException(
                    'Payment destination does not belong to the Conference Edition.',
                );
            }

            foreach (['code', 'label', 'bank_name', 'account_number', 'account_holder'] as $required) {
                if (! isset($data[$required]) || trim((string) $data[$required]) === '') {
                    throw new DomainException(
                        "Payment destination field [{$required}] is required.",
                    );
                }
            }

            $isDefault = (bool) ($data['is_default'] ?? false);
            $active = (bool) ($data['active'] ?? true);

            if ($isDefault && ! $active) {
                throw new DomainException(
                    'The default payment destination must be active.',
                );
            }

            if ($isDefault) {
                $query = $edition->paymentDestinations()
                    ->where('is_default', true);

                if ($destination !== null) {
                    $query->where('id', '!=', $destination->id);
                }

                $query->update([
                    'is_default' => false,
                    'updated_at' => now(),
                ]);
            }

            $attributes = [
                'code' => strtoupper(trim((string) $data['code'])),
                'label' => trim((string) $data['label']),
                'bank_name' => trim((string) $data['bank_name']),
                'account_number' => trim((string) $data['account_number']),
                'account_holder' => trim((string) $data['account_holder']),
                'instructions_i18n' => $data['instructions_i18n'] ?? null,
                'is_default' => $isDefault,
                'active' => $active,
                'display_order' => max(0, (int) ($data['display_order'] ?? 0)),
            ];

            if ($destination === null) {
                $destination = $edition->paymentDestinations()->create($attributes);
                $event = 'payment_destination_created';
            } else {
                $destination->fill($attributes)->save();
                $event = 'payment_destination_updated';
            }

            activity('payment')
                ->causedBy($actor)
                ->performedOn($destination)
                ->event($event)
                ->withProperties([
                    'conference_edition_id' => $edition->id,
                    'code' => $destination->code,
                    'is_default' => $destination->is_default,
                    'active' => $destination->active,
                ])
                ->log('Payment destination configured');

            return $destination->refresh();
        });
    }
}
