<?php

use App\Actions\Academic\ManageReviewerTopicEligibilityAction;
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
use App\Models\Submission;
use App\Models\User;
use App\Support\Academic\AbstractPoolEligibility;
use App\Support\Authorization\ActiveConferenceEditionContext;
use App\Support\Submission\InitialSubmissionSnapshotBuilder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

function topicFoundationContext(ConferenceEdition $edition): void
{
    app(ActiveConferenceEditionContext::class)->activate($edition->id);
    setPermissionsTeamId($edition->id);
}

beforeEach(function () {
    $this->withoutVite();
    $series = ConferenceSeries::query()->create(['code' => 'TOPICS', 'name' => 'Topics', 'status' => 'ACTIVE']);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => 'TOPICS-2027', 'year' => 2027,
        'host_name' => 'Test', 'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta', 'lifecycle_status' => 'DRAFT',
        'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonths(2),
    ]);
    $this->otherEdition = $this->edition->replicate();
    $this->otherEdition->edition_code = 'OTHER-TOPICS';
    $this->otherEdition->save();
    $this->track = $this->edition->tracks()->create(['code' => 'T01', 'name_i18n' => ['en' => 'Topic 1'], 'active' => true]);
    $this->secondTrack = $this->edition->tracks()->create(['code' => 'T02', 'name_i18n' => ['en' => 'Topic 2'], 'active' => true]);
    $this->foreignTrack = $this->otherEdition->tracks()->create(['code' => 'T01', 'name_i18n' => ['en' => 'Other Topic'], 'active' => true]);
    $this->committee = User::factory()->create();
    $this->reviewer = User::factory()->create();
    $this->author = User::factory()->create();
    topicFoundationContext($this->edition);
    $this->reviewer->assignRole(Role::create(['name' => 'reviewer', 'guard_name' => 'web', 'conference_edition_id' => $this->edition->id]));
    $this->committee->givePermissionTo(Permission::findOrCreate('review.assign', 'web'));
    $this->withSession([ActiveConferenceEditionContext::SESSION_KEY => $this->edition->id]);
    $membership = EditionMembership::query()->create([
        'edition_id' => $this->edition->id, 'user_id' => $this->author->id, 'membership_status' => 'ACTIVE', 'joined_at' => now(),
    ]);
    $package = ParticipationPackage::query()->create([
        'edition_id' => $this->edition->id, 'code' => 'AUTHOR', 'name_i18n' => ['en' => 'Author'],
        'billing_mode' => 'FREE', 'price' => 0, 'currency_code' => 'IDR', 'active' => true,
    ]);
    $registration = Registration::query()->create([
        'membership_id' => $membership->id, 'registration_code' => 'TOPIC-REG', 'package_id' => $package->id,
        'package_name_snapshot' => 'Author', 'billing_mode' => 'FREE', 'fee_amount' => 0, 'currency_code' => 'IDR', 'status' => 'PENDING',
    ]);
    $this->paper = Submission::query()->create([
        'edition_id' => $this->edition->id, 'registration_id' => $registration->id, 'paper_code' => 'TOPICS-P-0001',
        'track_id' => $this->track->id, 'primary_locale' => 'en', 'academic_status' => 'SUBMITTED', 'current_abstract_version' => 1, 'submitted_at' => now(),
    ]);
    $this->paper->translations()->create(['locale' => 'en', 'title' => 'Paper', 'abstract_text' => 'Abstract']);
    $builder = app(InitialSubmissionSnapshotBuilder::class);
    $metadata = $builder->build($this->paper);
    $this->snapshot = $this->paper->snapshots()->create([
        'snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => $metadata,
        'checksum' => $builder->checksum($metadata), 'created_by_user_id' => $this->author->id,
    ]);
    $this->manage = app(ManageReviewerTopicEligibilityAction::class);
    $this->pool = app(AbstractPoolEligibility::class);
});

afterEach(function () {
    setPermissionsTeamId(null);
    app(ActiveConferenceEditionContext::class)->clear();
});

