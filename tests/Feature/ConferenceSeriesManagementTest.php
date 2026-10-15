<?php

use App\Models\ConferenceSeries;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->withoutVite();
});

test('series routes require authentication', function () {
    $this->get(route('admin.series.index'))->assertRedirect(route('login'));
    $this->get(route('admin.series.create'))->assertRedirect(route('login'));
    $this->post(route('admin.series.store'), [])->assertRedirect(route('login'));
});

test('series routes require verified email', function () {
    $this->actingAs(User::factory()->superAdmin()->unverified()->create());

    $this->get(route('admin.series.index'))->assertRedirect(route('verification.notice'));
    $this->get(route('admin.series.create'))->assertRedirect(route('verification.notice'));
    $this->post(route('admin.series.store'), [])->assertRedirect(route('verification.notice'));
    $this->assertDatabaseCount('conference_series', 0);
});

test('ordinary users are forbidden on every series route before validation', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.series.index'))->assertForbidden();
    $this->get(route('admin.series.create'))->assertForbidden();
    $this->post(route('admin.series.store'), [])->assertForbidden();
    $this->assertDatabaseCount('conference_series', 0);
    expect(Activity::query()->where('event', 'conference_series_created')->count())->toBe(0);
});

test('superadmins can open the empty series list and create form', function () {
    $this->actingAs(User::factory()->superAdmin()->create());

    $this->get(route('admin.series.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/series/Index')
            ->where('auth.canAccessAdmin', true)
            ->has('series.data', 0));

    $this->get(route('admin.series.create'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/series/Create'));
});

test('series list returns real Eloquent records', function () {
    $series = ConferenceSeries::query()->create([
        'code' => 'ICHES',
        'name' => 'International Conference on Humanity Education and Society',
        'acronym' => 'ICHES',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.series.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('series.data', 1)
            ->where('series.data.0.id', $series->id)
            ->where('series.data.0.code', 'ICHES')
            ->where('series.data.0.name', $series->name)
            ->where('series.data.0.acronym', 'ICHES')
            ->where('series.data.0.status', 'ACTIVE'));
});

test('series creation validates required fields without writing data or audit', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.series.store'), ['code' => '   ', 'name' => ''])
        ->assertSessionHasErrors(['code', 'name']);

    $this->assertDatabaseCount('conference_series', 0);
    expect(Activity::query()->where('event', 'conference_series_created')->count())->toBe(0);
});

test('series creation validates field types lengths and description languages', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.series.store'), [
            'code' => str_repeat('A', 256),
            'name' => ['invalid'],
            'acronym' => str_repeat('A', 256),
            'about_i18n' => ['id' => str_repeat('A', 10001), 'en' => ['invalid'], 'fr' => 'Unsupported'],
        ])
        ->assertSessionHasErrors(['code', 'name', 'acronym', 'about_i18n', 'about_i18n.id', 'about_i18n.en']);

    $this->assertDatabaseCount('conference_series', 0);
});

test('duplicate series codes are rejected after normalization', function () {
    ConferenceSeries::query()->create(['code' => 'ICHES', 'name' => 'Existing', 'status' => 'ACTIVE']);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.series.store'), ['code' => '  iches  ', 'name' => 'Duplicate'])
        ->assertSessionHasErrors(['code' => 'duplicate']);

    $this->assertDatabaseCount('conference_series', 1);
    expect(Activity::query()->where('event', 'conference_series_created')->count())->toBe(0);
});

test('series creation normalizes the code sets active and audits the actor and subject', function () {
    $actor = User::factory()->superAdmin()->create();
    $about = ['id' => 'Konferensi berkala', 'en' => 'Recurring conference', 'ar' => 'مؤتمر دوري'];

    $this->actingAs($actor)
        ->post(route('admin.series.store'), [
            'code' => '  iches  ',
            'name' => 'International Conference',
            'acronym' => 'ICHES',
            'about_i18n' => $about,
            'status' => 'ARCHIVED',
            'logo_file_id' => 'untrusted',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.series.index'))
        ->assertSessionHas('series_created', true);

    $series = ConferenceSeries::query()->sole();
    expect($series->code)->toBe('ICHES')
        ->and($series->name)->toBe('International Conference')
        ->and($series->acronym)->toBe('ICHES')
        ->and($series->about_i18n)->toBe($about)
        ->and($series->status)->toBe('ACTIVE')
        ->and($series->logo_file_id)->toBeNull();

    $audit = Activity::query()->where('event', 'conference_series_created')->sole();
    expect($audit->log_name)->toBe('conference')
        ->and($audit->causer_id)->toBe($actor->id)
        ->and($audit->causer_type)->toBe($actor->getMorphClass())
        ->and($audit->subject_id)->toBe($series->id)
        ->and($audit->subject_type)->toBe($series->getMorphClass())
        ->and($audit->properties->get('code'))->toBe('ICHES')
        ->and($audit->properties->get('status'))->toBe('ACTIVE');
});

test('optional series fields may be omitted', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.series.store'), ['code' => 'minimal', 'name' => 'Minimal Series'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.series.index'));

    $this->assertDatabaseHas('conference_series', [
        'code' => 'MINIMAL',
        'name' => 'Minimal Series',
        'acronym' => null,
        'about_i18n' => null,
        'status' => 'ACTIVE',
    ]);
});
