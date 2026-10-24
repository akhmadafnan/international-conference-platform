<?php

use App\Actions\Academic\AcademicWorkflow;
use App\Actions\Academic\ClaimAbstractReviewAction;
use App\Models\AcademicScreening;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Permission;
use App\Models\Registration;
use App\Models\ReviewAssignment;
use App\Models\ReviewerTopicEligibility;
use App\Models\Role;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\User;
use App\Support\Academic\AbstractPoolEligibility;
use App\Support\Authorization\ActiveConferenceEditionContext;
use App\Support\Submission\InitialSubmissionSnapshotBuilder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    // Fresh connection lifetime, never RefreshDatabase/migrate:fresh/rollback or an existing database.
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    expect(DB::connection()->getDriverName())->toBe('sqlite')->and(DB::connection()->getDatabaseName())->toBe(':memory:');
    $this->artisan('migrate', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
    $this->withoutVite();
    $series = ConferenceSeries::query()->create(['code' => 'CLAIM', 'name' => 'Claim fixture', 'status' => 'ACTIVE']);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => 'CLAIM-2027', 'year' => 2027, 'host_name' => 'Test',
        'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta', 'lifecycle_status' => 'DRAFT',
        'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonths(2),
    ]);
    $this->other = $this->edition->replicate();
    $this->other->edition_code = 'OTHER-2027';
    $this->other->save();
    $this->track = $this->edition->tracks()->create(['code' => 'T01', 'name_i18n' => ['en' => 'Topic'], 'active' => true]);
    $this->author = User::factory()->create();
    $this->reviewer = User::factory()->create();
    $this->second = User::factory()->create();
    $this->committee = User::factory()->create();
    app(ActiveConferenceEditionContext::class)->activate($this->edition->id);
    setPermissionsTeamId($this->edition->id);
    $this->role = Role::create(['name' => 'reviewer', 'guard_name' => 'web', 'conference_edition_id' => $this->edition->id]);
    foreach ([$this->reviewer, $this->second] as $reviewer) {
        $reviewer->assignRole($this->role);
        ReviewerTopicEligibility::query()->create(['edition_id' => $this->edition->id, 'reviewer_id' => $reviewer->id, 'track_id' => $this->track->id, 'assigned_by' => $this->committee->id, 'active' => true]);
    }
    $this->committee->givePermissionTo(Permission::findOrCreate('review.assign', 'web'));
    $membership = EditionMembership::query()->create(['edition_id' => $this->edition->id, 'user_id' => $this->author->id, 'membership_status' => 'ACTIVE', 'joined_at' => now()]);
    $package = ParticipationPackage::query()->create(['edition_id' => $this->edition->id, 'code' => 'AUTHOR', 'name_i18n' => ['en' => 'Author'], 'billing_mode' => 'FREE', 'price' => 0, 'currency_code' => 'IDR', 'active' => true]);
    $registration = Registration::query()->create(['membership_id' => $membership->id, 'registration_code' => 'CLAIM-REG', 'package_id' => $package->id, 'package_name_snapshot' => 'Author', 'billing_mode' => 'FREE', 'fee_amount' => 0, 'currency_code' => 'IDR', 'status' => 'PENDING']);
    $this->paper = Submission::query()->create(['edition_id' => $this->edition->id, 'registration_id' => $registration->id, 'paper_code' => 'CLAIM-P-0001', 'primary_locale' => 'en', 'academic_status' => 'SUBMITTED', 'current_abstract_version' => 1, 'track_id' => $this->track->id, 'submitted_at' => now()]);
    $this->paper->translations()->create(['locale' => 'en', 'title' => 'Test paper', 'abstract_text' => 'Abstract content']);
    $this->paper->contributors()->create(['linked_user_id' => $this->author->id, 'sequence' => 1, 'display_name' => 'Author', 'email' => $this->author->email, 'country_code' => 'ID', 'is_corresponding' => true, 'contributor_role' => 'AUTHOR']);
    $builder = app(InitialSubmissionSnapshotBuilder::class);
    $metadata = $builder->build($this->paper);
    $this->snapshot = $this->paper->snapshots()->create(['snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => $metadata, 'checksum' => $builder->checksum($metadata), 'created_by_user_id' => $this->author->id]);
    $this->claim = app(ClaimAbstractReviewAction::class);
    $this->pool = app(AbstractPoolEligibility::class);
    $this->data = ['version' => 1, 'snapshot_id' => $this->snapshot->id, 'no_conflict_declared' => true];
    $this->withSession([ActiveConferenceEditionContext::SESSION_KEY => $this->edition->id]);
});

