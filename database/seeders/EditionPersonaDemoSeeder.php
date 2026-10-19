<?php

namespace Database\Seeders;

use App\Models\ConferenceEdition;
use App\Models\EditionMembership;
use App\Models\Role;
use App\Models\User;
use App\Support\Workspace\SuperAdminWorkspaceMode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
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
        $password = (string) config('demo_roles.password');

        Validator::make(
            ['edition' => $code, 'password' => $password],
            [
                'edition' => ['required', 'string', 'max:255'],
                'password' => [
                    'required', 'string',
                    Password::min(12)->mixedCase()->numbers()->symbols(),
                ],
            ],
        )->validate();

        $edition = ConferenceEdition::query()->where('edition_code', $code)->sole();
        $blueprints = config('foundation.edition_role_blueprints', []);

        if (! is_array($blueprints)) {
            throw new RuntimeException('Invalid canonical edition role blueprints.');
        }

        $this->call(FoundationAccessSeeder::class);

        $previousTeamId = getPermissionsTeamId();

        try {
            setPermissionsTeamId($edition->id);
            DB::transaction(function () use ($edition, $blueprints, $password): void {
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

                    $email = sprintf(
                        'demo+%s.%s@example.test',
                        str_replace('_', '-', $persona),
                        strtolower($edition->edition_code),
                    );
                    $user = User::query()->where('email', $email)->first();

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
