<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venue extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'name',
        'address',
        'city',
        'country_code',
        'latitude',
        'longitude',
        'map_url',
        'details_i18n',
    ];

    protected function casts(): array
    {
        return [
            'details_i18n' => 'array',
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
