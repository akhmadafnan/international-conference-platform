<?php

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
});

test('guests cannot access the admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('unverified superadmins must verify their email', function () {
    $this->actingAs(User::factory()->superAdmin()->unverified()->create())
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('verification.notice'));
});

test('ordinary users receive forbidden on the admin dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('superadmins can access the admin dashboard with an empty database', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->where('auth.canAccessAdmin', true)
            ->where('editionCount', 0)
            ->has('recentEditions', 0));
});

test('superadmins opening the participant dashboard are redirected', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('dashboard'))
        ->assertRedirect(route('admin.dashboard'));
});

test('ordinary users retain the participant dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('auth.canAccessAdmin', false)
            ->where('phase02.registration', null));
});

test('the admin dashboard counts all editions and shows only the ten newest with real series data', function () {
    $series = ConferenceSeries::query()->create([
        'code' => 'ICHES',
        'name' => 'International Conference on Humanity Education and Society',
        'status' => 'ACTIVE',
    ]);
    $otherSeries = ConferenceSeries::query()->create([
        'code' => 'OTHER',
        'name' => 'Other Conference Series',
        'status' => 'ACTIVE',
    ]);

    foreach (range(1, 12) as $index) {
        ConferenceEdition::query()->create([
            'series_id' => $index === 12 ? $otherSeries->id : $series->id,
            'edition_code' => 'EDITION-'.$index,
            'year' => 2027,
            'host_name' => 'Conference Host',
            'mode' => 'OFFLINE',
            'timezone' => 'Asia/Jakarta',
            'starts_at' => '2027-06-01 00:00:00',
            'ends_at' => '2027-06-02 00:00:00',
            'lifecycle_status' => 'DRAFT',
            'created_at' => now()->subDays(13 - $index),
        ]);
    }

    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->where('editionCount', 12)
            ->has('recentEditions', 10)
            ->where('recentEditions.0.code', 'EDITION-12')
            ->where('recentEditions.0.seriesName', 'Other Conference Series')
            ->where('recentEditions.0.year', 2027)
            ->where('recentEditions.0.status', 'DRAFT')
            ->where('recentEditions.1.seriesName', $series->name)
            ->where('recentEditions.9.code', 'EDITION-3'));
});
