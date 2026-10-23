<?php

use App\Actions\Submission\CreateDraftSubmissionAction;
use App\Actions\Submission\EvaluateDraftSubmissionReadinessAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Track;
use App\Models\User;
use App\Support\Workspace\SuperAdminWorkspaceMode;
use Database\Seeders\EditionPersonaDemoSeeder;
use Database\Seeders\LocalConferenceUatSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

test('eight official UAT Topics are isolated idempotent and preserve operator edits and submitted snapshots', function () {
    config(['demo_roles.enabled' => true]);
    $this->seed(LocalConferenceUatSeeder::class);
    $edition = ConferenceEdition::query()->where('edition_code', LocalConferenceUatSeeder::EDITION_CODE)->sole();
    preg_match_all('/^\| (T0[1-8]) \| (.+) \|$/m', file_get_contents(base_path('docs/governance/ICHES_2027_OPERATIONAL_REBASELINE_2026_10_23.md')), $matches, PREG_SET_ORDER);
    $expected = array_column($matches, 2, 1);
    expect($expected)->toHaveCount(8);
    foreach ($edition->tracks()->get() as $track) {
        expect($track->name_i18n['en'])->toBe($expected[$track->code])
            ->and($track->name_i18n['id'])->not->toBeEmpty()->and($track->name_i18n['ar'])->not->toBeEmpty();
    }
    $other = $edition->replicate();
    $other->edition_code = 'OTHER-TOPICS';
    $other->settings_json = ['other' => 'preserved'];
    $other->save();
    $otherBefore = $other->fresh()->getRawOriginal();
    $manual = $edition->tracks()->where('code', 'T01')->sole();
    $manual->update(['name_i18n' => ['en' => 'Operator edited', 'id' => 'Manual', 'ar' => 'يدوي'], 'active' => false]);
    $manualBefore = $manual->fresh()->getRawOriginal();
    $settings = $edition->settings_json;
    $settings['abstract_submission']['track_required'] = false;
    $settings['abstract_submission']['min_keywords'] = 2;
    $settings['preserved'] = true;
    $edition->update(['settings_json' => $settings]);
    $paper = Submission::query()->create([
        'edition_id' => $edition->id, 'registration_id' => Registration::query()->sole()->id,
        'paper_code' => 'ICHES-UAT-2027-P-0001', 'academic_status' => 'SUBMITTED', 'primary_locale' => 'en', 'current_abstract_version' => 1,
    ]);
    $snapshot = $paper->snapshots()->create([
        'snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => ['track_id' => null],
        'checksum' => hash('sha256', 'unchanged'), 'created_by_user_id' => User::query()->where('email', 'author@iches.test')->sole()->id,
    ]);
    $before = [$paper->fresh()->getRawOriginal(), $snapshot->fresh()->getRawOriginal()];
    $this->seed(LocalConferenceUatSeeder::class);
    $this->seed(LocalConferenceUatSeeder::class);
    expect(Track::query()->count())->toBe(8)->and($other->tracks()->count())->toBe(0)
        ->and($manual->fresh()->getRawOriginal())->toBe($manualBefore)->and($other->fresh()->getRawOriginal())->toBe($otherBefore)
        ->and($edition->fresh()->settings_json['abstract_submission']['track_required'])->toBeTrue()
        ->and($edition->fresh()->settings_json['abstract_submission']['min_keywords'])->toBe(2)
        ->and($edition->fresh()->settings_json['preserved'])->toBeTrue()
        ->and([$paper->fresh()->getRawOriginal(), $snapshot->fresh()->getRawOriginal()])->toBe($before)
        ->and(User::query()->count())->toBe(18)->and(Registration::query()->count())->toBe(1);
});

test('a new UAT draft requires primary Topic without rewriting an existing submission', function () {
    config(['demo_roles.enabled' => true]);
    $this->seed(LocalConferenceUatSeeder::class);
    $registration = Registration::query()->sole();
    $author = $registration->membership->user;
    $edition = $registration->membership->edition;
    $draft = app(CreateDraftSubmissionAction::class)->handle($author, $edition, $registration, 'en');
    $readiness = app(EvaluateDraftSubmissionReadinessAction::class)->handle($author, $draft)->toArray();
    expect(array_column($readiness['findings'], 'code'))->toContain('TRACK_REQUIRED');
});