afterEach(function () {
    setPermissionsTeamId(null);
    app(ActiveConferenceEditionContext::class)->clear();
});

function legacyClaimAssignment(object $test, string $status = 'INVITED', string $stage = 'ABSTRACT'): ReviewAssignment
{
    return ReviewAssignment::query()->create([
        'submission_id' => $test->paper->id, 'snapshot_id' => $test->snapshot->id, 'reviewer_id' => $test->second->id,
        'assigned_by' => $test->committee->id, 'academic_stage' => $stage, 'status' => $status, 'screening_note' => 'Legacy human assignment',
    ]);
}

test('valid pool projection and claim need no PASS and proceed through existing draft and final review', function () {
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeTrue();
    $row = $this->pool->query($this->reviewer, $this->edition)->sole()->toArray();
    expect(array_keys($row))->toBe(['id', 'edition_id', 'track_id', 'paper_code', 'academic_status', 'current_abstract_version']);
    $before = $this->snapshot->fresh()->getRawOriginal();
    $assignment = $this->claim->handle($this->reviewer, $this->paper, $this->data);
    expect($assignment->status)->toBe('ACCEPTED')->and($assignment->anonymity)->toBe('SINGLE_ANONYMOUS')
        ->and($assignment->academic_stage)->toBe('ABSTRACT')->and($assignment->assignment_source)->toBe('SELF_CLAIM')
        ->and($this->paper->fresh()->academic_status)->toBe('UNDER_REVIEW')->and(AcademicScreening::query()->count())->toBe(0)
        ->and($this->snapshot->fresh()->getRawOriginal())->toBe($before);
    $this->actingAs($this->reviewer)->put(route('academic.review.save', $assignment), ['final' => false, 'author_comments' => 'Draft'])->assertSessionHasNoErrors();
    $this->put(route('academic.review.save', $assignment), ['final' => true, 'recommendation' => 'ACCEPT', 'author_comments' => 'Recommendation', 'confidential_comments' => 'PRIVATE REVIEW'])->assertSessionHasNoErrors();
    expect($assignment->fresh()->status)->toBe('COMPLETED')->and($this->paper->fresh()->academic_status)->toBe('UNDER_REVIEW')
        ->and(AcademicScreening::query()->count())->toBe(0)->and(DB::table('academic_decisions')->count())->toBe(0);
    $audit = DB::table('activity_log')->where('event', 'abstract_review_claimed')->sole();
    expect($audit->causer_id)->toBe($this->reviewer->id)->and($audit->properties)->not->toContain('PRIVATE REVIEW');
});

test('same-reviewer retries are idempotent and a competing reviewer loses without rewriting history', function () {
    $before = $this->snapshot->fresh()->getRawOriginal();
    $first = $this->claim->handle($this->reviewer, $this->paper, $this->data);
    $attributes = $first->fresh()->getRawOriginal();
    expect($this->claim->handle($this->reviewer, $this->paper, $this->data)->id)->toBe($first->id);
    expect(fn () => $this->claim->handle($this->second, $this->paper, $this->data))->toThrow(ValidationException::class);
    expect($first->fresh()->getRawOriginal())->toBe($attributes)->and($this->snapshot->fresh()->getRawOriginal())->toBe($before)
        ->and(ReviewAssignment::query()->count())->toBe(1)->and(DB::table('activity_log')->where('event', 'abstract_review_claimed')->count())->toBe(1);
});

