<?php

use App\Actions\Admin\CreateConferenceEditionAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->withoutVite();
    $this->series = ConferenceSeries::query()->create([
        'code' => 'ICHES-2026', 'name' => 'ICHES Conference Series', 'status' => 'ACTIVE',
    ]);
    $this->payload = [
        'series_id' => $this->series->id,
        'edition_code' => 'ICHES-2027',
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'Conference Host',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => '2027-06-01T09:00',
        'ends_at' => '2027-06-02T17:00',
        'theme_i18n' => ['id' => 'Tema', 'en' => 'Theme', 'ar' => 'الموضوع'],
        'organizer_i18n' => ['id' => 'Panitia', 'en' => 'Committee', 'ar' => 'اللجنة'],
    ];
});

test('edition routes protect guests unverified accounts and ordinary users', function (string $actor) {
    if ($actor === 'unverified') {
        $this->actingAs(User::factory()->superAdmin()->unverified()->create());
    } elseif ($actor === 'ordinary') {
        $this->actingAs(User::factory()->create());
    }

    $responses = [
        $this->get(route('admin.editions.index')),
        $this->get(route('admin.editions.create')),
        $this->post(route('admin.editions.store'), []),
    ];
    foreach ($responses as $response) {
        if ($actor === 'ordinary') {
            $response->assertForbidden();
        } else {
            $response->assertRedirect(route($actor === 'guest' ? 'login' : 'verification.notice'));
        }
    }
    $this->assertDatabaseCount('conference_editions', 0);
    expect(Activity::query()->where('event', 'conference_edition_created')->count())->toBe(0);
})->with(['guest', 'unverified', 'ordinary']);

test('superadmins can open the empty edition list and only active real series are offered', function () {
    ConferenceSeries::query()->create(['code' => 'OLD', 'name' => 'Archived', 'status' => 'ARCHIVED']);
    $this->actingAs(User::factory()->superAdmin()->create());

    $this->get(route('admin.editions.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/editions/Index')
            ->where('auth.canAccessAdmin', true)
            ->has('editions.data', 0)->where('editions.total', 0)
            ->where('editions.from', null)->where('editions.to', null)
            ->where('editions.perPage', 20)->where('editions.currentPage', 1)
            ->where('editions.lastPage', 1)->where('editions.previousUrl', null)
            ->where('editions.nextUrl', null)->where('created', false));

    $this->get(route('admin.editions.create'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/editions/Create')
            ->has('series', 1)->where('series.0.id', $this->series->id)
            ->where('series.0.code', 'ICHES-2026')->where('series.0.name', $this->series->name));
});

test('create form has an empty dropdown when there are no active series', function () {
    $this->series->update(['status' => 'ARCHIVED']);
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.editions.create'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/editions/Create')->has('series', 0));
});

