<?php

use App\Actions\Academic\ManageReviewerTopicEligibilityAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\Permission;
use App\Models\ReviewerTopicEligibility;
use App\Models\Role;
use App\Models\User;
use App\Support\Academic\ReviewerDesignation;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Database\Seeders\LocalConferenceUatReviewerSeeder;
use Database\Seeders\LocalConferenceUatSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function pickerDesignate(User $user, ConferenceEdition $edition): void
{
    $previous = getPermissionsTeamId();
    try {
        setPermissionsTeamId($edition->id);
        $role = Role::query()->firstOrCreate(['name' => ReviewerDesignation::ROLE, 'guard_name' => 'web', 'conference_edition_id' => $edition->id]);
        $user->assignRole($role);
    } finally {
        setPermissionsTeamId($previous);
        $user->unsetRelation('roles')->unsetRelation('permissions');
    }
}

beforeEach(function () {
    $this->withoutVite();
    $series = ConferenceSeries::query()->create(['code' => 'PICKER', 'name' => 'Picker fixture', 'status' => 'ACTIVE']);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => LocalConferenceUatSeeder::EDITION_CODE, 'year' => 2027,
        'host_name' => 'Test', 'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta', 'lifecycle_status' => 'DRAFT',
        'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonths(2), 'settings_json' => ['uat_only' => true],
    ]);
    $this->other = $this->edition->replicate();
    $this->other->edition_code = 'ICHES-2027';
    $this->other->save();
    $this->track = $this->edition->tracks()->create(['code' => 'T01', 'name_i18n' => ['en' => 'Topic'], 'active' => true]);
    $this->committee = User::factory()->create();
    $this->reviewer = User::factory()->create(['name' => 'ICHES Demo · reviewer', 'email' => 'reviewer@iches.test']);
    app(ActiveConferenceEditionContext::class)->activate($this->edition->id);
    setPermissionsTeamId($this->edition->id);
    $this->committee->givePermissionTo(Permission::findOrCreate('review.assign', 'web'));
    $this->withSession([ActiveConferenceEditionContext::SESSION_KEY => $this->edition->id]);
});

afterEach(function () {
    setPermissionsTeamId(null);
    app(ActiveConferenceEditionContext::class)->clear();
});

test('picker lists only verified designated users for the selected Edition and excludes existing reviewers', function () {
    pickerDesignate($this->reviewer, $this->edition);
    $plain = User::factory()->create();
    $unverified = User::factory()->unverified()->create();
    pickerDesignate($unverified, $this->edition);
    $foreign = User::factory()->create();
    pickerDesignate($foreign, $this->other);
    $existing = User::factory()->create();
    pickerDesignate($existing, $this->edition);
    ReviewerTopicEligibility::query()->create(['edition_id' => $this->edition->id, 'reviewer_id' => $existing->id, 'track_id' => $this->track->id, 'assigned_by' => $this->committee->id, 'active' => false]);
    // An incorrectly attached foreign role must not count as a local designation.
    $foreignRole = Role::query()->where('conference_edition_id', $this->other->id)->sole();
    $plain->assignRole($foreignRole);
    $this->actingAs($this->committee)->get(route('academic.reviewer-topics.index'))->assertOk()->assertInertia(fn ($page) => $page
        ->has('reviewerOptions', 1)->where('reviewerOptions.0.id', $this->reviewer->id)
        ->where('reviewerOptions.0.name', $this->reviewer->name)->where('reviewerOptions.0.email', 'reviewer@iches.test')
        ->missing('reviewerOptions.0.password')->has('reviewers', 1)->where('reviewers.0.id', $existing->id));
    expect(ReviewerDesignation::verifiedUsers($this->other->id)->pluck('id')->all())->toBe([$foreign->id]);
});

test('picker never falls back to all users and unauthorized actors cannot enumerate accounts', function () {
    $this->actingAs($this->committee)->get(route('academic.reviewer-topics.index'))->assertInertia(fn ($page) => $page->has('reviewerOptions', 0));
    $this->actingAs($this->reviewer)->get(route('academic.reviewer-topics.index'))->assertForbidden();
    $this->actingAs(User::factory()->unverified()->create())->get(route('academic.reviewer-topics.index'))->assertRedirect(route('verification.notice'));
});

test('picker Save rejects forged or revoked Reviewer identities without changing Topic eligibility', function (string $case) {
    $id = $this->reviewer->id;
    if ($case === 'unverified') {
        pickerDesignate($this->reviewer, $this->edition);
        $this->reviewer->forceFill(['email_verified_at' => null])->save();
    } elseif ($case === 'foreign') {
        pickerDesignate($this->reviewer, $this->other);
    } elseif ($case === 'revoked') {
        pickerDesignate($this->reviewer, $this->edition);
        $this->reviewer->removeRole('reviewer');
    } elseif ($case === 'global') {
        $role = Role::create(['name' => 'reviewer', 'guard_name' => 'web', 'conference_edition_id' => null]);
        $this->reviewer->assignRole($role);
    } elseif ($case === 'absent') {
        $id = (string) Str::uuid();
    }
    $this->actingAs($this->committee)->put(route('academic.reviewer-topics.sync'), [
        'edition_id' => $this->edition->id, 'reviewer_id' => $id,
        'track_ids' => [$this->track->id], 'expected_state' => 'new',
    ])->assertSessionHasErrors('reviewer_id');
    expect(ReviewerTopicEligibility::query()->count())->toBe(0)->and(DB::table('activity_log')->count())->toBe(0);
})->with(['plain', 'unverified', 'foreign', 'revoked', 'global', 'absent']);

