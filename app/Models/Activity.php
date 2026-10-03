<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'code',
        'name_i18n',
        'description_i18n',
        'starts_at',
        'ends_at',
        'venue_id',
        'activity_type',
        'attendance_required',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'name_i18n' => 'array',
            'description_i18n' => 'array',
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'attendance_required' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function packageEntitlements(): HasMany
    {
        return $this->hasMany(PackageActivityEntitlement::class, 'activity_id');
    }
}
