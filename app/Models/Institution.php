<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasUuids;

    protected $fillable = [
        'preferred_name',
        'ror_uri',
        'country_code',
        'website',
        'aliases_json',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'aliases_json' => 'array',
        ];
    }

    /**
     * @return HasMany<ContributorAffiliation, $this>
     */
    public function contributorAffiliations(): HasMany
    {
        return $this->hasMany(ContributorAffiliation::class);
    }
}