test('invalid designation Topic state or snapshot excludes pool and rejects claim', function (string $case) {
    if ($case === 'unverified') {
        $this->reviewer->forceFill(['email_verified_at' => null])->save();
    } elseif ($case === 'no designation') {
        $this->reviewer->removeRole('reviewer');
    } elseif ($case === 'permissionful designation') {
        $this->role->givePermissionTo(Permission::findOrCreate('review.assign', 'web'));
    } elseif ($case === 'inactive eligibility') {
        ReviewerTopicEligibility::query()->where('reviewer_id', $this->reviewer->id)->update(['active' => false]);
    } elseif ($case === 'inactive Topic') {
        $this->track->update(['active' => false]);
    } elseif ($case === 'no Topic') {
        $this->paper->update(['track_id' => null]);
    } elseif ($case === 'foreign Topic') {
        $track = $this->other->tracks()->create(['code' => 'OTHER', 'name_i18n' => ['en' => 'Other'], 'active' => true]);
        $this->paper->update(['track_id' => $track->id]);
    } elseif ($case === 'archived') {
        $this->edition->update(['lifecycle_status' => 'ARCHIVED']);
    } elseif ($case === 'checksum') {
        $this->snapshot->update(['checksum' => str_repeat('0', 64)]);
    } elseif ($case === 'identity' || $case === 'empty abstract') {
        $data = $this->snapshot->metadata_json;
        if ($case === 'identity') {
            $data['submission_id'] = (string) Str::uuid();
        } else {
            $data['translations'][0]['abstract'] = '';
        }
        $this->snapshot->update(['metadata_json' => $data, 'checksum' => app(InitialSubmissionSnapshotBuilder::class)->checksum($data)]);
    } elseif ($case === 'ambiguous snapshot') {
        $snapshot = $this->snapshot->replicate();
        $snapshot->snapshot_type = 'ADMIN_CORRECTION';
        $snapshot->save();
    } elseif ($case === 'wrong version') {
        $this->paper->update(['current_abstract_version' => 2]);
    } else {
        $this->paper->update(['academic_status' => $case]);
    }
    expect($this->pool->allows($this->reviewer, $this->paper->fresh()))->toBeFalse();
    expect(fn () => $this->claim->handle($this->reviewer, $this->paper, $this->data))->toThrow($case === 'unverified' ? AuthorizationException::class : ValidationException::class);
    expect(ReviewAssignment::query()->count())->toBe(0)->and(DB::table('activity_log')->count())->toBe(0);
})->with(['unverified', 'no designation', 'permissionful designation', 'inactive eligibility', 'inactive Topic', 'no Topic', 'foreign Topic', 'archived', 'checksum', 'identity', 'empty abstract', 'ambiguous snapshot', 'wrong version', 'DRAFT', 'ACCEPTED', 'REJECTED']);

test('COI declaration and request snapshot version are mandatory and stale requests fail safely', function (string $case) {
    $data = $this->data;
    if ($case === 'no declaration') {
        unset($data['no_conflict_declared']);
    } elseif ($case === 'declared conflict') {
        $data['no_conflict_declared'] = false;
    } elseif ($case === 'stale snapshot') {
        $data['snapshot_id'] = (string) Str::uuid();
    } else {
        $data['version'] = 2;
    }
    expect(fn () => $this->claim->handle($this->reviewer, $this->paper, $data))->toThrow(ValidationException::class);
    expect(ReviewAssignment::query()->count())->toBe(0);
})->with(['no declaration', 'declared conflict', 'stale snapshot', 'stale version']);

test('authors contributors known COI and foreign Edition contexts cannot claim', function (string $case) {
    if ($case === 'owner') {
        $this->paper->registration->membership->update(['user_id' => $this->reviewer->id]);
    } elseif ($case === 'linked contributor' || $case === 'email contributor') {
        $this->paper->contributors()->create(['linked_user_id' => $case === 'linked contributor' ? $this->reviewer->id : null, 'sequence' => 2, 'display_name' => 'Contributor', 'email' => $case === 'email contributor' ? strtoupper($this->reviewer->email) : 'other@test.example', 'country_code' => 'ID']);
    } elseif ($case === 'COI' || $case === 'future COI') {
        ReviewAssignment::query()->create(['submission_id' => $this->paper->id, 'snapshot_id' => $this->snapshot->id, 'reviewer_id' => $this->reviewer->id, 'assigned_by' => $this->committee->id, 'academic_stage' => $case === 'future COI' ? 'CONFERENCE' : 'ABSTRACT', 'status' => 'COI', 'screening_note' => 'Recorded conflict']);
        expect($this->pool->allows($this->reviewer, $this->paper))->toBeFalse();
    } else {
        app(ActiveConferenceEditionContext::class)->activate($this->other->id);
        setPermissionsTeamId($this->other->id);
    }
    expect(fn () => $this->claim->handle($this->reviewer, $this->paper, $this->data))->toThrow(AuthorizationException::class);
    expect(DB::table('activity_log')->count())->toBe(0);
})->with(['owner', 'linked contributor', 'email contributor', 'COI', 'future COI', 'foreign context']);

test('latest technical exceptions block claims while PASS is optional history', function (string $outcome) {
    AcademicScreening::query()->create(['submission_id' => $this->paper->id, 'abstract_version' => 1, 'outcome' => $outcome, 'note' => 'Human exception', 'actor_id' => $this->committee->id]);
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeFalse();
    expect(fn () => $this->claim->handle($this->reviewer, $this->paper, $this->data))->toThrow(ValidationException::class);
    AcademicScreening::query()->create(['submission_id' => $this->paper->id, 'abstract_version' => 1, 'outcome' => 'PASS', 'note' => 'Resolved', 'actor_id' => $this->committee->id]);
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeTrue();
})->with(['INELIGIBLE', 'CORRECTION_REQUIRED']);

