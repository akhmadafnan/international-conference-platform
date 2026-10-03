<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $edition_id
 * @property string $code
 * @property string $label
 * @property string $bank_name
 * @property string $account_number
 * @property string $account_holder
 * @property array<string, string>|null $instructions_i18n
 * @property bool $is_default
 * @property bool $active
 */
class PaymentDestination extends Model
{
    use HasUuids;

    protected $fillable = [
        'edition_id',
        'code',
        'label',
        'bank_name',
        'account_number',
        'account_holder',
        'instructions_i18n',
        'is_default',
        'active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'instructions_i18n' => 'array',
            'is_default' => 'boolean',
            'active' => 'boolean',
            'display_order' => 'integer',
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
     * @return HasMany<ParticipationPackage, $this>
     */
    public function packages(): HasMany
    {
        return $this->hasMany(ParticipationPackage::class, 'payment_destination_id');
    }
}
