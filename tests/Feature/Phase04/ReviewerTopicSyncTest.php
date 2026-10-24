<?php

use App\Actions\Academic\ManageReviewerTopicEligibilityAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Permission;
use App\Models\Registration;
use App\Models\ReviewAssignment;
use App\Models\ReviewerTopicEligibility;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->withoutVite();
    $series = ConferenceSeries::query()->create(['code' => 'SYNC', 'name' => 'Sync fixture', 'status' => 'ACTIVE']);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => 'SYNC-2027', 'year' => 2027, 'host_name' => 'Test',
        'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta', 'lifecycle_status' => 'DRAFT',
        'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonths(2),
    ]);
    $this->other = $this->edition->replicate();
    $this->other->edition_code = 'OTHER-2027';
    $this->other->save();
    $this->tracks = collect(range(1, 8))->map(fn ($i) => $this->edition->tracks()->create([
        'code' => sprintf('T%02d', $i), 'name_i18n' => ['en' => 'Topic '.$i], 'active' => true,
    ]));
    $this->foreignTrack = $this->other->tracks()->create(['code' => 'T01', 'name_i18n' => ['en' => 'Other'], 'active' => true]);
    $this->committee = User::factory()->create();
    $this->reviewer = User::factory()->create(['name' => 'ZZZ Reviewer']);
    app(ActiveConferenceEditionContext::class)->activate($this->edition->id);
    setPermissionsTeamId($this->edition->id);
    $this->reviewer->assignRole(Role::create(['name' => 'reviewer', 'guard_name' => 'web', 'conference_edition_id' => $this->edition->id]));
    $this->committee->givePermissionTo(Permission::findOrCreate('review.assign', 'web'));
    $this->withSession([ActiveConferenceEditionContext::SESSION_KEY => $this->edition->id]);
    $this->manage = app(ManageReviewerTopicEligibilityAction::class);
});

afterEach(function () {
    setPermissionsTeamId(null);
    app(ActiveConferenceEditionContext::class)->clear();
});

function topicSyncData(object $test, array $indices, bool $existing = true): array
{
    return [
        'edition_id' => $test->edition->id,
        'reviewer_id' => $test->reviewer->id,
        'track_ids' => array_map(fn ($i) => $test->tracks[$i]->id, $indices),
        'expected_state' => $existing ? $test->manage->stateToken($test->edition->id, $test->reviewer->id) : 'new',
    ];
}

test('reviewer synchronization replaces the complete set atomically preserves identities and audits only transitions', function () {
    $this->actingAs($this->committee)->put(route('academic.reviewer-topics.sync'), topicSyncData($this, [0, 1], false))->assertSessionHasNoErrors();
    $ids = ReviewerTopicEligibility::query()->pluck('id', 'track_id');
    $data = topicSyncData($this, [1, 2]);
    $this->put(route('academic.reviewer-topics.sync'), $data)->assertSessionHasNoErrors();
    $rows = DB::table('reviewer_topic_eligibilities')->orderBy('id')->get()->toJson();
    $audit = DB::table('activity_log')->orderBy('id')->get()->toJson();
    $this->put(route('academic.reviewer-topics.sync'), $data)->assertSessionHasNoErrors();
    expect(DB::table('reviewer_topic_eligibilities')->orderBy('id')->get()->toJson())->toBe($rows)
        ->and(DB::table('activity_log')->orderBy('id')->get()->toJson())->toBe($audit)
        ->and(ReviewerTopicEligibility::query()->where('track_id', $this->tracks[0]->id)->sole()->id)->toBe($ids[$this->tracks[0]->id])
        ->and(ReviewerTopicEligibility::query()->where('active', true)->pluck('track_id')->sort()->values()->all())->toBe(collect([$this->tracks[1]->id, $this->tracks[2]->id])->sort()->values()->all());
    $this->put(route('academic.reviewer-topics.sync'), topicSyncData($this, [0]))->assertSessionHasNoErrors();
    expect(ReviewerTopicEligibility::query()->where('track_id', $this->tracks[0]->id)->sole()->id)->toBe($ids[$this->tracks[0]->id])
        ->and(DB::table('activity_log')->where('event', 'reviewer_topic_granted')->count())->toBe(3)
        ->and(DB::table('activity_log')->where('event', 'reviewer_topic_deactivated')->count())->toBe(3)
        ->and(DB::table('activity_log')->where('event', 'reviewer_topic_reactivated')->count())->toBe(1);
    $data = topicSyncData($this, []);
    $this->put(route('academic.reviewer-topics.sync'), $data)->assertSessionHasNoErrors();
    $this->put(route('academic.reviewer-topics.sync'), $data)->assertSessionHasNoErrors();
    expect(ReviewerTopicEligibility::query()->count())->toBe(3)->and(ReviewerTopicEligibility::query()->where('active', true)->count())->toBe(0);
    $this->get(route('academic.reviewer-topics.index'))->assertInertia(fn ($page) => $page
        ->has('reviewers', 1)->has('reviewers.0.topics', 0)->where('pagination.total', 1));
});

