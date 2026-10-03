<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $edition_id
 * @property string $user_id
 * @property MembershipStatus $membership_status
 */
class EditionMembership extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'user_id',
        'membership_status',
        'joined_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'membership_status' => MembershipStatus::class,
            'joined_at' => 'immutable_datetime',
            'ended_at' => 'immutable_datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne<Registration, $this>
     */
    public function registration(): HasOne
    {
        return $this->hasOne(Registration::class, 'membership_id');
    }
}
