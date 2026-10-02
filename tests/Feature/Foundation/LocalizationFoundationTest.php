<?php

use App\Enums\Locale;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('supported locale contract is id en ar with Arabic RTL only', function () {
    expect(Locale::values())->toBe([
        'id',
        'en',
        'ar',
    ])
        ->and(Locale::Indonesian->direction())->toBe('ltr')
        ->and(Locale::English->direction())->toBe('ltr')
        ->and(Locale::Arabic->direction())->toBe('rtl');
});

test('Arabic locale cookie applies server locale and RTL root document', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withCookie('locale', 'ar')
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('localization.locale', 'ar')
                ->where('localization.direction', 'rtl')
                ->has('localization.supportedLocales', 3),
        );
});

test('Indonesian locale remains LTR', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withCookie('locale', 'id')
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('lang="id"', false)
        ->assertSee('dir="ltr"', false)
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('localization.locale', 'id')
                ->where('localization.direction', 'ltr'),
        );
});

test('locale update persists a supported locale', function () {
    $this->from('/login')
        ->post('/locale', [
            'locale' => 'ar',
        ])
        ->assertStatus(303)
        ->assertRedirect('/login')
        ->assertCookie('locale', 'ar');
});

test('locale update rejects unsupported locale', function () {
    $this->from('/login')
        ->post('/locale', [
            'locale' => 'fr',
        ])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('locale');
});
