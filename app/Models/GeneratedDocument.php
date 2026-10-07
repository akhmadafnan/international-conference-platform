<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class GeneratedDocument extends Model
{
    use HasUuids;

    public const TYPE_EVENT_PASS = 'EVENT_PASS';

    public const STATUS_ISSUED = 'ISSUED';

    public const STATUS_REVOKED = 'REVOKED';

    public const STATUS_SUPERSEDED = 'SUPERSEDED';

    protected $fillable = [
        'edition_id',
        'document_type',
        'registration_id',
        'submission_id',
        'recipient_user_id',
        'status',
        'document_number',
        'snapshot_json',
        'stored_file_id',
        'issued_at',
        'revoked_at',
        'supersedes_document_id',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_json' => 'array',
            'issued_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<ConferenceEdition, $this>
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(ConferenceEdition::class, 'edition_id');
    }

    /**
     * @return BelongsTo<Registration, $this>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * @return BelongsTo<StoredFile, $this>
     */
    public function storedFile(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class);
    }

    /**
     * @return BelongsTo<GeneratedDocument, $this>
     */
    public function supersedesDocument(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supersedes_document_id');
    }

    /**
     * @return BelongsTo<Submission, $this>
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * @return MorphMany<VerificationToken, $this>
     */
    public function verificationTokens(): MorphMany
    {
        return $this->morphMany(VerificationToken::class, 'subject');
    }
}
