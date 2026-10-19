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

test('superadmins receive server paginated series with accurate ranges and navigation', function (
    int $total,
    int $currentPage,
    int $count,
    ?int $from,
    ?int $to,
    int $lastPage,
) {
    // Deliberately insert in reverse order to verify the query's code ordering.
    for ($number = $total; $number >= 1; $number--) {
        ConferenceSeries::query()->create([
            'code' => sprintf('SERIES-%03d', $number),
            'name' => 'Series '.$number,
            'status' => 'ACTIVE',
        ]);
    }

    $urlFor = fn (int $page): string => route('admin.series.index', ['page' => $page]);

    $this->actingAs(User::factory()->superAdmin()->create())
        ->get($urlFor($currentPage))->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/series/Index')
            ->where('auth.canAccessAdmin', true)
            ->has('series.data', $count)
            ->where('series.total', $total)
            ->where('series.from', $from)
            ->where('series.to', $to)
            ->where('series.perPage', 20)
            ->where('series.currentPage', $currentPage)
            ->where('series.lastPage', $lastPage)
            ->where('series.firstUrl', $currentPage > 1 ? $urlFor(1) : null)
            ->where('series.lastUrl', $currentPage < $lastPage ? $urlFor($lastPage) : null)
            ->where('series.previousUrl', $currentPage > 1 ? $urlFor($currentPage - 1) : null)
            ->where('series.nextUrl', $currentPage < $lastPage ? $urlFor($currentPage + 1) : null)
            ->has('series.pages', $lastPage)
            ->where('series.pages.0.page', 1)
            ->where('series.pages.0.url', $urlFor(1))
            ->where('series.data', function ($items) use ($count, $from) {
                $expected = $count === 0
                    ? []
                    : array_map(fn (int $number): string => sprintf('SERIES-%03d', $number), range($from, $from + $count - 1));

                return collect($items)->pluck('code')->all() === $expected;
            }));
})->with([
    'empty' => [0, 1, 0, null, null, 1],
    'one entry' => [1, 1, 1, 1, 1, 1],
    'full page' => [20, 1, 20, 1, 20, 1],
    'first of two pages' => [21, 1, 20, 1, 20, 2],
    'last of two pages' => [21, 2, 1, 21, 21, 2],
]);

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
