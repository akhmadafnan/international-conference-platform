<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

test('registration accepts a simple six-character password', function () {
    $user = app(CreateNewUser::class)->create([
        'name' => 'Simple Author',
        'email' => 'simple-author@example.test',
        'password' => 'abcdef',
        'password_confirmation' => 'abcdef',
    ]);

    expect($user->exists)->toBeTrue()
        ->and(Hash::check('abcdef', $user->password))->toBeTrue();
});

test('registration rejects a five-character password', function () {
    expect(fn () => app(CreateNewUser::class)->create([
        'name' => 'Short Password',
        'email' => 'short@example.test',
        'password' => 'abcde',
        'password_confirmation' => 'abcde',
    ]))->toThrow(ValidationException::class);

    expect(User::query()->where('email', 'short@example.test')->exists())->toBeFalse();
});

test('reset action accepts a simple six-character password', function () {
    $user = User::factory()->create();

    app(ResetUserPassword::class)->reset($user, [
        'password' => 'abcdef',
        'password_confirmation' => 'abcdef',
    ]);

    expect(Hash::check('abcdef', $user->fresh()->password))->toBeTrue();
});

test('security settings accept a simple six-character password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'abcdef',
            'password_confirmation' => 'abcdef',
        ]);

    $response->assertSessionHasNoErrors();

    expect(Hash::check('abcdef', $user->fresh()->password))->toBeTrue();
});

test('security settings reject five characters without changing the old password', function () {
    $user = User::factory()->create();
    $originalHash = $user->password;

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'abcde',
            'password_confirmation' => 'abcde',
        ])
        ->assertSessionHasErrors('password');

    expect($user->fresh()->password)->toBe($originalHash);
});

test('trusted bootstrap accepts a six-character password without symbols', function () {
    config([
        'foundation.super_admin.enabled' => true,
        'foundation.super_admin.name' => 'Local Superadmin',
        'foundation.super_admin.email' => 'local-superadmin@example.test',
        'foundation.super_admin.password' => 'abcdef',
    ]);

    $this->seed(SuperAdminSeeder::class);

    $user = User::query()->where('email', 'local-superadmin@example.test')->sole();

    expect($user->isSuperAdmin())->toBeTrue()
        ->and(Hash::check('abcdef', $user->password))->toBeTrue();
});

test('trusted bootstrap rejects five-character password', function () {
    config([
        'foundation.super_admin.enabled' => true,
        'foundation.super_admin.name' => 'Local Superadmin',
        'foundation.super_admin.email' => 'local-superadmin@example.test',
        'foundation.super_admin.password' => 'abcde',
    ]);

    expect(fn () => $this->seed(SuperAdminSeeder::class))
        ->toThrow(ValidationException::class);

    expect(User::query()->where('email', 'local-superadmin@example.test')->exists())->toBeFalse();
});
