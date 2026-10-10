<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContributorAffiliation extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_contributor_id',
        'institution_id',
        'institution_name_snapshot',
        'ror_uri_snapshot',
        'subdivision_text',
        'country_code',
        'city_text',
        'sequence',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<SubmissionContributor, $this>
     */
    public function contributor(): BelongsTo
    {
        return $this->belongsTo(SubmissionContributor::class, 'submission_contributor_id');
    }

    /**
     * @return BelongsTo<Institution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }
}
