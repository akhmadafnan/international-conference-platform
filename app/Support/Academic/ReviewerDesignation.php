<?php

namespace App\Support\Academic;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

final class ReviewerDesignation
{
    public const ROLE = 'reviewer';

    /** @return Builder<User> */
    public static function verifiedUsers(string $editionId): Builder
    {
        return User::query()->whereNotNull('email_verified_at')
            ->whereIn('id', self::assignments($editionId)->select('model_has_roles.model_id'));
    }

    public static function assigned(User $user, string $editionId): bool
    {
        return self::assignments($editionId)->where('model_has_roles.model_id', $user->id)
            ->lockForUpdate()->first(['model_has_roles.role_id']) !== null;
    }

    private static function assignments(string $editionId): QueryBuilder
    {
        // Check both role and pivot Edition explicitly, independent of cached team relations.
        return DB::table('model_has_roles')->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', self::ROLE)->where('roles.guard_name', 'web')
            ->where('roles.conference_edition_id', $editionId)
            ->where('model_has_roles.conference_edition_id', $editionId)
            ->whereNotExists(fn (QueryBuilder $query) => $query->selectRaw('1')->from('role_has_permissions')
                ->whereColumn('role_has_permissions.role_id', 'roles.id'))
            ->where('model_has_roles.model_type', (new User)->getMorphClass());
    }
}