test('creation stores draft UTC timestamps and audit without changing its series or opening workflows', function () {
    $actor = User::factory()->superAdmin()->create();
    $originalSeries = $this->series->getAttributes();
    $this->payload['edition_code'] = '  iches-2027  ';

    $this->actingAs($actor)->post(route('admin.editions.store'), $this->payload)
        ->assertSessionHasNoErrors()->assertRedirect(route('admin.editions.index'))
        ->assertSessionHas('edition_created', true);

    $edition = ConferenceEdition::query()->sole();
    expect($edition->edition_code)->toBe('ICHES-2027')
        ->and($edition->series_id)->toBe($this->series->id)
        ->and($edition->edition_number)->toBe(6)->and($edition->year)->toBe(2027)
        ->and($edition->host_name)->toBe('Conference Host')
        ->and($edition->mode)->toBe('OFFLINE')
        ->and($edition->timezone)->toBe('Asia/Jakarta')
        ->and($edition->starts_at->utc()->format('Y-m-d H:i:s'))->toBe('2027-06-01 02:00:00')
        ->and($edition->ends_at->utc()->format('Y-m-d H:i:s'))->toBe('2027-06-02 10:00:00')
        ->and($edition->theme_i18n)->toBe($this->payload['theme_i18n'])
        ->and($edition->organizer_i18n)->toBe($this->payload['organizer_i18n'])
        ->and($edition->lifecycle_status)->toBe('DRAFT')
        ->and($edition->registration_opens_at)->toBeNull()
        ->and($edition->registration_closes_at)->toBeNull()
        ->and($edition->schedule_published_at)->toBeNull()
        ->and($edition->settings_json)->toBeNull()
        ->and($this->series->fresh()->code)->toBe('ICHES-2026');

    // Eloquent may hydrate previously unset nullable columns; verify original
    // persisted values without depending on attribute insertion order.
    $persistedSeries = $this->series->fresh();
    foreach ($originalSeries as $key => $value) {
        expect($persistedSeries->getRawOriginal($key))->toBe($value);
    }

    $audit = Activity::query()->where('event', 'conference_edition_created')->sole();
    expect($audit->log_name)->toBe('conference')
        ->and($audit->causer_id)->toBe($actor->id)->and($audit->causer_type)->toBe($actor->getMorphClass())
        ->and($audit->subject_id)->toBe($edition->id)->and($audit->subject_type)->toBe($edition->getMorphClass())
        ->and($audit->properties->get('edition_code'))->toBe('ICHES-2027')
        ->and($audit->properties->get('series_id'))->toBe($this->series->id)
        ->and($audit->properties->get('lifecycle_status'))->toBe('DRAFT')
        ->and($audit->properties->get('timezone'))->toBe('Asia/Jakarta')
        ->and($audit->properties->get('starts_at'))->toBe('2027-06-01T02:00:00+00:00');

    $this->get(route('admin.editions.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('created', true)
            ->where('editions.data.0.code', 'ICHES-2027')
            ->where('editions.data.0.seriesCode', 'ICHES-2026')
            ->where('editions.data.0.startsAt', '2027-06-01T02:00:00+00:00'));
    $this->assertDatabaseCount('participation_packages', 0);
    $this->assertDatabaseCount('payments', 0);
});

test('optional metadata and edition number may be omitted and timezone defaults to Jakarta', function () {
    unset($this->payload['theme_i18n'], $this->payload['organizer_i18n'], $this->payload['edition_number'], $this->payload['timezone']);
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.editions.store'), $this->payload)->assertSessionHasNoErrors();
    $edition = ConferenceEdition::query()->sole();
    expect($edition->theme_i18n)->toBeNull()->and($edition->organizer_i18n)->toBeNull()
        ->and($edition->edition_number)->toBeNull()->and($edition->timezone)->toBe('Asia/Jakarta');
});

test('edition validation rejects invalid fields without data or audit writes', function (string $field, mixed $value) {
    $payload = array_replace($this->payload, [$field => $value]);
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.editions.store'), $payload)->assertSessionHasErrors($field);
    $this->assertDatabaseCount('conference_editions', 0);
    expect(Activity::query()->where('event', 'conference_edition_created')->count())->toBe(0);
})->with([
    'missing series' => ['series_id', null],
    'invalid series' => ['series_id', 'bad-id'],
    'missing code' => ['edition_code', '   '],
    'code too long' => ['edition_code', str_repeat('A', 256)],
    'invalid number' => ['edition_number', 0],
    'fractional number' => ['edition_number', 1.5],
    'number overflow' => ['edition_number', 65536],
    'invalid year' => ['year', 0],
    'missing host' => ['host_name', ''],
    'host type' => ['host_name', ['bad']],
    'invalid mode' => ['mode', 'VIRTUAL'],
    'invalid timezone' => ['timezone', 'Mars/Olympus'],
    'invalid start' => ['starts_at', '2027-02-30T09:00'],
    'offset not allowed' => ['starts_at', '2027-06-01T09:00+07:00'],
    'equal end' => ['ends_at', '2027-06-01T09:00'],
    'earlier end' => ['ends_at', '2027-06-01T08:59'],
    'unsupported theme locale' => ['theme_i18n', ['fr' => 'Theme']],
    'unsupported organizer locale' => ['organizer_i18n', ['fr' => 'Organizer']],
    'theme type' => ['theme_i18n', 'bad'],
    'lifecycle injection' => ['lifecycle_status', 'PUBLISHED'],
    'registration injection' => ['registration_opens_at', '2027-01-01T00:00'],
    'registration closing injection' => ['registration_closes_at', '2027-12-01T00:00'],
    'schedule injection' => ['schedule_published_at', '2027-01-01T00:00'],
    'settings injection' => ['settings_json', ['registration' => true]],
    'unknown field' => ['unexpected_admin_field', 'bad'],
]);

test('missing and inactive series are rejected', function (bool $inactive) {
    if ($inactive) {
        $this->series->update(['status' => 'ARCHIVED']);
    } else {
        $this->payload['series_id'] = (string) Str::uuid7();
    }
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.editions.store'), $this->payload)->assertSessionHasErrors('series_id');
    $this->assertDatabaseCount('conference_editions', 0);
})->with([true, false]);

test('duplicate edition code is rejected after normalization', function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $this->post(route('admin.editions.store'), $this->payload)->assertSessionHasNoErrors();
    $this->payload['edition_code'] = '  iches-2027  ';
    $this->post(route('admin.editions.store'), $this->payload)->assertSessionHasErrors(['edition_code' => 'duplicate']);
    $this->assertDatabaseCount('conference_editions', 1);
    expect(Activity::query()->where('event', 'conference_edition_created')->count())->toBe(1);
});

