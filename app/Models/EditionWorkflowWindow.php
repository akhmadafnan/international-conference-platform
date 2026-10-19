<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property CarbonImmutable|null $opens_at
 * @property CarbonImmutable|null $closes_at
 */
class EditionWorkflowWindow extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'window_code',
        'opens_at',
        'closes_at',
        'active',
        'configuration_json',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'immutable_datetime',
            'closes_at' => 'immutable_datetime',
            'active' => 'boolean',
            'configuration_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ConferenceEdition, $this>
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }
}
