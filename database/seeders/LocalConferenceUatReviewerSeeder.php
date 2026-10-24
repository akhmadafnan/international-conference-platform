<?php

namespace Database\Seeders;

use App\Models\ConferenceEdition;
use App\Models\Role;
use App\Models\User;
use App\Support\Academic\ReviewerDesignation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Spatie\Permission\PermissionRegistrar;

final class LocalConferenceUatReviewerSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) || ! config('demo_roles.enabled')) {
            throw new RuntimeException('Explicit local/testing demo opt-in required.');
        }
        $previousTeamId = getPermissionsTeamId();
        try {
            DB::transaction(function (): void {
                $edition = ConferenceEdition::query()->where('edition_code', LocalConferenceUatSeeder::EDITION_CODE)->lockForUpdate()->first();
                if ($edition === null || ($edition->settings_json['uat_only'] ?? false) !== true) {
                    throw new RuntimeException('Existing marked UAT Edition required.');
                }
                $user = User::query()->where('email', 'reviewer@iches.test')->lockForUpdate()->first();
                if ($user === null || ! $user->hasVerifiedEmail() || $user->isSuperAdmin() || $user->name !== 'ICHES Demo · reviewer') {
                    throw new RuntimeException('Existing verified demo reviewer identity required.');
                }
                setPermissionsTeamId($edition->id);
                $role = Role::query()->firstOrCreate([
                    'conference_edition_id' => $edition->id, 'name' => ReviewerDesignation::ROLE, 'guard_name' => 'web',
                ]);
                if ($role->permissions()->exists()) {
                    throw new RuntimeException('Reviewer designation must have no permissions; existing role left unchanged.');
                }
                if (! ReviewerDesignation::assigned($user, $edition->id)) {
                    $user->assignRole($role);
                    activity('academic')->performedOn($user)->event('reviewer_designated')->withProperties([
                        'conference_edition_id' => $edition->id, 'role_id' => $role->id,
                        'source' => self::class,
                    ])->log('reviewer_designated');
                }
            }, 3);
        } finally {
            setPermissionsTeamId($previousTeamId);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}
