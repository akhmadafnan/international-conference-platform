<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VerificationToken extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected $fillable = [
        'subject_type',
        'subject_id',
        'purpose',
        'token_hash',
        'public_code',
        'active',
        'expires_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'expires_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