test('sync rejects invalid inactive foreign duplicate missing selections and archived Editions without partial writes', function (string $case) {
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [0, 1], false));
    $data = topicSyncData($this, [2]);
    if ($case === 'inactive') {
        $this->tracks[3]->update(['active' => false]);
        $data['track_ids'][] = $this->tracks[3]->id;
    } elseif ($case === 'foreign') {
        $data['track_ids'][] = $this->foreignTrack->id;
    } elseif ($case === 'duplicate') {
        $data['track_ids'][] = $this->tracks[2]->id;
    } elseif ($case === 'malformed') {
        $data['track_ids'][] = 'bad-id';
    } elseif ($case === 'missing') {
        unset($data['track_ids']);
    } else {
        $this->edition->update(['lifecycle_status' => 'ARCHIVED']);
    }
    $before = DB::table('reviewer_topic_eligibilities')->get()->toJson();
    $audit = DB::table('activity_log')->count();
    expect(fn () => $this->manage->sync($this->committee, $this->edition, $data))->toThrow(ValidationException::class);
    expect(DB::table('reviewer_topic_eligibilities')->get()->toJson())->toBe($before)->and(DB::table('activity_log')->count())->toBe($audit);
})->with(['inactive', 'foreign', 'duplicate', 'malformed', 'missing', 'archived']);

test('stale updates cannot overwrite newer changes including an ABA change within the same second', function () {
    $this->freezeTime();
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [0, 2], false));
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [0]));
    $stale = topicSyncData($this, [1]);
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [2]));
    expect(fn () => $this->manage->sync($this->committee, $this->edition, $stale))->toThrow(ValidationException::class);
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [0]));
    expect(fn () => $this->manage->sync($this->committee, $this->edition, $stale))->toThrow(ValidationException::class);
    $data = topicSyncData($this, [1]);
    unset($data['expected_state']);
    expect(fn () => $this->manage->sync($this->committee, $this->edition, $data))->toThrow(ValidationException::class);
});

test('reviewer identity rejects unverified and absent accounts', function (string $case) {
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [0], false));
    $data = topicSyncData($this, [1]);
    if ($case === 'unverified') {
        $this->reviewer->forceFill(['email_verified_at' => null])->save();
    } else {
        $data['reviewer_id'] = (string) Str::uuid();
    }
    $before = DB::table('reviewer_topic_eligibilities')->get()->toJson();
    expect(fn () => $this->manage->sync($this->committee, $this->edition, $data))->toThrow(ValidationException::class);
    expect(DB::table('reviewer_topic_eligibilities')->get()->toJson())->toBe($before);
})->with(['unverified', 'absent']);

test('unauthorized unverified and foreign Edition mutations fail', function (string $case) {
    $actor = $this->committee;
    $edition = $this->edition;
    if ($case === 'unauthorized') {
        $actor = $this->reviewer;
    } elseif ($case === 'unverified') {
        $actor->forceFill(['email_verified_at' => null])->save();
    } else {
        $edition = $this->other;
    }
    expect(fn () => $this->manage->sync($actor, $edition, topicSyncData($this, [0], false)))->toThrow(AuthorizationException::class);
    expect(ReviewerTopicEligibility::query()->count())->toBe(0);
    $this->actingAs($actor)->put(route('academic.reviewer-topics.sync'), array_replace(topicSyncData($this, [0], false), ['edition_id' => $edition->id]))
        ->assertStatus($case === 'unverified' ? 302 : 403);
})->with(['unauthorized', 'unverified', 'foreign Edition']);

