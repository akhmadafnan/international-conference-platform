<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasUuids;

    protected $fillable = [
        'payment_id',
        'reason_code',
        'reason_text',
        'amount',
        'currency_code',
        'status',
        'processed_by_user_id',
        'processed_at',
        'proof_file_id',
        'restricted_destination_json',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => RefundStatus::class,
            'processed_at' => 'immutable_datetime',
            'restricted_destination_json' => 'array',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public function proofFile(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class, 'proof_file_id');
    }
}
