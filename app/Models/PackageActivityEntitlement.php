<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $activity_id
 * @property string|null $notes
 */
class PackageActivityEntitlement extends Model
{
    use HasUuids;

    protected $fillable = [
        'package_id',
        'activity_id',
        'entitlement_type',
        'notes',
    ];

    /**
     * @return BelongsTo<ParticipationPackage, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(ParticipationPackage::class, 'package_id');
    }

    /**
     * @return BelongsTo<Activity, $this>
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