test('legacy active or completed assignments block claims and stay readable', function (string $status) {
    $legacy = legacyClaimAssignment($this, $status);
    $before = $legacy->fresh()->getRawOriginal();
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeFalse();
    expect(fn () => $this->claim->handle($this->reviewer, $this->paper, $this->data))->toThrow(ValidationException::class);
    expect($legacy->fresh()->getRawOriginal())->toBe($before)->and($legacy->assignment_source)->toBe('MANUAL');
})->with(['INVITED', 'ACCEPTED', 'COMPLETED']);

test('database constraint blocks a second active owner even with direct inserts and across snapshots', function () {
    legacyClaimAssignment($this);
    $snapshot = $this->snapshot->replicate();
    $snapshot->version = 2;
    $snapshot->save();
    expect(fn () => ReviewAssignment::query()->create([
        'submission_id' => $this->paper->id, 'snapshot_id' => $snapshot->id, 'reviewer_id' => $this->reviewer->id,
        'assigned_by' => $this->committee->id, 'status' => 'ACCEPTED', 'screening_note' => 'Direct conflicting writer',
    ]))->toThrow(QueryException::class);
    expect(ReviewAssignment::query()->whereIn('status', ['INVITED', 'ACCEPTED'])->count())->toBe(1);
});

test('stage separation permits a future-stage owner but forbids using it through abstract review operations', function () {
    $other = legacyClaimAssignment($this, 'ACCEPTED', 'CONFERENCE');
    $other->update(['reviewer_id' => $this->reviewer->id]);
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeTrue();
    $abstract = $this->claim->handle($this->reviewer, $this->paper, $this->data);
    expect($abstract->academic_stage)->toBe('ABSTRACT')->and(ReviewAssignment::query()->count())->toBe(2);
    expect(fn () => app(AcademicWorkflow::class)->review($this->reviewer, $other, ['final' => false]))->toThrow(ValidationException::class);
    expect(fn () => app(AcademicWorkflow::class)->cancel($this->committee, $other, 'Wrong stage'))->toThrow(ValidationException::class);
});

test('designation and eligibility alone do not grant packet or private file access and competing users stay denied', function () {
    $this->actingAs($this->reviewer)->get(route('academic.file', $this->paper))->assertForbidden();
    app(ActiveConferenceEditionContext::class)->activate($this->edition->id);
    setPermissionsTeamId($this->edition->id);
    $assignment = $this->claim->handle($this->reviewer, $this->paper, $this->data);
    $this->actingAs($this->second)->get(route('academic.review', $assignment))->assertNotFound();
    $this->get(route('academic.review.file', $assignment))->assertNotFound();
    expect($this->reviewer->getAllPermissions())->toBeEmpty();
});

test('only the claimed reviewer can download the exact private snapshot file and cancellation revokes access', function () {
    $bytes = '%PDF-1.4 isolated manuscript';
    $file = StoredFile::query()->create(['disk' => 'private', 'path' => 'isolated-claim/paper.pdf', 'original_name' => 'paper.pdf', 'mime_type' => 'application/pdf', 'size_bytes' => strlen($bytes), 'checksum_sha256' => hash('sha256', $bytes), 'visibility_class' => 'PRIVATE', 'uploaded_by_user_id' => $this->author->id]);
    $metadata = $this->snapshot->metadata_json;
    $metadata['abstract_file'] = ['stored_file_id' => $file->id];
    $this->snapshot->update(['metadata_json' => $metadata, 'checksum' => app(InitialSubmissionSnapshotBuilder::class)->checksum($metadata)]);
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('exists')->once()->with($file->path)->andReturnTrue();
    $disk->shouldReceive('get')->once()->with($file->path)->andReturn($bytes);
    Storage::shouldReceive('disk')->twice()->with('private')->andReturn($disk);
    $assignment = $this->claim->handle($this->reviewer, $this->paper, $this->data);
    $this->actingAs($this->second)->get(route('academic.review.file', $assignment))->assertNotFound();
    $this->actingAs($this->reviewer)->get(route('academic.review.file', $assignment))->assertOk()->assertContent($bytes);
    app(ActiveConferenceEditionContext::class)->activate($this->edition->id);
    setPermissionsTeamId($this->edition->id);
    app(AcademicWorkflow::class)->cancel($this->committee, $assignment, 'Human release');
    $this->get(route('academic.review.file', $assignment))->assertSessionHasErrors('academic');
});

