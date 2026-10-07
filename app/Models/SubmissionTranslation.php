<?php

namespace App\Models;

use App\Enums\Locale;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionTranslation extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'locale',
        'title',
        'subtitle',
        'abstract_text',
    ];

    protected function casts(): array
    {
        return [
            'locale' => Locale::class,
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
