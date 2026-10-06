<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportantDate extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'code',
        'label_i18n',
        'starts_at',
        'ends_at',
        'public',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'label_i18n' => 'array',
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'public' => 'boolean',
            'display_order' => 'integer',
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
