<?php

use App\Actions\Admin\DeleteConferenceRecordAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

function v03bcEdition(ConferenceSeries $series, string $code = 'ICHES-2027'): ConferenceEdition
{
    return ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => $code, 'year' => 2027,
        'host_name' => 'Host', 'mode' => 'OFFLINE', 'timezone' => 'Asia/Jakarta',
        'starts_at' => '2027-06-01 02:00:00', 'ends_at' => '2027-06-02 10:00:00',
        'lifecycle_status' => 'DRAFT',
    ]);
}

beforeEach(function () {
    $this->withoutVite();
    $this->series = ConferenceSeries::query()->create([
        'code' => 'ICHES-2026', 'name' => 'Conference Series', 'status' => 'ACTIVE',
    ]);
    $this->emptySeries = ConferenceSeries::query()->create([
        'code' => 'EMPTY', 'name' => 'Empty Series', 'status' => 'ACTIVE',
    ]);
    $this->edition = v03bcEdition($this->series);
    $this->admin = User::factory()->superAdmin()->create();
});

test('conference delete routes require a verified superadmin', function (string $actor) {
    if ($actor === 'ordinary') {
        $this->actingAs(User::factory()->create());
    } elseif ($actor === 'unverified') {
        $this->actingAs(User::factory()->superAdmin()->unverified()->create());
    }

    foreach ([
        ['admin.series.destroy', $this->emptySeries->id, 'EMPTY'],
        ['admin.editions.destroy', $this->edition->id, 'ICHES-2027'],
    ] as [$route, $id, $code]) {
        $response = $this->delete(route($route, $id), ['confirmation_code' => $code]);
        if ($actor === 'ordinary') {
            $response->assertForbidden();
        } else {
            $response->assertRedirect(route($actor === 'guest' ? 'login' : 'verification.notice'));
        }
    }
    $this->assertDatabaseCount('conference_series', 2);
    $this->assertDatabaseCount('conference_editions', 1);
    expect(Activity::query()->whereIn('event', ['conference_series_deleted', 'conference_edition_deleted'])->count())->toBe(0);
})->with(['guest', 'ordinary', 'unverified']);

