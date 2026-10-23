<?php

use App\Models\User;
use App\Support\Workspace\SuperAdminWorkspaceMode;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

test('verified superadmin may choose participant perspective without changing identity or permissions', function () {
    $this->withoutVite();
    $admin = User::factory()->superAdmin()->create(['email' => 'superadmin@unisya.ac.id']);

    $this->actingAs($admin)->post(route('workspace.mode.switch'), [
        'mode' => 'participant',
    ])->assertRedirect(route('dashboard'));

    $this->assertSame(
        'participant',
        session(SuperAdminWorkspaceMode::SESSION_KEY),
    );
    $this->actingAs($admin)->get(route('dashboard'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('auth.workspaceMode', 'participant')
            ->where('auth.user.email', 'superadmin@unisya.ac.id')
            ->where('auth.canAccessAdmin', true)
            ->where('phase02.registration', null));

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    expect($admin->fresh()->isSuperAdmin())->toBeTrue();
    expect(Activity::query()->where('event', 'superadmin_workspace_view_changed')->count())->toBe(1);
});

test('superadmin can open the real reviewer workspace without impersonating assignments', function () {
    $this->withoutVite();
    $admin = User::factory()->superAdmin()->create();
    $this->actingAs($admin)->post(route('workspace.mode.switch'), [
        'mode' => 'reviewer',
    ])->assertRedirect(route('academic.reviews'));

    $this->actingAs($admin)->get(route('academic.reviews'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('academic/Reviews')
            ->has('assignments', 0)
            ->where('auth.workspaceMode', 'reviewer'));
    $this->actingAs($admin)->post(route('workspace.mode.switch'), [
        'mode' => 'superadmin',
    ])->assertRedirect(route('admin.dashboard'));
    expect(session(SuperAdminWorkspaceMode::SESSION_KEY))->toBe('superadmin');
});

test('ordinary accounts cannot use superadmin switch endpoints', function () {
    $this->withoutVite();
    $user = User::factory()->create();
    $this->actingAs($user)->post(route('workspace.mode.switch'), [
        'mode' => 'finance',
    ])->assertForbidden();
    $this->actingAs($user)->get(route('workspace.overview'))->assertForbidden();
    expect(session()->has(SuperAdminWorkspaceMode::SESSION_KEY))->toBeFalse();
});

test('invalid mode never overwrites a permitted workspace view', function () {
    $admin = User::factory()->superAdmin()->create();
    $this->actingAs($admin)->withSession([
        SuperAdminWorkspaceMode::SESSION_KEY => 'participant',
    ])->post(route('workspace.mode.switch'), [
        'mode' => 'unknown',
    ])->assertSessionHasErrors('mode');
    expect(session(SuperAdminWorkspaceMode::SESSION_KEY))->toBe('participant');
});

test('superadmin switching to the same view does not duplicate audit history', function () {
    $admin = User::factory()->superAdmin()->create();
    $this->actingAs($admin)->post(route('workspace.mode.switch'), [
        'mode' => 'superadmin',
    ])->assertRedirect(route('admin.dashboard'));
    expect(Activity::query()->where('event', 'superadmin_workspace_view_changed')->count())->toBe(0);
});
