<?php

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\Track;
use App\Models\User;
use Database\Seeders\LocalConferenceUatSeeder;
use Database\Seeders\LocalConferenceUatTopicsSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    config(['demo_roles.enabled' => true]);
    $series = ConferenceSeries::query()->create(['code' => 'ICHES-UAT', 'name' => 'UAT fixture', 'status' => 'ACTIVE']);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => LocalConferenceUatSeeder::EDITION_CODE,
        'year' => 2027, 'host_name' => 'Test', 'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta',
        'lifecycle_status' => 'DRAFT', 'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonths(2),
        'settings_json' => ['uat_only' => true, 'abstract_submission' => ['track_required' => false]],
    ]);
});

test('Topic-only seeder inserts exactly the eight official localized Topics and is idempotent', function (string $environment) {
    app()->detectEnvironment(static fn (): string => $environment);
    $editionBefore = $this->edition->fresh()->getRawOriginal();
    expect(Track::query()->count())->toBe(0);

    $this->seed(LocalConferenceUatTopicsSeeder::class);

    preg_match_all('/^\| (T0[1-8]) \| (.+) \|$/m', file_get_contents(base_path('docs/governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md')), $matches, PREG_SET_ORDER);
    $expected = array_column($matches, 2, 1);
    expect($expected)->toHaveCount(8)->and(Track::query()->count())->toBe(8);
    foreach ($this->edition->tracks()->get() as $track) {
        expect($track->name_i18n['en'])->toBe($expected[$track->code])
            ->and($track->name_i18n['id'])->not->toBeEmpty()
            ->and($track->name_i18n['ar'])->not->toBeEmpty()
            ->and($track->active)->toBeTrue();
    }
    $before = DB::table('tracks')->orderBy('id')->get()->toJson();
    $this->seed(LocalConferenceUatTopicsSeeder::class);
    $this->seed(LocalConferenceUatTopicsSeeder::class);

    expect(DB::table('tracks')->orderBy('id')->get()->toJson())->toBe($before)
        ->and($this->edition->fresh()->getRawOriginal())->toBe($editionBefore)
        ->and(User::query()->count())->toBe(0);
})->with(['local', 'testing']);

test('Topic-only seeder preserves existing Topic edits active state and other Editions', function () {
    $manual = $this->edition->tracks()->create([
        'code' => 'T01', 'name_i18n' => ['en' => 'Edited', 'id' => 'Manual', 'ar' => 'يدوي'],
        'description_i18n' => ['en' => 'Operator description'], 'active' => false,
    ]);
    $custom = $this->edition->tracks()->create(['code' => 'CUSTOM', 'name_i18n' => ['en' => 'Custom'], 'active' => false]);
    foreach (['ICHES-2027', 'OTHER-2027'] as $code) {
        $other = $this->edition->replicate();
        $other->edition_code = $code;
        $other->settings_json = ['uat_only' => false];
        $other->save();
        if ($code === 'OTHER-2027') {
            $other->tracks()->create(['code' => 'T02', 'name_i18n' => ['en' => 'Other'], 'active' => false]);
        }
    }
    $editionsBefore = DB::table('conference_editions')->orderBy('id')->get()->toJson();
    $tracksBefore = Track::query()->get()->mapWithKeys(fn ($track) => [$track->id => $track->getRawOriginal()]);

    $this->seed(LocalConferenceUatTopicsSeeder::class);
    $this->seed(LocalConferenceUatTopicsSeeder::class);

    foreach ($tracksBefore as $id => $attributes) {
        expect(Track::query()->findOrFail($id)->getRawOriginal())->toBe($attributes);
    }
    expect($this->edition->tracks()->count())->toBe(9)
        ->and($manual->fresh()->active)->toBeFalse()->and($custom->fresh()->active)->toBeFalse()
        ->and(Track::query()->where('edition_id', '!=', $this->edition->id)->count())->toBe(1)
        ->and(DB::table('conference_editions')->orderBy('id')->get()->toJson())->toBe($editionsBefore);
});

