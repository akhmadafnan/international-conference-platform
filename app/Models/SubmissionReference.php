<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionReference extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'sequence',
        'raw_citation',
        'doi',
        'url',
        'structured_json',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'structured_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Submission, $this>
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
