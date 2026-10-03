<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\FoundationAccessSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;

test('foundation access seeder creates canonical permissions idempotently', function () {
    $permissions = config('foundation.permissions');

    expect($permissions)
        ->toBeArray()
        ->and($permissions)
        ->not->toBeEmpty()
        ->and(array_unique($permissions))
        ->toHaveCount(count($permissions));

    $this->seed(FoundationAccessSeeder::class);
    $this->seed(FoundationAccessSeeder::class);

    expect(
        Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->all(),
    )->toBe(
        collect($permissions)
            ->sort()
            ->values()
            ->all(),
    );
});

test('foundation does not create global edition business role rows', function () {
    $this->seed(FoundationAccessSeeder::class);

    $roleBlueprints = config(
        'foundation.edition_role_blueprints',
    );

    expect($roleBlueprints)
        ->toBeArray()
        ->and(array_keys($roleBlueprints))
        ->not->toContain(
            'super_admin',
            'reviewer',
            'session_chair',
            'moderator',
            'presenter',
        )
        ->and(Role::query()->count())
        ->toBe(0);

    foreach ($roleBlueprints as $permissions) {
        foreach ($permissions as $permission) {
            expect(config('foundation.permissions'))
                ->toContain($permission);
        }
    }
});

test('database seeder does not create laravel default test user', function () {
    config([
        'foundation.super_admin.enabled' => false,
    ]);

    $this->seed(DatabaseSeeder::class);

    expect(User::query()->count())
        ->toBe(0)
        ->and(Permission::query()->count())
        ->toBe(count(config('foundation.permissions')));
});

test('trusted bootstrap creates one verified global superadmin', function () {
    config([
        'foundation.super_admin.enabled' => true,
        'foundation.super_admin.name' => 'Initial Administrator',
        'foundation.super_admin.email' => 'admin@example.test',
        'foundation.super_admin.password' => 'Conference#2026Admin',
    ]);

    $this->seed(SuperAdminSeeder::class);

    $user = User::query()
        ->where('email', 'admin@example.test')
        ->sole();

    expect($user->isSuperAdmin())
        ->toBeTrue()
        ->and($user->email_verified_at)
        ->not->toBeNull()
        ->and(Hash::check(
            'Conference#2026Admin',
            $user->password,
        ))
        ->toBeTrue()
        ->and(Activity::query()
            ->where('event', 'superadmin_bootstrapped')
            ->count())
        ->toBe(1);

    $originalId = $user->id;
    $originalPassword = $user->password;

    $this->seed(SuperAdminSeeder::class);

    $user->refresh();

    expect(User::query()->count())
        ->toBe(1)
        ->and($user->id)
        ->toBe($originalId)
        ->and($user->password)
        ->toBe($originalPassword)
        ->and(Activity::query()
            ->where('event', 'superadmin_bootstrapped')
            ->count())
        ->toBe(1);
});

test('trusted bootstrap may promote an existing user without resetting password', function () {
    $user = User::factory()
        ->unverified()
        ->create([
            'email' => 'existing@example.test',
            'password' => 'Existing#2026Password',
        ]);

    $originalPassword = $user->password;

    config([
        'foundation.super_admin.enabled' => true,
        'foundation.super_admin.name' => null,
        'foundation.super_admin.email' => 'existing@example.test',
        'foundation.super_admin.password' => null,
    ]);

    $this->seed(SuperAdminSeeder::class);

    $user->refresh();

    expect($user->isSuperAdmin())
        ->toBeTrue()
        ->and($user->email_verified_at)
        ->not->toBeNull()
        ->and($user->password)
        ->toBe($originalPassword)
        ->and(Activity::query()
            ->where('event', 'superadmin_promoted')
            ->count())
        ->toBe(1);
});

test('trusted bootstrap has no implicit fallback credentials', function () {
    config([
        'foundation.super_admin.enabled' => true,
        'foundation.super_admin.name' => null,
        'foundation.super_admin.email' => 'missing@example.test',
        'foundation.super_admin.password' => null,
    ]);

    expect(
        fn () => $this->seed(SuperAdminSeeder::class),
    )->toThrow(ValidationException::class);

    expect(User::query()->count())->toBe(0);
});
