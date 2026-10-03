<?php

namespace App\Models;

use App\Enums\BillingMode;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property string $membership_id
 * @property string $package_id
 * @property string $package_name_snapshot
 * @property BillingMode $billing_mode
 * @property string $fee_amount
 * @property string $currency_code
 * @property RegistrationStatus $status
 */
class Registration extends Model
{
    use HasUuids;

    protected $fillable = [
        'membership_id',
        'registration_code',
        'package_id',
        'package_name_snapshot',
        'participant_category',
        'billing_mode',
        'fee_amount',
        'currency_code',
        'status',
        'confirmed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'billing_mode' => BillingMode::class,
            'fee_amount' => 'decimal:2',
            'status' => RegistrationStatus::class,
            'confirmed_at' => 'immutable_datetime',
            'cancelled_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<EditionMembership, $this>
     */
    public function membership(): BelongsTo
    {
        return $this->belongsTo(EditionMembership::class, 'membership_id');
    }

    /**
     * @return BelongsTo<ParticipationPackage, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(ParticipationPackage::class, 'package_id');
    }

    /**
     * @return HasMany<RegistrationActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(RegistrationActivity::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<RegistrationFeeExemption, $this>
     */
    public function feeExemptions(): HasMany
    {
        return $this->hasMany(RegistrationFeeExemption::class);
    }

    /**
     * @return HasOne<RegistrationFeeExemption, $this>
     */
    public function activeFeeExemption(): HasOne
    {
        return $this->hasOne(RegistrationFeeExemption::class)
            ->whereNull('revoked_at')
            ->latestOfMany('granted_at');
    }

    public function isFeeSatisfied(): bool
    {
        if ($this->billing_mode === BillingMode::FREE) {
            return true;
        }

        if ($this->activeFeeExemption()->exists()) {
            return true;
        }

        return $this->payments()
            ->where('status', PaymentStatus::VERIFIED->value)
            ->exists();
    }
}
