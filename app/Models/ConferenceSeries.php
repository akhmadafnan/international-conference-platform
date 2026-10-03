<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceSeries extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'name',
        'acronym',
        'about_i18n',
        'logo_file_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'about_i18n' => 'array',
        ];
    }

    public function editions(): HasMany
    {
        return $this->hasMany(ConferenceEdition::class, 'series_id');
    }

    public function logoFile(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class, 'logo_file_id');
    }
}
