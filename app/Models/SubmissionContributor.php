<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionContributor extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'linked_user_id',
        'display_name',
        'given_name',
        'family_name',
        'single_name',
        'email',
        'country_code',
        'orcid_uri',
        'orcid_verification_state',
        'is_corresponding',
        'sequence',
        'contributor_role',
    ];

    protected function casts(): array
    {
        return [
            'single_name' => 'boolean',
            'is_corresponding' => 'boolean',
            'sequence' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Submission, $this>
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function linkedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }

    /**
     * @return HasMany<ContributorAffiliation, $this>
     */
    public function affiliations(): HasMany
    {
        return $this->hasMany(ContributorAffiliation::class)->orderBy('sequence');
    }
}
