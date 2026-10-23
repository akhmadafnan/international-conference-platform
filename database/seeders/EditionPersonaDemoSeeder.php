<?php

namespace Database\Seeders;

use App\Models\ConferenceEdition;
use App\Models\EditionMembership;
use App\Models\Role;
use App\Models\User;
use App\Support\Workspace\SuperAdminWorkspaceMode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Spatie\Permission\PermissionRegistrar;

final class EditionPersonaDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('demo_roles.enabled')) {
            throw new RuntimeException('Demo role seeding is disabled. Opt in explicitly.');
        }
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo role seeding is prohibited outside local/testing.');
        }

        $code = trim((string) config('demo_roles.edition_code'));
        $password = 'password';

        Validator::make(
            ['edition' => $code],
            [
                'edition' => ['required', 'string', 'max:255'],
            ],
        )->validate();

        $edition = ConferenceEdition::query()->where('edition_code', $code)->sole();
        if ($code === LocalConferenceUatSeeder::EDITION_CODE && ($edition->settings_json['uat_only'] ?? false) !== true) {
            throw new RuntimeException('UAT Edition must have the uat_only marker.');
        }
        $blueprints = config('foundation.edition_role_blueprints', []);

        if (! is_array($blueprints)) {
            throw new RuntimeException('Invalid canonical edition role blueprints.');
        }

        $previousTeamId = getPermissionsTeamId();

        try {
            setPermissionsTeamId($edition->id);
            DB::transaction(function () use ($edition, $blueprints, $password): void {
                $this->call(FoundationAccessSeeder::class);
                foreach ($blueprints as $name => $abilities) {
                    $role = Role::query()
                        ->where('conference_edition_id', $edition->id)
                        ->where('name', $name)
                        ->where('guard_name', 'web')
                        ->first();

                    if (! $role instanceof Role) {
                        $role = new Role;
                        $role->name = $name;
                        $role->guard_name = 'web';
                        $role->setAttribute('conference_edition_id', $edition->id);
                        $role->save();
                        $role->syncPermissions($abilities);
                    }
                }

                foreach (SuperAdminWorkspaceMode::available() as $persona) {
                    if ($persona === 'superadmin') {
                        continue;
                    }

                    $legacyEmail = sprintf(
                        'demo+%s.%s@example.test',
                        str_replace('_', '-', $persona),
                        strtolower($edition->edition_code),
                    );
                    $email = $edition->edition_code === LocalConferenceUatSeeder::EDITION_CODE
                        ? str_replace('_', '-', $persona).'@iches.test'
                        : sprintf('demo+%s.%s@iches.test', str_replace('_', '-', $persona), strtolower($edition->edition_code));
                    $legacyUser = User::query()->where('email', $legacyEmail)->lockForUpdate()->first();
                    $targetUser = User::query()->where('email', $email)->lockForUpdate()->first();

                    if ($legacyUser instanceof User && $targetUser instanceof User && $legacyUser->id !== $targetUser->id) {
                        throw new RuntimeException("Demo email collision between legacy and target identities for {$persona}; aborting without takeover.");
                    }

                    $user = $legacyUser ?? $targetUser;

                    if ($user instanceof User && (
                        $user->isSuperAdmin() || $user->name !== 'ICHES Demo · '.$persona
                    )) {
                        throw new RuntimeException('Demo account name collision; aborting without takeover.');
                    }

                    if (! $user instanceof User) {
                        $user = new User([
                            'name' => 'ICHES Demo · '.$persona,
                            'email' => $email,
                            'password' => $password,
                        ]);
                        $user->email_verified_at = now();
                        $user->save();
                    } else {
                        // The exact legacy address binds its demo name marker to this Edition.
                        // Personas with persistent Edition relationships must retain that evidence.
                        if (array_key_exists($persona, $blueprints) && ! $user->roles()
                            ->where('roles.conference_edition_id', $edition->id)->where('name', $persona)->exists()) {
                            throw new RuntimeException('Demo identity has no matching Edition role; aborting without takeover.');
                        }
                        if (in_array($persona, ['participant', 'non_presenting_participant', 'author', 'co_author', 'presenter', 'invited_speaker'], true)
                            && ! EditionMembership::query()->where('edition_id', $edition->id)->where('user_id', $user->id)->exists()) {
                            throw new RuntimeException('Demo identity has no matching Edition membership; aborting without takeover.');
                        }
                        $user->email = $email;
                        if (! Hash::check($password, $user->password)) {
                            $user->password = $password;
                        }
                        if ($user->isDirty()) {
                            $user->save();
                        }
                    }

                    if (array_key_exists($persona, $blueprints)) {
                        // Strictly limited to this Edition; never a global role.
                        if (! $user->hasRole($persona)) {
                            $user->assignRole($persona);
                        }
                    }

                    if (in_array($persona, [
                        'participant', 'non_presenting_participant', 'author',
                        'co_author', 'presenter', 'invited_speaker',
                    ], true)) {
                        EditionMembership::query()->firstOrCreate(
                            ['edition_id' => $edition->id, 'user_id' => $user->id],
                            ['membership_status' => 'ACTIVE', 'joined_at' => now()],
                        );
                    }
                    // Reviewer/Presenter/Author assignment and actual submission
                    // data must be created by their domain actions, never fabricated.
                }
            });
        } finally {
            setPermissionsTeamId($previousTeamId);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}
