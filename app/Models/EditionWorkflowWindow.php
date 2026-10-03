<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }
}