test('isolated UAT environment opt-in and idempotent actual registration', function () {
    config([
        'demo_roles.enabled' => true,
        'demo_roles.password' => null,
        'app.env' => 'testing',
    ]);

    $this->seed(LocalConferenceUatSeeder::class);
    $usersAfterMigration = DB::table('users')->orderBy('id')->get()->toJson();
    $this->seed(LocalConferenceUatSeeder::class);

    expect(DB::table('users')->orderBy('id')->get()->toJson())->toBe($usersAfterMigration);

    $edition = ConferenceEdition::query()
        ->where('edition_code', LocalConferenceUatSeeder::EDITION_CODE)->sole();

    expect($edition->settings_json['uat_only'])->toBeTrue()
        ->and(ConferenceSeries::query()->where('code', 'ICHES-UAT')->count())->toBe(1)
        ->and(ParticipationPackage::query()->where('edition_id', $edition->id)->count())->toBe(2)
        ->and($edition->workflowWindows()->count())->toBe(2)
        ->and(User::query()->count())->toBe(18)
        ->and(EditionMembership::query()->count())->toBe(6)
        ->and(Registration::query()->count())->toBe(1);

    foreach (SuperAdminWorkspaceMode::available() as $persona) {
        if ($persona === 'superadmin') {
            continue;
        }
        $user = User::query()->where('email', str_replace('_', '-', $persona).'@iches.test')->sole();
        expect(Hash::check('password', $user->password))->toBeTrue();
    }

    $registration = Registration::query()->sole();
    expect($registration->package->code)->toBe('PRESENTER-UAT')
        ->and($registration->membership->user->email)->toBe('author@iches.test')
        ->and($registration->status->value)->toBe('PENDING');
});

test('UAT seeder cannot be run without explicit local opt-in', function () {
    config(['demo_roles.enabled' => false]);
    $this->seed(LocalConferenceUatSeeder::class);
})->throws(RuntimeException::class, 'opt-in');

test('UAT seeder refuses production even with opt-in', function () {
    config(['demo_roles.enabled' => true]);
    app()->detectEnvironment(static fn (): string => 'production');

    expect(fn () => (new LocalConferenceUatSeeder)->run())->toThrow(RuntimeException::class, 'local/testing');
    expect(User::query()->count())->toBe(0)->and(ConferenceEdition::query()->count())->toBe(0);
});

test('legacy identities migrate in place preserving all Edition and scholarly history', function () {
    config(['demo_roles.enabled' => true, 'demo_roles.password' => null]);
    $this->seed(LocalConferenceUatSeeder::class);
    $edition = ConferenceEdition::query()->where('edition_code', LocalConferenceUatSeeder::EDITION_CODE)->sole();
    $registration = Registration::query()->sole();
    $author = User::query()->where('email', 'author@iches.test')->sole();
    $submission = Submission::query()->create([
        'edition_id' => $edition->id, 'registration_id' => $registration->id,
        'paper_code' => 'UAT-LEGACY', 'primary_locale' => 'en', 'academic_status' => 'SUBMITTED',
        'submitted_at' => now(),
    ]);
    $submission->snapshots()->create([
        'snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1,
        'metadata_json' => ['email' => 'demo+author.iches-uat-2027@example.test'],
        'checksum' => hash('sha256', 'legacy scholarly metadata'), 'created_by_user_id' => $author->id,
    ]);
    activity()->causedBy($author)->performedOn($submission)->log('Legacy demo submission');

    $identities = [];
    foreach (User::query()->get() as $user) {
        $persona = str_replace('ICHES Demo · ', '', $user->name);
        $identities[$user->id] = str_replace('_', '-', $persona).'@iches.test';
        $user->update([
            'email' => 'demo+'.str_replace('_', '-', $persona).'.iches-uat-2027@example.test',
            'password' => 'LegacyPassword!123',
        ]);
    }

    $tables = ['edition_memberships', 'registrations', 'submissions', 'submission_snapshots', 'roles',
        'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions', 'activity_log'];
    $before = [];
    foreach ($tables as $table) {
        $before[$table] = DB::table($table)->get()->toJson();
    }
    $superadmin = User::factory()->create(['is_super_admin' => true]);
    $superadminBefore = $superadmin->fresh()->getRawOriginal();

    $this->seed(LocalConferenceUatSeeder::class);
    $this->seed(LocalConferenceUatSeeder::class);

    expect(User::query()->count())->toBe(19);
    foreach ($identities as $id => $email) {
        $user = User::query()->findOrFail($id);
        expect($user->email)->toBe($email)->and(Hash::check('password', $user->password))->toBeTrue();
    }
    foreach ($tables as $table) {
        expect(DB::table($table)->get()->toJson())->toBe($before[$table]);
    }
    expect($superadmin->fresh()->getRawOriginal())->toBe($superadminBefore);
});

