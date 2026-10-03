<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberSequence extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'sequence_type',
        'prefix',
        'configuration_json',
        'last_value',
    ];

    protected function casts(): array
    {
        return [
            'configuration_json' => 'array',
            'last_value' => 'integer',
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