test('database rejects conflicting status activation and releases slots without deleting history', function () {
    $old = legacyClaimAssignment($this, 'CANCELLED');
    $first = $this->claim->handle($this->reviewer, $this->paper, $this->data);
    expect(fn () => $old->update(['status' => 'ACCEPTED']))->toThrow(QueryException::class);
    expect($old->fresh()->status)->toBe('CANCELLED');
    app(AcademicWorkflow::class)->cancel($this->committee, $first, 'Human release reason');
    expect($first->fresh()->status)->toBe('CANCELLED')->and($first->fresh()->active_owner_slot)->toBeNull();
    expect(fn () => $this->claim->handle($this->reviewer, $this->paper, $this->data))->toThrow(ValidationException::class);
    expect(ReviewAssignment::query()->count())->toBe(2);
});

test('a cancelled historical assignment of another reviewer does not prevent a new claim', function () {
    $legacy = legacyClaimAssignment($this, 'CANCELLED');
    $before = $legacy->fresh()->getRawOriginal();
    $this->claim->handle($this->reviewer, $this->paper, $this->data);
    expect($legacy->fresh()->getRawOriginal())->toBe($before);
});

test('manual assignment cannot create a conflicting owner after a self-claim', function () {
    AcademicScreening::query()->create(['submission_id' => $this->paper->id, 'abstract_version' => 1, 'outcome' => 'PASS', 'note' => 'Historical check', 'actor_id' => $this->committee->id]);
    $this->claim->handle($this->reviewer, $this->paper, $this->data);
    expect(fn () => app(AcademicWorkflow::class)->assign($this->committee, $this->paper, [
        'version' => 1, 'reviewer_email' => $this->second->email, 'coi_screened' => true, 'screening_note' => 'Manual attempt',
    ]))->toThrow(ValidationException::class);
    expect(ReviewAssignment::query()->count())->toBe(1);
});

test('claim transaction rolls back on audit failure', function () {
    $dispatcher = Activity::getEventDispatcher();
    $isolated = clone $dispatcher;
    Activity::setEventDispatcher($isolated);
    $isolated->listen('eloquent.creating: '.Activity::class, function (): void {
        throw new RuntimeException('Simulated audit failure');
    });
    try {
        expect(fn () => $this->claim->handle($this->reviewer, $this->paper, $this->data))->toThrow(RuntimeException::class);
        expect(ReviewAssignment::query()->count())->toBe(0)->and($this->paper->fresh()->academic_status)->toBe('SUBMITTED')
            ->and(DB::table('activity_log')->count())->toBe(0);
    } finally {
        Activity::setEventDispatcher($dispatcher);
    }
});

test('migration upgrades nonempty legacy rows without rewriting evidence and refuses conflicting owners before DDL', function (bool $conflict) {
    config(['database.connections.claim_legacy' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true]]);
    $previous = DB::getDefaultConnection();
    DB::setDefaultConnection('claim_legacy');
    try {
        Schema::create('review_assignments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('submission_id');
            $table->uuid('snapshot_id');
            $table->uuid('reviewer_id');
            $table->string('status');
            $table->text('screening_note');
            $table->unique(['snapshot_id', 'reviewer_id']);
        });
        $old = ['id' => (string) Str::uuid(), 'submission_id' => $this->paper->id, 'snapshot_id' => $this->snapshot->id, 'reviewer_id' => $this->reviewer->id, 'status' => 'ACCEPTED', 'screening_note' => 'Unchanged legacy evidence'];
        DB::table('review_assignments')->insert($old);
        if ($conflict) {
            DB::table('review_assignments')->insert(array_replace($old, ['id' => (string) Str::uuid(), 'reviewer_id' => $this->second->id]));
        }
        $migration = require base_path('database/migrations/2026_10_24_120000_add_exclusive_stage_ownership_to_review_assignments.php');
        if ($conflict) {
            expect(fn () => $migration->up())->toThrow(RuntimeException::class, 'Conflicting legacy');
            expect(Schema::hasColumn('review_assignments', 'academic_stage'))->toBeFalse();
        } else {
            $migration->up();
            $row = (array) DB::table('review_assignments')->sole();
            foreach ($old as $key => $value) {
                expect($row[$key])->toBe($value);
            }
            expect($row['academic_stage'])->toBe('ABSTRACT')->and($row['assignment_source'])->toBe('MANUAL')->and($row['active_owner_slot'])->toBe(1);
        }
    } finally {
        DB::setDefaultConnection($previous);
    }
})->with([false, true]);
