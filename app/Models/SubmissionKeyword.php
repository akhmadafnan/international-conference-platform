<?php

namespace App\Models;

use App\Enums\Locale;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionKeyword extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'locale',
        'value',
        'sequence',
    ];

    protected function casts(): array
    {
        return [
            'locale' => Locale::class,
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
}