test('Committee grants multiple Topics idempotently and can deactivate and reactivate the same audited identity', function () {
    $data = ['edition_id' => $this->edition->id, 'reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id, $this->secondTrack->id]];
    $this->actingAs($this->committee)->post(route('academic.reviewer-topics.store'), $data)->assertSessionHasNoErrors();
    $this->post(route('academic.reviewer-topics.store'), $data)->assertSessionHasNoErrors();
    expect(ReviewerTopicEligibility::query()->count())->toBe(2)
        ->and(DB::table('activity_log')->where('event', 'reviewer_topic_granted')->count())->toBe(2);
    $record = ReviewerTopicEligibility::query()->where('track_id', $this->track->id)->sole();
    $this->delete(route('academic.reviewer-topics.destroy', $record))->assertSessionHasNoErrors();
    expect($record->fresh()->active)->toBeFalse();
    $this->post(route('academic.reviewer-topics.store'), $data)->assertSessionHasNoErrors();
    topicFoundationContext($this->edition);
    $this->reviewer->unsetRelation('roles')->unsetRelation('permissions');
    expect($record->fresh()->active)->toBeTrue()->and(ReviewerTopicEligibility::query()->count())->toBe(2)
        ->and(DB::table('activity_log')->where('event', 'reviewer_topic_deactivated')->count())->toBe(1)
        ->and(DB::table('activity_log')->where('event', 'reviewer_topic_reactivated')->count())->toBe(1)
        ->and($this->reviewer->getRoleNames()->all())->toBe(['reviewer'])->and($this->reviewer->getAllPermissions())->toBeEmpty();
    $this->get(route('academic.reviewer-topics.index'))->assertOk()->assertInertia(fn ($page) => $page
        ->component('academic/ReviewerTopics')->has('editions', 1)->has('tracks', 2)->has('eligibilities', 2));
});

test('unverified reviewers and inactive or cross-Edition Topics are rejected atomically', function (string $invalid) {
    $trackId = $this->track->id;
    if ($invalid === 'unverified') {
        $this->reviewer->forceFill(['email_verified_at' => null])->save();
    }
    if ($invalid === 'inactive') {
        $this->track->update(['active' => false]);
    }
    if ($invalid === 'foreign') {
        $trackId = $this->foreignTrack->id;
    }
    expect(fn () => $this->manage->grant($this->committee, $this->edition, [
        'reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->secondTrack->id, $trackId],
    ]))->toThrow(ValidationException::class);
    expect(ReviewerTopicEligibility::query()->count())->toBe(0);
})->with(['unverified', 'inactive', 'foreign']);

test('permission and Edition context are enforced on management and foreign eligibility resources', function () {
    $this->actingAs($this->reviewer)->get(route('academic.reviewer-topics.index'))->assertForbidden();
    $this->post(route('academic.reviewer-topics.store'), ['edition_id' => $this->edition->id])->assertForbidden();
    $this->actingAs($this->committee)->post(route('academic.reviewer-topics.edition'), ['edition_id' => $this->otherEdition->id])->assertForbidden();
    $this->post(route('academic.reviewer-topics.store'), ['edition_id' => $this->otherEdition->id])->assertForbidden();
    $foreign = ReviewerTopicEligibility::query()->create([
        'edition_id' => $this->otherEdition->id, 'reviewer_id' => $this->reviewer->id,
        'track_id' => $this->foreignTrack->id, 'assigned_by' => $this->committee->id, 'active' => true,
    ]);
    $this->delete(route('academic.reviewer-topics.destroy', $foreign))->assertForbidden();
    $this->get(route('academic.reviewer-topics.index'))->assertInertia(fn ($page) => $page->has('eligibilities', 0)->has('editions', 1));
    expect($foreign->fresh()->active)->toBeTrue();
});

test('the same reviewer may have independent eligibility in a second authorized Edition', function () {
    $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id]]);
    topicFoundationContext($this->otherEdition);
    $this->reviewer->unsetRelation('roles')->assignRole(Role::create(['name' => 'reviewer', 'guard_name' => 'web', 'conference_edition_id' => $this->otherEdition->id]));
    $this->committee->unsetRelation('permissions')->givePermissionTo(Permission::findOrCreate('review.assign', 'web'));
    $this->manage->grant($this->committee, $this->otherEdition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->foreignTrack->id]]);
    expect(ReviewerTopicEligibility::query()->where('reviewer_id', $this->reviewer->id)->count())->toBe(2);
});