test('UAT target collision rolls back the entire seed without claiming a non-demo account', function (bool $legacy) {
    config(['demo_roles.enabled' => true, 'demo_roles.edition_code' => 'PREVIOUS']);
    if ($legacy) {
        $this->seed(LocalConferenceUatSeeder::class);
        foreach (User::query()->get() as $user) {
            $persona = str_replace('ICHES Demo · ', '', $user->name);
            $user->update(['email' => 'demo+'.str_replace('_', '-', $persona).'.iches-uat-2027@example.test']);
        }
    }
    $realUser = User::factory()->create(['email' => 'finance@iches.test']);
    $before = [];
    foreach (['users', 'conference_editions', 'conference_series', 'participation_packages', 'edition_memberships', 'registrations', 'roles', 'permissions'] as $table) {
        $before[$table] = DB::table($table)->get()->toJson();
    }

    expect(fn () => $this->seed(LocalConferenceUatSeeder::class))->toThrow(RuntimeException::class, 'collision');

    foreach ($before as $table => $rows) {
        expect(DB::table($table)->get()->toJson())->toBe($rows);
    }
    expect($realUser->fresh()->name)->toBe($realUser->name)
        ->and(config('demo_roles.edition_code'))->toBe('PREVIOUS');
})->with([false, true]);

test('both seeders require the UAT marker on an existing Edition', function (string $seeder) {
    config(['demo_roles.enabled' => true]);
    $this->seed(LocalConferenceUatSeeder::class);
    $edition = ConferenceEdition::query()->where('edition_code', LocalConferenceUatSeeder::EDITION_CODE)->sole();
    $edition->update(['settings_json' => ['uat_only' => false]]);
    config(['demo_roles.edition_code' => LocalConferenceUatSeeder::EDITION_CODE]);

    expect(fn () => $this->seed($seeder))->toThrow(RuntimeException::class);
})->with([LocalConferenceUatSeeder::class, EditionPersonaDemoSeeder::class]);

test('a legacy demo name without its required Edition relationship cannot be migrated', function () {
    config(['demo_roles.enabled' => true]);
    $this->seed(LocalConferenceUatSeeder::class);
    $author = User::query()->where('email', 'author@iches.test')->sole();
    $author->update(['email' => 'demo+author.iches-uat-2027@example.test']);
    // This unrelated identity has a plausible marker but no UAT membership.
    $other = User::factory()->create([
        'email' => 'demo+participant.iches-uat-2027@example.test', 'name' => 'ICHES Demo · participant',
    ]);
    User::query()->where('email', 'participant@iches.test')->update(['email' => 'original-participant@iches.test']);
    $before = $other->fresh()->getRawOriginal();

    expect(fn () => $this->seed(LocalConferenceUatSeeder::class))->toThrow(RuntimeException::class, 'Edition membership');
    expect($other->fresh()->getRawOriginal())->toBe($before)
        ->and($author->fresh()->email)->toBe('demo+author.iches-uat-2027@example.test');
});
