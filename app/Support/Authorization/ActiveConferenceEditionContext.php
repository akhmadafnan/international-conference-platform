<?php

namespace App\Support\Authorization;

use Illuminate\Support\Str;
use InvalidArgumentException;

final class ActiveConferenceEditionContext
{
    public const SESSION_KEY = 'active_conference_edition_id';

    private ?string $editionId = null;

    public function activate(string $editionId): void
    {
        if (! Str::isUuid($editionId, 7)) {
            throw new InvalidArgumentException(
                'Active Conference Edition ID must be a UUIDv7.'
            );
        }

        $this->editionId = $editionId;
    }

    public function clear(): void
    {
        $this->editionId = null;
    }

    public function id(): ?string
    {
        return $this->editionId;
    }

    public function hasActiveEdition(): bool
    {
        return $this->editionId !== null;
    }
}