test('valid submitted paper is available without screening PASS and pool returns no confidential or file data', function () {
    $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id]]);
    expect(AcademicScreening::query()->count())->toBe(0)->and($this->pool->allows($this->reviewer, $this->paper))->toBeTrue();
    $row = $this->pool->query($this->reviewer, $this->edition)->sole()->toArray();
    expect(array_keys($row))->toBe(['id', 'edition_id', 'track_id', 'paper_code', 'academic_status', 'current_abstract_version']);
    $this->actingAs($this->reviewer)->get(route('academic.review.file', Str::uuid()->toString()))->assertNotFound();
    $this->get(route('academic.file', $this->paper))->assertForbidden();
    $this->assertDatabaseCount('review_assignments', 0);
});

test('pool excludes unverified inactive ineligible author and co-author reviewers', function (string $case) {
    $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id]]);
    if ($case === 'unverified') {
        User::query()->whereKey($this->reviewer->id)->update(['email_verified_at' => null]);
    }
    if ($case === 'inactive') {
        ReviewerTopicEligibility::query()->update(['active' => false]);
    }
    if ($case === 'no eligibility') {
        ReviewerTopicEligibility::query()->update(['track_id' => $this->secondTrack->id]);
    }
    if ($case === 'author') {
        $this->paper->registration->membership->update(['user_id' => $this->reviewer->id]);
    }
    if (in_array($case, ['linked co-author', 'email co-author'], true)) {
        $this->paper->contributors()->create([
            'linked_user_id' => $case === 'linked co-author' ? $this->reviewer->id : null,
            'email' => $case === 'email co-author' ? strtoupper($this->reviewer->email) : 'other@iches.test',
            'display_name' => 'Co-author', 'sequence' => 1, 'country_code' => 'ID',
        ]);
    }
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeFalse();
})->with(['unverified', 'inactive', 'no eligibility', 'author', 'linked co-author', 'email co-author']);

test('pool rejects other states invalid routing missing current snapshots and unresolved exceptions', function (string $case) {
    $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id]]);
    if (in_array($case, ['DRAFT', 'ACCEPTED', 'REJECTED', 'UNDER_REVIEW'], true)) {
        $this->paper->update(['academic_status' => $case]);
    }
    if ($case === 'no Topic') {
        $this->paper->update(['track_id' => null]);
    }
    if ($case === 'foreign Topic') {
        $this->paper->update(['track_id' => $this->foreignTrack->id]);
    }
    if ($case === 'inactive Topic') {
        $this->track->update(['active' => false]);
    }
    if ($case === 'wrong version') {
        $this->paper->update(['current_abstract_version' => 2]);
    }
    if (in_array($case, ['CORRECTION_REQUIRED', 'INELIGIBLE'], true)) {
        AcademicScreening::query()->create([
            'submission_id' => $this->paper->id, 'abstract_version' => 1, 'outcome' => $case, 'note' => 'Unresolved', 'actor_id' => $this->committee->id,
        ]);
    }
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeFalse();
})->with(['DRAFT', 'ACCEPTED', 'REJECTED', 'UNDER_REVIEW', 'no Topic', 'foreign Topic', 'inactive Topic', 'wrong version', 'CORRECTION_REQUIRED', 'INELIGIBLE']);

test('only latest current-version exception is authoritative and PASS remains history rather than a mandatory gate', function () {
    $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id]]);
    foreach (['CORRECTION_REQUIRED', 'PASS'] as $outcome) {
        AcademicScreening::query()->create([
            'submission_id' => $this->paper->id, 'abstract_version' => 1, 'outcome' => $outcome, 'note' => $outcome, 'actor_id' => $this->committee->id,
        ]);
    }
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeTrue();
    topicFoundationContext($this->otherEdition);
    expect(fn () => $this->pool->query($this->reviewer, $this->edition))->toThrow(AuthorizationException::class);
});

test('active completed and declared COI assignments make the paper unavailable', function (string $status) {
    $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id]]);
    ReviewAssignment::query()->create([
        'submission_id' => $this->paper->id, 'snapshot_id' => $this->snapshot->id,
        'reviewer_id' => $status === 'COI' ? $this->reviewer->id : User::factory()->create()->id,
        'assigned_by' => $this->committee->id, 'status' => $status, 'screening_note' => 'Existing assignment',
    ]);
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeFalse();
})->with(['INVITED', 'ACCEPTED', 'COMPLETED', 'COI']);

