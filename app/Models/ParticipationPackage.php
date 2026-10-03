<?php

namespace App\Models;

use App\Enums\BillingMode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    public function paymentDestination(): BelongsTo
    {
        return $this->belongsTo(PaymentDestination::class, 'payment_destination_id');
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(PackageActivityEntitlement::class, 'package_id');
    }
}
