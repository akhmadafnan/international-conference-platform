<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $edition_code
 */
class ConferenceEdition extends Model
{
    use HasUuids;

    protected $fillable = [
        'series_id',
        'edition_code',
        'edition_number',
        'year',
        'theme_i18n',
        'host_name',
        'organizer_i18n',
        'mode',
        'timezone',
        'starts_at',
        'ends_at',
        'registration_opens_at',
        'registration_closes_at',
        'schedule_published_at',
        'lifecycle_status',
        'settings_json',
    ];

    protected function casts(): array
    {
        return [
            'edition_number' => 'integer',
            'year' => 'integer',
            'theme_i18n' => 'array',
            'organizer_i18n' => 'array',
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'registration_opens_at' => 'immutable_datetime',
            'registration_closes_at' => 'immutable_datetime',
            'schedule_published_at' => 'immutable_datetime',
            'settings_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ConferenceSeries, $this>
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(ConferenceSeries::class, 'series_id');
    }

    /**
     * @return HasMany<EditionMembership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(EditionMembership::class, 'edition_id');
    }

    /**
     * @return HasMany<PaymentDestination, $this>
     */
    public function paymentDestinations(): HasMany
    {
        return $this->hasMany(PaymentDestination::class, 'edition_id');
    }

    /**
     * @return HasMany<Activity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'edition_id');
    }

    /**
     * @return HasMany<ParticipationPackage, $this>
     */
    public function participationPackages(): HasMany
    {
        return $this->hasMany(ParticipationPackage::class, 'edition_id');
    }

    /**
     * @return HasMany<EditionWorkflowWindow, $this>
     */
    public function workflowWindows(): HasMany
    {
        return $this->hasMany(EditionWorkflowWindow::class, 'edition_id');
    }

    /**
     * @return HasMany<NumberSequence, $this>
     */
    public function numberSequences(): HasMany
    {
        return $this->hasMany(NumberSequence::class, 'edition_id');
    }

    /**
     * @return HasMany<ImportantDate, $this>
     */
    public function importantDates(): HasMany
    {
        return $this->hasMany(ImportantDate::class, 'edition_id');
    }

    /**
     * @return HasMany<Track, $this>
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class, 'edition_id');
    }

    /**
     * @return HasMany<GeneratedDocument, $this>
     */
    public function generatedDocuments(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class, 'edition_id');
    }

    /**
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'edition_id');
    }
}