test('authorized legacy routing correction changes only effective Topic and keeps immutable scholarly evidence', function () {
    $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->secondTrack->id]]);
    $this->paper->update(['track_id' => null]);
    expect($this->pool->allows($this->reviewer, $this->paper))->toBeFalse();
    $before = $this->paper->fresh()->getRawOriginal();
    $snapshotBefore = $this->snapshot->fresh()->getRawOriginal();
    $this->actingAs($this->committee)->get(route('academic.show', $this->paper))->assertOk()->assertInertia(fn ($page) => $page
        ->where('routing.currentTopic', null)->where('routing.snapshotTopic.code', 'T01')->where('routing.canCorrect', true));
    $this->post(route('academic.topic.correct', $this->paper), ['track_id' => $this->secondTrack->id, 'reason' => 'Human review of the submitted abstract'])->assertSessionHasNoErrors();
    $after = $this->paper->fresh()->getRawOriginal();
    unset($before['track_id'], $before['updated_at'], $after['track_id'], $after['updated_at']);
    expect($after)->toBe($before)->and($this->snapshot->fresh()->getRawOriginal())->toBe($snapshotBefore)
        ->and($this->paper->fresh()->track_id)->toBe($this->secondTrack->id);
    topicFoundationContext($this->edition);
    expect($this->pool->allows($this->reviewer, $this->paper->fresh()))->toBeTrue();
    $audit = DB::table('activity_log')->where('event', 'submitted_topic_routing_corrected')->sole();
    $properties = json_decode($audit->properties, true);
    expect($properties['old_track_id'])->toBeNull()->and($properties['new_track_id'])->toBe($this->secondTrack->id)
        ->and($audit->causer_id)->toBe($this->committee->id)->and($properties['reason'])->toBe('Human review of the submitted abstract');
});

test('reactivation binds the original eligibility identity even if an email now belongs to another verified account', function () {
    $this->manage->grant($this->committee, $this->edition, ['reviewer_email' => $this->reviewer->email, 'track_ids' => [$this->track->id]]);
    $record = ReviewerTopicEligibility::query()->sole();
    $this->manage->deactivate($this->committee, $record);
    $email = $this->reviewer->email;
    $this->reviewer->update(['email' => 'changed-reviewer@iches.test']);
    User::factory()->create(['email' => $email]);
    expect(fn () => $this->manage->grant($this->committee, $this->edition, [
        'reviewer_email' => $email, 'track_ids' => [$this->track->id], 'eligibility_id' => $record->id,
    ]))->toThrow(ValidationException::class);
    expect(ReviewerTopicEligibility::query()->count())->toBe(1)->and($record->fresh()->active)->toBeFalse();
});

test('routing correction rejects foreign unauthorized inactive reasonless non-submitted and assigned changes', function (string $case) {
    $actor = $this->committee;
    $trackId = $this->secondTrack->id;
    if ($case === 'unauthorized') {
        $actor = $this->reviewer;
    }
    if ($case === 'foreign context') {
        $this->withSession([ActiveConferenceEditionContext::SESSION_KEY => $this->otherEdition->id]);
    }
    if ($case === 'foreign Topic') {
        $trackId = $this->foreignTrack->id;
    }
    if ($case === 'inactive') {
        $this->secondTrack->update(['active' => false]);
    }
    if ($case === 'non submitted') {
        $this->paper->update(['academic_status' => 'ACCEPTED']);
    }
    if ($case === 'assigned') {
        ReviewAssignment::query()->create([
            'submission_id' => $this->paper->id, 'snapshot_id' => $this->snapshot->id, 'reviewer_id' => $this->reviewer->id,
            'assigned_by' => $this->committee->id, 'status' => 'INVITED', 'screening_note' => 'Active',
        ]);
    }
    $before = $this->paper->fresh()->getRawOriginal();
    $response = $this->actingAs($actor)->post(route('academic.topic.correct', $this->paper), ['track_id' => $trackId, 'reason' => $case === 'reasonless' ? '' : 'Review']);
    if (in_array($case, ['unauthorized', 'foreign context'], true)) {
        $response->assertForbidden();
    } else {
        $response->assertSessionHasErrors();
    }
    expect($this->paper->fresh()->getRawOriginal())->toBe($before);
})->with(['unauthorized', 'foreign context', 'foreign Topic', 'inactive', 'reasonless', 'non submitted', 'assigned']);
