<?php

namespace App\Support\Authorization;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

final class EditionScopedAuthorizer
{
    public function __construct(
        private readonly ActiveConferenceEditionContext $editionContext,
    ) {}

    public function authorize(
        User $actor,
        string $ability,
        string $editionId,
    ): void {
        Gate::forUser($actor)->authorize($ability);

        if ($actor->isSuperAdmin()) {
            return;
        }

        if (
            $this->editionContext->id() !== $editionId
            || getPermissionsTeamId() !== $editionId
        ) {
            throw new AuthorizationException(
                'The active Conference Edition does not match the target resource.',
            );
        }
    }
}
