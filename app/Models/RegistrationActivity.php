<?php

namespace App\Models;

use App\Enums\RegistrationActivityStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationActivity extends Model
{
    use HasUuids;

    protected $fillable = [
        'registration_id',
        'activity_id',
        'entitlement_source',
        'status',
        'assigned_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => RegistrationActivityStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Registration, $this>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * @return BelongsTo<Activity, $this>
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
