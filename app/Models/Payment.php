<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'registration_id',
        'payment_destination_id',
        'package_id_snapshot',
        'package_name_snapshot',
        'expected_amount',
        'currency_code',
        'payment_destination_snapshot_json',
        'submitted_amount',
        'sender_name',
        'transfer_date',
        'status',
        'submitted_at',
        'verified_by_user_id',
        'verified_at',
        'correction_reason',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'payment_destination_snapshot_json' => 'array',
            'submitted_amount' => 'decimal:2',
            'transfer_date' => 'date',
            'status' => PaymentStatus::class,
            'submitted_at' => 'immutable_datetime',
            'verified_at' => 'immutable_datetime',
            'cancelled_at' => 'immutable_datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function paymentDestination(): BelongsTo
    {
        return $this->belongsTo(PaymentDestination::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function proofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }
}
