<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Track extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'code',
        'name_i18n',
        'description_i18n',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'name_i18n' => 'array',
            'description_i18n' => 'array',
            'active' => 'boolean',
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