test('Topic-only seeder preserves accounts passwords Paper 0001 snapshots and all non-Topic data', function (bool $hasReviewHistory) {
    $author = User::factory()->create(['email' => 'author@iches.test', 'password' => 'OperatorPassword!123']);
    $reviewer = User::factory()->create();
    $membership = EditionMembership::query()->create([
        'edition_id' => $this->edition->id, 'user_id' => $author->id, 'membership_status' => 'ACTIVE', 'joined_at' => now(),
    ]);
    $package = ParticipationPackage::query()->create([
        'edition_id' => $this->edition->id, 'code' => 'AUTHOR', 'name_i18n' => ['en' => 'Author'],
        'billing_mode' => 'FREE', 'price' => 0, 'currency_code' => 'IDR', 'active' => true,
    ]);
    $registration = Registration::query()->create([
        'membership_id' => $membership->id, 'registration_code' => 'UAT-REG', 'package_id' => $package->id,
        'package_name_snapshot' => 'Author', 'billing_mode' => 'FREE', 'fee_amount' => 0, 'currency_code' => 'IDR', 'status' => 'PENDING',
    ]);
    $paper = Submission::query()->create([
        'edition_id' => $this->edition->id, 'registration_id' => $registration->id,
        'paper_code' => 'ICHES-UAT-2027-P-0001', 'primary_locale' => 'en', 'academic_status' => 'SUBMITTED',
        'current_abstract_version' => 1, 'track_id' => null, 'submitted_at' => now(),
    ]);
    $snapshot = $paper->snapshots()->create([
        'snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => ['track_id' => null],
        'checksum' => hash('sha256', 'historical snapshot'), 'created_by_user_id' => $author->id,
    ]);
    if ($hasReviewHistory) {
        $assignment = ReviewAssignment::query()->create([
            'submission_id' => $paper->id, 'snapshot_id' => $snapshot->id, 'reviewer_id' => $reviewer->id,
            'assigned_by' => $author->id, 'status' => 'ACCEPTED', 'screening_note' => 'Historical COI check',
        ]);
        $assignment->review()->create([
            'status' => 'SUBMITTED', 'recommendation' => 'ACCEPT', 'author_comments' => 'Historical review',
            'confidential_comments' => 'Historical private note', 'submitted_at' => now(),
        ]);
    }
    $before = [];
    foreach (Schema::getTableListing(schemaQualified: false) as $table) {
        if ($table !== 'tracks') {
            $before[$table] = DB::table($table)->get()->toJson();
        }
    }

    $this->seed(LocalConferenceUatTopicsSeeder::class);
    $this->seed(LocalConferenceUatTopicsSeeder::class);

    foreach ($before as $table => $rows) {
        expect(DB::table($table)->get()->toJson(), $table)->toBe($rows);
    }
    expect($paper->fresh()->track_id)->toBeNull()->and($paper->snapshots()->count())->toBe(1)
        ->and(ReviewAssignment::query()->count())->toBe($hasReviewHistory ? 1 : 0)
        ->and(Track::query()->count())->toBe(8);
})->with([false, true]);

test('Topic-only seeder rejects unsafe environments and missing demo opt-in before querying data', function (string $environment, bool $optIn) {
    app()->detectEnvironment(static fn (): string => $environment);
    config(['demo_roles.enabled' => $optIn]);
    DB::enableQueryLog();
    DB::flushQueryLog();

    expect(fn () => (new LocalConferenceUatTopicsSeeder)->run())->toThrow(RuntimeException::class, 'local/testing');
    expect(DB::getQueryLog())->toBeEmpty();
    DB::disableQueryLog();
})->with([['production', true], ['staging', true], ['local', false], ['testing', false]]);

test('Topic-only seeder rejects absent UAT Edition without creating it', function () {
    $this->edition->update(['edition_code' => 'ICHES-2027']);

    expect(fn () => $this->seed(LocalConferenceUatTopicsSeeder::class))->toThrow(RuntimeException::class, 'Existing ICHES-UAT-2027');
    expect(ConferenceEdition::query()->count())->toBe(1)->and(Track::query()->count())->toBe(0);
});

test('Topic-only seeder requires a strict true UAT marker', function (array $settings) {
    $this->edition->update(['settings_json' => $settings]);
    $before = $this->edition->fresh()->getRawOriginal();

    expect(fn () => $this->seed(LocalConferenceUatTopicsSeeder::class))->toThrow(RuntimeException::class, 'uat_only');
    expect($this->edition->fresh()->getRawOriginal())->toBe($before)->and(Track::query()->count())->toBe(0);
})->with([[[]], [['uat_only' => false]], [['uat_only' => 'true']], [['uat_only' => 1]]]);