test('inactive historical eligibility and every unrelated table remain unchanged by synchronization', function () {
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [0, 1], false));
    $this->tracks[0]->update(['active' => false]);
    $historical = ReviewerTopicEligibility::query()->where('track_id', $this->tracks[0]->id)->sole()->getRawOriginal();
    $foreign = ReviewerTopicEligibility::query()->create(['edition_id' => $this->other->id, 'reviewer_id' => $this->reviewer->id, 'track_id' => $this->foreignTrack->id, 'assigned_by' => $this->committee->id, 'active' => true]);
    $membership = EditionMembership::query()->create(['edition_id' => $this->edition->id, 'user_id' => $this->committee->id, 'membership_status' => 'ACTIVE', 'joined_at' => now()]);
    $package = ParticipationPackage::query()->create(['edition_id' => $this->edition->id, 'code' => 'AUTHOR', 'name_i18n' => ['en' => 'Author'], 'billing_mode' => 'FREE', 'price' => 0, 'currency_code' => 'IDR', 'active' => true]);
    $registration = Registration::query()->create(['membership_id' => $membership->id, 'registration_code' => 'SYNC-REG', 'package_id' => $package->id, 'package_name_snapshot' => 'Author', 'billing_mode' => 'FREE', 'fee_amount' => 0, 'currency_code' => 'IDR', 'status' => 'PENDING']);
    $paper = Submission::query()->create(['edition_id' => $this->edition->id, 'registration_id' => $registration->id, 'paper_code' => 'ICHES-UAT-2027-P-0001', 'primary_locale' => 'en', 'academic_status' => 'SUBMITTED', 'current_abstract_version' => 1, 'track_id' => null]);
    $snapshot = $paper->snapshots()->create(['snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => ['track_id' => null], 'checksum' => hash('sha256', 'immutable history'), 'created_by_user_id' => $this->committee->id]);
    $assignment = ReviewAssignment::query()->create(['submission_id' => $paper->id, 'snapshot_id' => $snapshot->id, 'reviewer_id' => $this->reviewer->id, 'assigned_by' => $this->committee->id, 'status' => 'COMPLETED', 'screening_note' => 'Prior check']);
    $assignment->review()->create(['status' => 'SUBMITTED', 'recommendation' => 'ACCEPT', 'confidential_comments' => 'PRIVATE MANUSCRIPT CONTENT', 'submitted_at' => now()]);
    $auditBefore = DB::table('activity_log')->orderBy('id')->get()->toJson();
    $lastAuditId = DB::table('activity_log')->max('id');
    $before = [];
    foreach (Schema::getTableListing(schemaQualified: false) as $table) {
        if (! in_array($table, ['reviewer_topic_eligibilities', 'activity_log'], true)) {
            $before[$table] = DB::table($table)->get()->toJson();
        }
    }
    $foreignBefore = $foreign->fresh()->getRawOriginal();
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, []));
    expect(ReviewerTopicEligibility::query()->where('track_id', $this->tracks[0]->id)->sole()->getRawOriginal())->toBe($historical)
        ->and($foreign->fresh()->getRawOriginal())->toBe($foreignBefore);
    foreach ($before as $table => $rows) {
        expect(DB::table($table)->get()->toJson(), $table)->toBe($rows);
    }
    expect(DB::table('activity_log')->where('id', '<=', $lastAuditId)->orderBy('id')->get()->toJson())->toBe($auditBefore)
        ->and(DB::table('activity_log')->where('id', '>', $lastAuditId)->get()->toJson())->not->toContain('PRIVATE MANUSCRIPT CONTENT');
});

test('new reviewers require Topics and Add cannot replace an existing reviewer selection', function () {
    expect(fn () => $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [], false)))->toThrow(ValidationException::class);
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [0], false));
    expect(fn () => $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [1], false)))->toThrow(ValidationException::class);
});

test('reviewer pagination keeps each complete Topic set together and excludes foreign Edition reviewers', function () {
    foreach (range(1, 31) as $i) {
        $reviewer = User::factory()->create(['name' => sprintf('Reviewer %02d', $i)]);
        $reviewer->assignRole('reviewer');
        $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $reviewer->email, 'track_ids' => $this->tracks->pluck('id')->all()]);
    }
    $this->manage->sync($this->committee, $this->edition, topicSyncData($this, [0], false));
    $this->actingAs($this->committee)->get(route('academic.reviewer-topics.index'))->assertInertia(fn ($page) => $page
        ->has('tracks', 8)->has('reviewers', 30)->has('reviewers.0.topics', 8)->where('pagination.total', 32)->where('pagination.perPage', 30));
    $this->get(route('academic.reviewer-topics.index', ['page' => 2]))->assertInertia(fn ($page) => $page
        ->has('reviewers', 2)->where('pagination.currentPage', 2));
});