test('superadmin deletes only the selected empty series and records its deleted identity', function () {
    $seriesAttributes = $this->series->getAttributes();
    $editionAttributes = $this->edition->getAttributes();
    $this->actingAs($this->admin)
        ->delete(route('admin.series.destroy', $this->emptySeries), ['confirmation_code' => 'EMPTY'])
        ->assertRedirect(route('admin.series.index'))->assertSessionHasNoErrors()->assertSessionHas('series_deleted', true);

    $this->assertDatabaseMissing('conference_series', ['id' => $this->emptySeries->id]);
    // Ignore newly hydrated nullable keys and attribute ordering.
    foreach ($seriesAttributes as $key => $value) {
        expect($this->series->fresh()->getRawOriginal($key))->toBe($value);
    }
    foreach ($editionAttributes as $key => $value) {
        expect($this->edition->fresh()->getRawOriginal($key))->toBe($value);
    }

    $audit = Activity::query()->where('event', 'conference_series_deleted')->sole();
    expect($audit->causer_id)->toBe($this->admin->id)->and($audit->causer_type)->toBe($this->admin->getMorphClass())
        ->and($audit->subject_id)->toBe($this->emptySeries->id)->and($audit->subject_type)->toBe($this->emptySeries->getMorphClass())
        ->and($audit->properties->get('id'))->toBe($this->emptySeries->id)->and($audit->properties->get('code'))->toBe('EMPTY');
    $this->get(route('admin.series.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('deleted', true)->where('series.total', 1));
});

test('superadmin deletes only an empty draft edition and preserves its series and unrelated data', function () {
    $other = v03bcEdition($this->series, 'UNRELATED');
    $venueId = (string) Str::uuid7();
    DB::table('venues')->insert([
        'id' => $venueId, 'edition_id' => $other->id, 'name' => 'Other venue',
        'address' => 'Address', 'city' => 'City', 'country_code' => 'ID',
    ]);
    $seriesAttributes = $this->series->getAttributes();
    $otherAttributes = $other->getAttributes();
    $this->actingAs($this->admin)
        ->delete(route('admin.editions.destroy', $this->edition), ['confirmation_code' => 'ICHES-2027'])
        ->assertRedirect(route('admin.editions.index'))->assertSessionHasNoErrors()->assertSessionHas('edition_deleted', true);

    $this->assertDatabaseMissing('conference_editions', ['id' => $this->edition->id]);
    $this->assertDatabaseHas('venues', ['id' => $venueId, 'edition_id' => $other->id]);
    $this->assertDatabaseCount('conference_series', 2);
    // Check original persisted values; Eloquent hydrates nullable columns.
    foreach ($seriesAttributes as $key => $value) {
        expect($this->series->fresh()->getRawOriginal($key))->toBe($value);
    }
    foreach ($otherAttributes as $key => $value) {
        expect($other->fresh()->getRawOriginal($key))->toBe($value);
    }
    $audit = Activity::query()->where('event', 'conference_edition_deleted')->sole();
    expect($audit->causer_id)->toBe($this->admin->id)->and($audit->causer_type)->toBe($this->admin->getMorphClass())
        ->and($audit->subject_id)->toBe($this->edition->id)->and($audit->subject_type)->toBe($this->edition->getMorphClass())
        ->and($audit->properties->get('id'))->toBe($this->edition->id)
        ->and($audit->properties->get('code'))->toBe('ICHES-2027')
        ->and($audit->properties->get('series_id'))->toBe($this->series->id)
        ->and($audit->properties->get('lifecycle_status'))->toBe('DRAFT');
    $this->get(route('admin.editions.index'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('deleted', true)->where('editions.total', 1));
});

test('typed deletion codes must match exactly for both resource types', function (mixed $code) {
    $this->actingAs($this->admin);
    foreach ([['admin.series.destroy', $this->emptySeries], ['admin.editions.destroy', $this->edition]] as [$route, $record]) {
        $this->delete(route($route, $record), ['confirmation_code' => $code])->assertSessionHasErrors('confirmation_code');
    }
    $this->assertDatabaseCount('conference_series', 2);
    $this->assertDatabaseCount('conference_editions', 1);
    expect(Activity::query()->whereIn('event', ['conference_series_deleted', 'conference_edition_deleted'])->count())->toBe(0);
})->with([
    'missing' => [null],
    'empty' => [''],
    'wrong' => ['wrong'],
    'series case mismatch' => ['empty'],
    'edition case mismatch' => ['iches-2027'],
    'series extra spaces' => [' EMPTY '],
    'edition extra spaces' => [' ICHES-2027 '],
    'array instead of text' => [['invalid']],
]);

test('series with an edition cannot be deleted', function () {
    $this->actingAs($this->admin)->delete(route('admin.series.destroy', $this->series), ['confirmation_code' => 'ICHES-2026'])
        ->assertSessionHasErrors(['delete' => 'seriesHasEditions']);
    $this->assertDatabaseHas('conference_series', ['id' => $this->series->id]);
    $this->assertDatabaseHas('conference_editions', ['id' => $this->edition->id]);
    expect(Activity::query()->where('event', 'conference_series_deleted')->count())->toBe(0);
});

test('only draft editions may be deleted', function (string $status) {
    $this->edition->update(['lifecycle_status' => $status]);
    $this->actingAs($this->admin)->delete(route('admin.editions.destroy', $this->edition), ['confirmation_code' => 'ICHES-2027'])
        ->assertSessionHasErrors(['delete' => 'editionNotDraft']);
    $this->assertDatabaseHas('conference_editions', ['id' => $this->edition->id, 'lifecycle_status' => $status]);
    expect(Activity::query()->where('event', 'conference_edition_deleted')->count())->toBe(0);
})->with(['ACTIVE', 'PUBLISHED', 'COMPLETED', 'CANCELLED', 'ARCHIVED']);

test('every direct edition dependency prevents deletion without removing any records', function (string $table) {
    $id = (string) Str::uuid7();
    $attributes = match ($table) {
        'edition_memberships' => ['user_id' => $this->admin->id, 'joined_at' => now()],
        'participation_packages' => ['code' => 'FREE', 'name_i18n' => '{"en":"Free"}', 'billing_mode' => 'FREE'],
        'activities' => ['code' => 'EVENT', 'name_i18n' => '{"en":"Event"}', 'activity_type' => 'CONFERENCE'],
        'tracks' => ['code' => 'TRACK', 'name_i18n' => '{"en":"Track"}'],
        'venues' => ['name' => 'Venue', 'address' => 'Address', 'city' => 'City', 'country_code' => 'ID'],
        'edition_workflow_windows' => ['window_code' => 'REGISTRATION'],
        'important_dates' => ['code' => 'START', 'label_i18n' => '{"en":"Start"}', 'starts_at' => '2027-06-01 02:00:00'],
        'number_sequences' => ['sequence_type' => 'PAPER'],
        'payment_destinations' => ['code' => 'BANK', 'label' => 'Bank', 'bank_name' => 'Bank', 'account_number' => '123', 'account_holder' => 'Host'],
        'generated_documents' => ['document_type' => 'EVENT_PASS', 'status' => 'ISSUED', 'snapshot_json' => '{}'],
        default => [],
    };
    DB::table($table)->insert(['id' => $id, 'edition_id' => $this->edition->id, ...$attributes]);
    $before = DB::table($table)->where('id', $id)->first();
    $this->actingAs($this->admin)->delete(route('admin.editions.destroy', $this->edition), ['confirmation_code' => 'ICHES-2027'])
        ->assertSessionHasErrors(['delete' => 'editionHasData']);
    $this->assertDatabaseHas('conference_editions', ['id' => $this->edition->id]);
    $this->assertDatabaseCount('conference_series', 2);
    expect(DB::table($table)->where('id', $id)->first())->toEqual($before)
        ->and(Activity::query()->where('event', 'conference_edition_deleted')->count())->toBe(0);
})->with([
    'edition_memberships', 'participation_packages', 'activities', 'tracks', 'venues',
    'edition_workflow_windows', 'important_dates', 'number_sequences', 'payment_destinations', 'generated_documents',
]);

test('a submission and all registration prerequisites survive a refused edition deletion', function () {
    $membership = (string) Str::uuid7();
    $package = (string) Str::uuid7();
    $registration = (string) Str::uuid7();
    $submission = (string) Str::uuid7();
    DB::table('edition_memberships')->insert(['id' => $membership, 'edition_id' => $this->edition->id, 'user_id' => $this->admin->id, 'joined_at' => now()]);
    DB::table('participation_packages')->insert(['id' => $package, 'edition_id' => $this->edition->id, 'code' => 'FREE', 'name_i18n' => '{"en":"Free"}', 'billing_mode' => 'FREE']);
    DB::table('registrations')->insert([
        'id' => $registration, 'membership_id' => $membership, 'package_id' => $package,
        'registration_code' => 'REG-1', 'package_name_snapshot' => 'Free',
        'billing_mode' => 'FREE', 'fee_amount' => '0.00', 'currency_code' => 'IDR', 'status' => 'CONFIRMED',
    ]);
    DB::table('submissions')->insert([
        'id' => $submission, 'edition_id' => $this->edition->id, 'registration_id' => $registration,
        'paper_code' => 'PAPER-1', 'primary_locale' => 'en', 'academic_status' => 'DRAFT',
    ]);
    $this->actingAs($this->admin)->delete(route('admin.editions.destroy', $this->edition), ['confirmation_code' => 'ICHES-2027'])
        ->assertSessionHasErrors(['delete' => 'editionHasData']);
    foreach (['submissions' => $submission, 'registrations' => $registration, 'participation_packages' => $package, 'edition_memberships' => $membership] as $table => $id) {
        $this->assertDatabaseHas($table, ['id' => $id]);
    }
    $this->assertDatabaseHas('conference_editions', ['id' => $this->edition->id]);
});

test('edition scoped roles without an edition foreign key also prevent deletion', function () {
    $id = (string) Str::uuid7();
    $table = (string) config('permission.table_names.roles');
    $teamColumn = (string) config('permission.column_names.team_foreign_key');
    DB::table($table)->insert(['id' => $id, $teamColumn => $this->edition->id, 'name' => 'edition-role', 'guard_name' => 'web']);
    $this->actingAs($this->admin)->delete(route('admin.editions.destroy', $this->edition), ['confirmation_code' => 'ICHES-2027'])
        ->assertSessionHasErrors(['delete' => 'editionHasData']);
    $this->assertDatabaseHas($table, ['id' => $id]);
    $this->assertDatabaseHas('conference_editions', ['id' => $this->edition->id]);
});

test('failed audit rolls back a single model deletion and returns a safe error', function (string $kind) {
    $eventName = $kind === 'series' ? 'conference_series_deleted' : 'conference_edition_deleted';
    $record = $kind === 'series' ? $this->emptySeries : $this->edition;
    $code = $kind === 'series' ? 'EMPTY' : 'ICHES-2027';
    $event = 'eloquent.creating: '.Activity::class;
    Event::listen($event, function (Activity $activity) use ($eventName): void {
        if ($activity->event === $eventName) {
            throw new RuntimeException('Audit persistence unavailable');
        }
    });
    try {
        $this->actingAs($this->admin)->delete(route($kind === 'series' ? 'admin.series.destroy' : 'admin.editions.destroy', $record), ['confirmation_code' => $code])
            ->assertSessionHasErrors(['delete' => 'failed']);
    } finally {
        Event::forget($event);
    }
    $this->assertDatabaseCount('conference_series', 2);
    $this->assertDatabaseCount('conference_editions', 1);
    $this->assertDatabaseHas($record->getTable(), ['id' => $record->getKey()]);
    expect(Activity::query()->where('event', $eventName)->count())->toBe(0);
})->with(['series', 'edition']);

test('foreign key restrict is translated to a clear error if a dependency appears after checks', function () {
    $event = 'eloquent.deleting: '.ConferenceEdition::class;
    Event::listen($event, function (ConferenceEdition $edition): void {
        DB::table('venues')->insert([
            'id' => (string) Str::uuid7(), 'edition_id' => $edition->id,
            'name' => 'Concurrent venue', 'address' => 'Address', 'city' => 'City', 'country_code' => 'ID',
        ]);
    });
    try {
        $this->actingAs($this->admin)->delete(route('admin.editions.destroy', $this->edition), ['confirmation_code' => 'ICHES-2027'])
            ->assertSessionHasErrors(['delete' => 'editionHasData']);
    } finally {
        Event::forget($event);
    }
    $this->assertDatabaseHas('conference_editions', ['id' => $this->edition->id]);
    $this->assertDatabaseCount('venues', 0);
    expect(Activity::query()->where('event', 'conference_edition_deleted')->count())->toBe(0);
});

test('action rechecks the current database state instead of a stale draft snapshot', function () {
    ConferenceEdition::query()->whereKey($this->edition->id)->update(['lifecycle_status' => 'ACTIVE']);
    expect(fn () => app(DeleteConferenceRecordAction::class)->handle($this->admin, $this->edition, 'ICHES-2027'))
        ->toThrow(ValidationException::class);
    $this->assertDatabaseHas('conference_editions', ['id' => $this->edition->id, 'lifecycle_status' => 'ACTIVE']);
});

test('deletion action checks superadmin independently of the HTTP route', function () {
    $ordinary = User::factory()->create();
    expect(fn () => app(DeleteConferenceRecordAction::class)->handle($ordinary, $this->emptySeries, 'EMPTY'))->toThrow(AuthorizationException::class);
    expect(fn () => app(DeleteConferenceRecordAction::class)->handle($ordinary, $this->edition, 'ICHES-2027'))->toThrow(AuthorizationException::class);
    $this->assertDatabaseCount('conference_series', 2);
    $this->assertDatabaseCount('conference_editions', 1);
});