test('all supported modes and local timezone conversions are persisted', function (string $mode, string $timezone, string $utcStart) {
    $this->payload['mode'] = $mode;
    $this->payload['timezone'] = $timezone;
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.editions.store'), $this->payload)->assertSessionHasNoErrors();
    $edition = ConferenceEdition::query()->sole();
    expect($edition->mode)->toBe($mode)->and($edition->timezone)->toBe($timezone)
        ->and($edition->starts_at->utc()->format('Y-m-d H:i:s'))->toBe($utcStart);
})->with([
    ['OFFLINE', 'Asia/Jakarta', '2027-06-01 02:00:00'],
    ['ONLINE', 'UTC', '2027-06-01 09:00:00'],
    ['HYBRID', 'America/New_York', '2027-06-01 13:00:00'],
]);

test('nonexistent local time during daylight saving is rejected', function () {
    $this->payload['timezone'] = 'America/New_York';
    $this->payload['starts_at'] = '2027-03-14T02:30';
    $this->payload['ends_at'] = '2027-03-14T04:00';
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.editions.store'), $this->payload)->assertSessionHasErrors('starts_at');
    $this->assertDatabaseCount('conference_editions', 0);
});

test('edition list uses real server pagination with accurate totals ranges and series', function (int $total, int $current, int $count, ?int $from, ?int $to, int $last) {
    foreach (range(1, $total) as $number) {
        ConferenceEdition::query()->create([
            'series_id' => $this->series->id, 'edition_code' => 'EDITION-'.$number,
            'year' => 2027, 'host_name' => 'Host '.$number, 'mode' => 'OFFLINE',
            'timezone' => 'Asia/Jakarta', 'starts_at' => '2027-06-01 02:00:00',
            'ends_at' => '2027-06-02 10:00:00', 'lifecycle_status' => 'DRAFT',
            'created_at' => now()->subMinutes($total - $number),
        ]);
    }
    $url = fn (int $page): string => route('admin.editions.index', ['page' => $page]);
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get($url($current))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/editions/Index')
            ->has('editions.data', $count)->where('editions.total', $total)
            ->where('editions.from', $from)->where('editions.to', $to)
            ->where('editions.perPage', 20)->where('editions.currentPage', $current)->where('editions.lastPage', $last)
            ->where('editions.firstUrl', $current > 1 ? $url(1) : null)
            ->where('editions.lastUrl', $current < $last ? $url($last) : null)
            ->where('editions.previousUrl', $current > 1 ? $url($current - 1) : null)
            ->where('editions.nextUrl', $current < $last ? $url($current + 1) : null)
            ->has('editions.pages', $last)
            ->where('editions.pages.0.page', 1)->where('editions.pages.0.url', $url(1))
            ->where('editions.data.0.seriesName', $this->series->name)
            ->where('editions.data.0.code', 'EDITION-'.($total - (($current - 1) * 20)))
            ->where('editions.data.0.timezone', 'Asia/Jakarta'));
})->with([
    [1, 1, 1, 1, 1, 1], [20, 1, 20, 1, 20, 1],
    [21, 1, 20, 1, 20, 2], [21, 2, 1, 21, 21, 2],
]);

test('edition action requires a superadmin independently of HTTP middleware', function () {
    expect(fn () => app(CreateConferenceEditionAction::class)->handle(User::factory()->create(), $this->payload))
        ->toThrow(AuthorizationException::class);
    $this->assertDatabaseCount('conference_editions', 0);
});

test('edition creation rolls back when audit persistence fails', function () {
    $event = 'eloquent.creating: '.Activity::class;
    Event::listen($event, function (): void {
        throw new RuntimeException('Audit persistence unavailable');
    });
    try {
        expect(fn () => app(CreateConferenceEditionAction::class)->handle(User::factory()->superAdmin()->create(), $this->payload))
            ->toThrow(RuntimeException::class, 'Audit persistence unavailable');
    } finally {
        Event::forget($event);
    }
    $this->assertDatabaseCount('conference_editions', 0);
    expect(Activity::query()->where('event', 'conference_edition_created')->count())->toBe(0);
});

test('edition work preserves superadmin and ordinary user access to series and dashboards', function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $this->get(route('admin.dashboard'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('seriesCount', 1)->where('editionCount', 0)->has('recentEditions', 0));
    $this->get(route('admin.series.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('series.data.0.code', 'ICHES-2026'));
    $this->get(route('admin.series.create'))->assertOk();

    $this->actingAs(User::factory()->create());
    $this->get(route('admin.dashboard'))->assertForbidden();
    $this->get(route('admin.series.index'))->assertForbidden();
    $this->get(route('admin.series.create'))->assertForbidden();
    $this->post(route('admin.series.store'), [])->assertForbidden();
    $this->get(route('dashboard'))->assertOk();
});
