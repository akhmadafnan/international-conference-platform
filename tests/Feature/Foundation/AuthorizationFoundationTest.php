<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

test('roles permissions and users use UUIDv7 identifiers', function () {
    $editionId = (string) Str::uuid7();

    setPermissionsTeamId($editionId);

    $user = User::factory()->create();

    $permission = Permission::create([
        'name' => 'payments.verify',
        'guard_name' => 'web',
    ]);

    $role = Role::create([
        'name' => 'finance',
        'guard_name' => 'web',
    ]);

    expect(Str::isUuid($user->id, 7))->toBeTrue()
        ->and(Str::isUuid($role->id, 7))->toBeTrue()
        ->and(Str::isUuid($permission->id, 7))->toBeTrue();
});

test('role assignment is scoped to the active conference edition', function () {
    $editionA = (string) Str::uuid7();
    $editionB = (string) Str::uuid7();

    $user = User::factory()->create();

    Permission::create([
        'name' => 'payments.verify',
        'guard_name' => 'web',
    ]);

    setPermissionsTeamId($editionA);

    $role = Role::create([
        'name' => 'finance',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo('payments.verify');
    $user->assignRole($role);

    $user->unsetRelation('roles')
        ->unsetRelation('permissions');

    expect($user->can('payments.verify'))->toBeTrue();

    setPermissionsTeamId($editionB);

    $user->unsetRelation('roles')
        ->unsetRelation('permissions');

    expect($user->can('payments.verify'))->toBeFalse();

    setPermissionsTeamId($editionA);

    $user->unsetRelation('roles')
        ->unsetRelation('permissions');

    expect($user->can('payments.verify'))->toBeTrue();
});

test('activity log supports UUIDv7 causer and subject', function () {
    $editionId = (string) Str::uuid7();

    setPermissionsTeamId($editionId);

    $user = User::factory()->create();

    $role = Role::create([
        'name' => 'reviewer',
        'guard_name' => 'web',
    ]);

    $activity = activity('security')
        ->causedBy($user)
        ->performedOn($role)
        ->event('role_assigned')
        ->withProperties([
            'conference_edition_id' => $editionId,
        ])
        ->log('Edition role assigned');

    $activity->refresh();

    expect((string) $activity->causer_id)->toBe($user->id)
        ->and((string) $activity->subject_id)->toBe($role->id)
        ->and(Str::isUuid((string) $activity->causer_id, 7))->toBeTrue()
        ->and(Str::isUuid((string) $activity->subject_id, 7))->toBeTrue()
        ->and($activity->causer)->toBeInstanceOf(User::class)
        ->and($activity->subject)->toBeInstanceOf(Role::class);
});
