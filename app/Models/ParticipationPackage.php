<?php

namespace App\Models;

use App\Enums\BillingMode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $edition_id
 * @property string $code
 * @property array<string, string> $name_i18n
 * @property BillingMode $billing_mode
 * @property string $price
 * @property string $currency_code
 * @property string|null $payment_destination_id
 * @property bool $active
 */
class ParticipationPackage extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'code',
        'name_i18n',
        'description_i18n',
        'billing_mode',
        'price',
        'currency_code',
        'payment_destination_id',
        'active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'name_i18n' => 'array',
            'description_i18n' => 'array',
            'billing_mode' => BillingMode::class,
            'price' => 'decimal:2',
            'active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<ConferenceEdition, $this>
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * @return BelongsTo<PaymentDestination, $this>
     */
    public function paymentDestination(): BelongsTo
    {
        return $this->belongsTo(PaymentDestination::class, 'payment_destination_id');
    }

    /**
     * @return HasMany<PackageActivityEntitlement, $this>
     */
    public function entitlements(): HasMany
    {
        return $this->hasMany(PackageActivityEntitlement::class, 'package_id');
    }
}
