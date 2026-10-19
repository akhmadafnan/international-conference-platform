<?php

use App\Enums\WorkflowWindowCode;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->withoutVite();
    $series = ConferenceSeries::query()->create([
        'code' => 'ICHES', 'name' => 'ICHES', 'status' => 'ACTIVE',
    ]);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => 'ICHES-2027',
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'UNISYA',
        'mode' => 'HYBRID',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => '2027-11-15 08:00:00',
        'ends_at' => '2027-11-16 18:00:00',
        'lifecycle_status' => 'DRAFT',
    ]);
    $this->track = [
        'code' => 'ISLAMIC_STUDIES',
        'name_i18n' => ['id' => 'Studi Islam', 'en' => 'Islamic Studies', 'ar' => 'الدراسات الإسلامية'],
        'active' => true,
    ];
    $this->window = [
        'window_code' => 'ABSTRACT_SUBMISSION',
        'opens_at' => '2027-01-10T08:00',
        'closes_at' => '2027-02-01T23:59',
        'active' => true,
    ];
});

test('academic configuration routes forbid ordinary accounts and redirect guests', function (string $actor) {
    if ($actor === 'ordinary') {
        $this->actingAs(User::factory()->create());
    }
    $checks = [
        $this->get(route('admin.editions.academic', $this->edition)),
        $this->post(route('admin.editions.tracks.store', $this->edition), $this->track),
        $this->post(route('admin.editions.windows.store', $this->edition), $this->window),
    ];
    foreach ($checks as $response) {
        if ($actor === 'guest') {
            $response->assertRedirect(route('login'));
        } else {
            $response->assertForbidden();
        }
    }
    $this->assertDatabaseCount('tracks', 0);
    $this->assertDatabaseCount('edition_workflow_windows', 0);
})->with(['guest', 'ordinary']);

test('real tracks and windows render for authorized superadmin', function () {
    $this->edition->tracks()->create($this->track);
    $this->edition->workflowWindows()->create([
        'window_code' => 'ABSTRACT_SUBMISSION',
        'opens_at' => '2027-01-10 01:00:00',
        'closes_at' => '2027-02-01 16:59:00',
        'active' => true,
    ]);
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.editions.academic', $this->edition))->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/editions/AcademicSetup')
            ->where('edition.code', 'ICHES-2027')
            ->where('edition.timezone', 'Asia/Jakarta')
            ->has('tracks', 1)->where('tracks.0.code', 'ISLAMIC_STUDIES')
            ->has('windows', 1)->where('windows.0.opens_at', '2027-01-10T08:00'));
});

test('track creation persists multilingual names and audit, duplicate cannot overwrite', function () {
    $actor = User::factory()->superAdmin()->create();
    $this->actingAs($actor)->post(route('admin.editions.tracks.store', $this->edition), $this->track)
        ->assertSessionHasNoErrors()->assertRedirect(route('admin.editions.academic', $this->edition));
    $this->assertDatabaseHas('tracks', ['edition_id' => $this->edition->id, 'code' => 'ISLAMIC_STUDIES', 'active' => true]);
    $this->actingAs($actor)->post(route('admin.editions.tracks.store', $this->edition), $this->track)
        ->assertSessionHasErrors('code');
    $this->assertDatabaseCount('tracks', 1);
    expect(Activity::query()->where('event', 'edition_track_created')->count())->toBe(1);
});

test('configured active abstract workflow window stores UTC, updates only its edition and audits', function () {
    $actor = User::factory()->superAdmin()->create();
    $this->actingAs($actor)->post(route('admin.editions.windows.store', $this->edition), $this->window)
        ->assertSessionHasNoErrors();
    $this->assertDatabaseHas('edition_workflow_windows', [
        'edition_id' => $this->edition->id,
        'window_code' => WorkflowWindowCode::ABSTRACT_SUBMISSION->value,
        'opens_at' => '2027-01-10 01:00:00',
        'closes_at' => '2027-02-01 16:59:00',
    ]);
    $changed = array_replace($this->window, ['closes_at' => '2027-03-01T23:59']);
    $this->actingAs($actor)->post(route('admin.editions.windows.store', $this->edition), $changed)
        ->assertSessionHasNoErrors();
    $this->assertDatabaseCount('edition_workflow_windows', 1);
    expect(Activity::query()->where('event', 'edition_window_configured')->count())->toBe(2);
});

test('invalid or backwards window cannot create or overwrite a saved period', function () {
    $actor = User::factory()->superAdmin()->create();
    $this->actingAs($actor)->post(route('admin.editions.windows.store', $this->edition), array_replace(
        $this->window, ['closes_at' => '2027-01-01T11:00'],
    ))->assertSessionHasErrors('closes_at');
    $this->actingAs($actor)->post(route('admin.editions.windows.store', $this->edition), array_replace(
        $this->window, ['window_code' => 'UNKNOWN'],
    ))->assertSessionHasErrors('window_code');
    $this->assertDatabaseCount('edition_workflow_windows', 0);
});

test('track names require all three locales without invalid persisted rows', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.editions.tracks.store', $this->edition), array_replace(
            $this->track, ['name_i18n' => ['id' => 'Satu', 'en' => 'One']],
        ))->assertSessionHasErrors('name_i18n.ar');
    $this->assertDatabaseCount('tracks', 0);
});
