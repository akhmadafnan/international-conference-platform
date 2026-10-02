<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

beforeEach(function () {
    Route::middleware(['web', 'auth'])
        ->get('/_foundation/authz/context', function (
            Request $request,
            ActiveConferenceEditionContext $context,
        ) {
            $user = $request->user();

            return response()->json([
                'edition_id' => $context->id(),
                'team_id' => getPermissionsTeamId(),
                'is_super_admin' => $user instanceof User
                    && $user->isSuperAdmin(),
            ]);
        });

    Route::middleware(['web', 'auth', 'can:payments.verify'])
        ->get(
            '/_foundation/authz/payments-verify',
            static fn () => response()->noContent(),
        );

    Route::middleware([
        'web',
        'auth',
        'can:foundation.sensitive-action',
    ])->post(
        '/_foundation/authz/validated-action',
        function (Request $request) {
            $request->validate([
                'confirmation' => ['required', 'accepted'],
            ]);

            return response()->noContent();
        },
    );
});

test('superadmin flag defaults false and cannot be mass assigned', function () {
    $user = new User([
        'name' => 'Normal User',
        'email' => 'normal@example.test',
        'password' => 'password',
        'is_super_admin' => true,
    ]);

    expect($user->isSuperAdmin())->toBeFalse()
        ->and($user->getFillable())->not->toContain('is_super_admin');

    $persisted = User::factory()->create();

    expect($persisted->isSuperAdmin())->toBeFalse();
});

test('superadmin factory state creates global authority user', function () {
    $user = User::factory()->superAdmin()->create();

    expect($user->isSuperAdmin())->toBeTrue();
});

test('global gate bypass applies only to superadmin', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $normalUser = User::factory()->create();

    expect(
        Gate::forUser($superAdmin)
            ->allows('foundation.synthetic-ability'),
    )->toBeTrue()
        ->and(
            Gate::forUser($normalUser)
                ->allows('foundation.synthetic-ability'),
        )->toBeFalse();
});

test('permission middleware follows active conference edition context', function () {
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

    setPermissionsTeamId(null);

    $user->unsetRelation('roles')
        ->unsetRelation('permissions');

    $this->actingAs($user)
        ->withSession([
            ActiveConferenceEditionContext::SESSION_KEY => $editionA,
        ])
        ->get('/_foundation/authz/payments-verify')
        ->assertNoContent();

    expect(getPermissionsTeamId())->toBeNull();

    $this->withSession([
        ActiveConferenceEditionContext::SESSION_KEY => $editionB,
    ])
        ->get('/_foundation/authz/payments-verify')
        ->assertForbidden();

    expect(getPermissionsTeamId())->toBeNull();
});

test('superadmin crosses edition authorization without business roles', function () {
    $editionA = (string) Str::uuid7();
    $editionB = (string) Str::uuid7();

    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->withSession([
            ActiveConferenceEditionContext::SESSION_KEY => $editionA,
        ])
        ->get('/_foundation/authz/payments-verify')
        ->assertNoContent();

    expect(getPermissionsTeamId())->toBeNull();

    $this->withSession([
        ActiveConferenceEditionContext::SESSION_KEY => $editionB,
    ])
        ->get('/_foundation/authz/payments-verify')
        ->assertNoContent();

    expect(getPermissionsTeamId())->toBeNull();
});

test('invalid edition session context is rejected and cleared', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession([
            ActiveConferenceEditionContext::SESSION_KEY => 'invalid-edition',
        ])
        ->get('/_foundation/authz/context');

    $response
        ->assertOk()
        ->assertJson([
            'edition_id' => null,
            'team_id' => null,
            'is_super_admin' => false,
        ])
        ->assertSessionMissing(
            ActiveConferenceEditionContext::SESSION_KEY,
        );

    expect(getPermissionsTeamId())->toBeNull();
});

test('superadmin authorization does not bypass validation', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->post('/_foundation/authz/validated-action')
        ->assertSessionHasErrors('confirmation');

    $this->post('/_foundation/authz/validated-action', [
        'confirmation' => 'yes',
    ])->assertNoContent();
});
