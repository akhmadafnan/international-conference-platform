<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReviewAssignment extends Model
{
    use HasUuids;

    public const ABSTRACT_STAGE = 'ABSTRACT';

    public const ACTIVE_STATUSES = ['INVITED', 'ACCEPTED'];

    protected $attributes = ['academic_stage' => self::ABSTRACT_STAGE, 'assignment_source' => 'MANUAL'];

    protected $guarded = ['id', 'active_owner_slot'];

    protected function casts(): array
    {
        return ['same_affiliation' => 'boolean'];
    }

    /** @return BelongsTo<Submission, $this> */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /** @return BelongsTo<SubmissionSnapshot, $this> */
    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(SubmissionSnapshot::class, 'snapshot_id');
    }

    /** @return BelongsTo<User, $this> */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /** @return HasOne<AcademicReview, $this> */
    public function review(): HasOne
    {
        return $this->hasOne(AcademicReview::class, 'assignment_id');
    }
}
