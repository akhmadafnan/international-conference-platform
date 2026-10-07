<?php

namespace App\Models;

use App\Enums\Locale;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'registration_id',
        'paper_code',
        'track_id',
        'primary_locale',
        'academic_status',
        'current_abstract_version',
        'actual_presenter_contributor_id',
        'submitted_at',
        'accepted_at',
        'rejected_at',
        'final_academic_approved_at',
    ];

    protected function casts(): array
    {
        return [
            'primary_locale' => Locale::class,
            'current_abstract_version' => 'integer',
            'submitted_at' => 'immutable_datetime',
            'accepted_at' => 'immutable_datetime',
            'rejected_at' => 'immutable_datetime',
            'final_academic_approved_at' => 'immutable_datetime',
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
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    /**
     * @return BelongsTo<Track, $this>
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class, 'track_id');
    }

    /**
     * @return HasMany<SubmissionTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SubmissionTranslation::class);
    }

    /**
     * @return HasOne<SubmissionTranslation, $this>
     */
    public function primaryTranslation(): HasOne
    {
        return $this->hasOne(SubmissionTranslation::class)
            ->where('locale', (string) $this->getRawOriginal('primary_locale'));
    }

    /**
     * @return HasMany<SubmissionKeyword, $this>
     */
    public function keywords(): HasMany
    {
        return $this->hasMany(SubmissionKeyword::class);
    }

    /**
     * @return HasMany<SubmissionContributor, $this>
     */
    public function contributors(): HasMany
    {
        return $this->hasMany(SubmissionContributor::class)->orderBy('sequence');
    }

    /**
     * @return HasOne<SubmissionContributor, $this>
     */
    public function correspondingAuthor(): HasOne
    {
        return $this->hasOne(SubmissionContributor::class)
            ->where('is_corresponding', true);
    }

    /**
     * @return BelongsTo<SubmissionContributor, $this>
     */
    public function actualPresenter(): BelongsTo
    {
        return $this->belongsTo(SubmissionContributor::class, 'actual_presenter_contributor_id');
    }

    /**
     * @return HasMany<SubmissionReference, $this>
     */
    public function references(): HasMany
    {
        return $this->hasMany(SubmissionReference::class)->orderBy('sequence');
    }

    /**
     * @return HasMany<SubmissionFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    /**
     * @return HasMany<SubmissionSnapshot, $this>
     */
    public function snapshots(): HasMany
    {
        return $this->hasMany(SubmissionSnapshot::class);
    }

    /**
     * @return HasMany<GeneratedDocument, $this>
     */
    public function generatedDocuments(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class);
    }
}