test('Add and Edit use immutable user IDs even after an email is assigned to another account', function () {
    pickerDesignate($this->reviewer, $this->edition);
    $data = ['edition_id' => $this->edition->id, 'reviewer_id' => $this->reviewer->id, 'track_ids' => [$this->track->id], 'expected_state' => 'new'];
    $this->actingAs($this->committee)->put(route('academic.reviewer-topics.sync'), $data)->assertSessionHasNoErrors();
    $record = ReviewerTopicEligibility::query()->sole();
    $data['expected_state'] = app(ManageReviewerTopicEligibilityAction::class)->stateToken($this->edition->id, $this->reviewer->id);
    $this->reviewer->update(['email' => 'changed@iches.test']);
    $replacement = User::factory()->create(['email' => 'reviewer@iches.test']);
    pickerDesignate($replacement, $this->edition);
    $data['track_ids'] = [];
    $this->put(route('academic.reviewer-topics.sync'), $data)->assertSessionHasNoErrors();
    expect($record->fresh()->active)->toBeFalse()->and($record->fresh()->reviewer_id)->toBe($this->reviewer->id)
        ->and(ReviewerTopicEligibility::query()->count())->toBe(1);
    $this->put(route('academic.reviewer-topics.sync'), array_diff_key($data, ['reviewer_id' => true]) + ['reviewer_email' => $replacement->email])->assertSessionHasErrors('reviewer_id');
});

test('legacy grant cannot bypass the Reviewer designation and designation alone grants no manuscript access', function () {
    $this->actingAs($this->committee)->post(route('academic.reviewer-topics.store'), [
        'edition_id' => $this->edition->id, 'reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id],
    ])->assertSessionHasErrors('reviewer_email');
    pickerDesignate($this->reviewer, $this->edition);
    expect($this->reviewer->getAllPermissions())->toBeEmpty();
    $this->actingAs($this->reviewer)->get(route('academic.reviewer-topics.index'))->assertForbidden();
    $this->get(route('academic.review.file', Str::uuid()->toString()))->assertNotFound();
    expect(DB::table('review_assignments')->count())->toBe(0);
});

test('demo Reviewer bootstrap is guarded idempotent and preserves every unrelated row and role', function () {
    config(['demo_roles.enabled' => true]);
    $unrelated = Role::create(['name' => 'unrelated', 'guard_name' => 'web', 'conference_edition_id' => $this->edition->id]);
    $this->reviewer->assignRole($unrelated);
    $before = [];
    foreach (Schema::getTableListing(schemaQualified: false) as $table) {
        if (! in_array($table, ['roles', 'model_has_roles', 'activity_log'], true)) {
            $before[$table] = DB::table($table)->get()->toJson();
        }
    }
    $roleBefore = $unrelated->fresh()->getRawOriginal();
    $this->seed(LocalConferenceUatReviewerSeeder::class);
    $rolesAfter = DB::table('roles')->orderBy('id')->get()->toJson();
    $pivotsAfter = DB::table('model_has_roles')->orderBy('role_id')->get()->toJson();
    $this->seed(LocalConferenceUatReviewerSeeder::class);
    expect(DB::table('roles')->orderBy('id')->get()->toJson())->toBe($rolesAfter)
        ->and(DB::table('model_has_roles')->orderBy('role_id')->get()->toJson())->toBe($pivotsAfter)
        ->and($unrelated->fresh()->getRawOriginal())->toBe($roleBefore)
        ->and(getPermissionsTeamId())->toBe($this->edition->id)
        ->and(DB::table('activity_log')->where('event', 'reviewer_designated')->count())->toBe(1)
        ->and($this->reviewer->fresh()->hasRole('reviewer'))->toBeTrue()
        ->and(Role::query()->where('name', 'reviewer')->sole()->permissions()->count())->toBe(0);
    foreach ($before as $table => $rows) {
        expect(DB::table($table)->get()->toJson(), $table)->toBe($rows);
    }
});

test('bootstrap rejects unsafe environment marker identity or permissionful Reviewer role', function (string $case) {
    config(['demo_roles.enabled' => $case !== 'no opt-in']);
    if (in_array($case, ['production', 'staging'], true)) {
        app()->detectEnvironment(static fn (): string => $case);
    } elseif ($case === 'missing Edition') {
        $this->edition->update(['edition_code' => 'MISSING']);
    } elseif ($case === 'marker') {
        $this->edition->update(['settings_json' => ['uat_only' => false]]);
    } elseif ($case === 'unverified') {
        $this->reviewer->forceFill(['email_verified_at' => null])->save();
    } elseif ($case === 'identity collision') {
        $this->reviewer->update(['name' => 'Real account']);
    } elseif ($case === 'missing user') {
        $this->reviewer->update(['email' => 'different@iches.test']);
    } elseif ($case === 'permissionful role') {
        pickerDesignate($this->reviewer, $this->edition);
        Role::query()->where('name', 'reviewer')->sole()->givePermissionTo(Permission::findOrCreate('review.assign', 'web'));
    }
    $tables = ['users', 'roles', 'model_has_roles', 'role_has_permissions'];
    $before = [];
    foreach ($tables as $table) {
        $before[$table] = DB::table($table)->get()->toJson();
    }
    expect(fn () => (new LocalConferenceUatReviewerSeeder)->run())->toThrow(RuntimeException::class);
    foreach ($before as $table => $rows) {
        expect(DB::table($table)->get()->toJson(), $table)->toBe($rows);
    }
    expect(getPermissionsTeamId())->toBe($this->edition->id);
})->with(['no opt-in', 'production', 'staging', 'missing Edition', 'marker', 'unverified', 'identity collision', 'missing user', 'permissionful role']);
