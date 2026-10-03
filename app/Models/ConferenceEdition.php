<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function series(): BelongsTo
    {
        return $this->belongsTo(ConferenceSeries::class, 'series_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(EditionMembership::class, 'edition_id');
    }

    public function paymentDestinations(): HasMany
    {
        return $this->hasMany(PaymentDestination::class, 'edition_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'edition_id');
    }

    public function participationPackages(): HasMany
    {
        return $this->hasMany(ParticipationPackage::class, 'edition_id');
    }

    public function workflowWindows(): HasMany
    {
        return $this->hasMany(EditionWorkflowWindow::class, 'edition_id');
    }

    public function numberSequences(): HasMany
    {
        return $this->hasMany(NumberSequence::class, 'edition_id');
    }
}
