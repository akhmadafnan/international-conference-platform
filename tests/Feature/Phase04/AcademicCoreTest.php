<?php

use App\Actions\Academic\AcademicWorkflow;
use App\Models\AcademicDecision;
use App\Models\AcademicScreening;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\GeneratedDocument;
use App\Models\NumberSequence;
use App\Models\ParticipationPackage;
use App\Models\Permission;
use App\Models\Registration;
use App\Models\ReviewAssignment;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use App\Support\Submission\InitialSubmissionSnapshotBuilder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

function p04Grant(User $actor, ConferenceEdition $edition, string $permission): void
{
    setPermissionsTeamId($edition->id);
    $actor->unsetRelation('roles')->unsetRelation('permissions');
    $actor->givePermissionTo(Permission::findOrCreate($permission, 'web'));
    setPermissionsTeamId(null);
    $actor->unsetRelation('roles')->unsetRelation('permissions');
}

function p04Context(ConferenceEdition $edition): void
{
    app(ActiveConferenceEditionContext::class)->activate($edition->id);
    setPermissionsTeamId($edition->id);
}

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('private');
    $series = ConferenceSeries::query()->create(['code' => 'ACA', 'name' => 'Academic Test', 'status' => 'ACTIVE']);
    $this->edition = ConferenceEdition::query()->create(['series_id' => $series->id, 'edition_code' => 'ACA-2027', 'edition_number' => 1, 'year' => 2027, 'host_name' => 'Test Host', 'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta', 'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonth()->addDay(), 'lifecycle_status' => 'DRAFT', 'settings_json' => ['abstract_submission' => ['min_keywords' => 3, 'max_keywords' => 5, 'track_required' => false, 'abstract_file_required' => false]]]);
    $this->author = User::factory()->create();
    $this->screener = User::factory()->create();
    $this->committee = User::factory()->create();
    $this->decider = User::factory()->create();
    $this->reviewer = User::factory()->create();
    p04Grant($this->screener, $this->edition, 'submission.admin_screen');
    p04Grant($this->committee, $this->edition, 'review.assign');
    p04Grant($this->decider, $this->edition, 'academic.decide');
    $membership = EditionMembership::query()->create(['edition_id' => $this->edition->id, 'user_id' => $this->author->id, 'membership_status' => 'ACTIVE', 'joined_at' => now()]);
    $package = ParticipationPackage::query()->create(['edition_id' => $this->edition->id, 'code' => 'PRESENTER', 'name_i18n' => ['en' => 'Presenter'], 'billing_mode' => 'PAID', 'price' => '750000.00', 'currency_code' => 'IDR', 'active' => true, 'display_order' => 1]);
    $this->registration = Registration::query()->create(['membership_id' => $membership->id, 'registration_code' => 'ACA-REG', 'package_id' => $package->id, 'package_name_snapshot' => 'Presenter', 'billing_mode' => 'PAID', 'fee_amount' => '750000.00', 'currency_code' => 'IDR', 'status' => 'PENDING']);
    $this->submission = Submission::query()->create(['edition_id' => $this->edition->id, 'registration_id' => $this->registration->id, 'paper_code' => 'ACA-P-0001', 'primary_locale' => 'id', 'academic_status' => 'SUBMITTED', 'current_abstract_version' => 1, 'submitted_at' => now()]);
    $this->submission->translations()->create(['locale' => 'id', 'title' => 'Judul Penelitian', 'abstract_text' => 'Abstrak penelitian ilmiah.']);
    foreach (['education', 'humanity', 'society'] as $i => $word) {
        $this->submission->keywords()->create(['locale' => 'id', 'sequence' => $i + 1, 'value' => $word]);
    }
    $author = $this->submission->contributors()->create(['linked_user_id' => $this->author->id, 'sequence' => 1, 'display_name' => 'Test Author', 'given_name' => 'Test', 'family_name' => 'Author', 'single_name' => false, 'email' => $this->author->email, 'country_code' => 'ID', 'is_corresponding' => true, 'contributor_role' => 'AUTHOR']);
    $author->affiliations()->create(['sequence' => 1, 'institution_name_snapshot' => 'Test University', 'country_code' => 'ID']);
    $builder = app(InitialSubmissionSnapshotBuilder::class);
    $metadata = $builder->build($this->submission);
    $this->snapshot = $this->submission->snapshots()->create(['snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => $metadata, 'checksum' => $builder->checksum($metadata), 'created_by_user_id' => $this->author->id]);
    $this->workflow = app(AcademicWorkflow::class);
    p04Context($this->edition);
    $this->withSession([ActiveConferenceEditionContext::SESSION_KEY => $this->edition->id]);
});

afterEach(function () {
    setPermissionsTeamId(null);
    app(ActiveConferenceEditionContext::class)->clear();
});

function p04Screen(object $test, string $outcome = 'PASS'): void
{
    p04Context($test->edition);
    $test->workflow->screen($test->screener, $test->submission->fresh(), ['version' => $test->submission->fresh()->current_abstract_version, 'outcome' => $outcome, 'note' => 'Screening note to author']);
}

function p04Assign(object $test, ?User $reviewer = null): ReviewAssignment
{
    p04Context($test->edition);

    return $test->workflow->assign($test->committee, $test->submission->fresh(), ['version' => $test->submission->fresh()->current_abstract_version, 'reviewer_email' => ($reviewer ?? $test->reviewer)->email, 'coi_screened' => true, 'screening_note' => 'COI checked against known relationships']);
}

function p04Complete(object $test, string $recommendation = 'ACCEPT'): ReviewAssignment
{
    p04Screen($test);
    $assignment = p04Assign($test);
    $test->workflow->respond($test->reviewer, $assignment, 'ACCEPTED', 'No conflict');
    $test->workflow->review($test->reviewer, $assignment, ['final' => true, 'recommendation' => $recommendation, 'author_comments' => 'Public reviewer feedback', 'confidential_comments' => 'SECRET EDITOR COMMENT']);

    return $assignment;
}

function p04Decision(object $test, string $outcome): array
{
    return ['version' => $test->submission->fresh()->current_abstract_version, 'outcome' => $outcome, 'author_message' => 'Decision message to author', 'rationale' => 'SECRET AUTHORITY RATIONALE: independent assessment of evidence'];
}

test('academic routes require authenticated verified users and explicit screening authority', function () {
    $this->get('/academic')->assertRedirect('/login');
    $this->actingAs(User::factory()->unverified()->create())->get('/academic')->assertRedirect(route('verification.notice'));
    $ordinary = User::factory()->create();
    $this->actingAs($ordinary)->get('/academic')->assertForbidden();
    $this->actingAs($this->committee)->post(route('academic.screen', $this->submission), ['version' => 1, 'outcome' => 'PASS', 'note' => 'Not authorized'])->assertForbidden();
    $this->assertDatabaseCount('academic_screenings', 0);
});

test('screening has distinct outcomes and audited idempotent evidence', function (string $outcome) {
    $this->actingAs($this->screener)->post(route('academic.screen', $this->submission), ['version' => 1, 'outcome' => $outcome, 'note' => 'Metadata checked'])->assertSessionHasNoErrors();
    $this->post(route('academic.screen', $this->submission), ['version' => 1, 'outcome' => $outcome, 'note' => 'Metadata checked'])->assertSessionHasNoErrors();
    $this->assertDatabaseCount('academic_screenings', 1);
    expect(AcademicScreening::query()->sole()->outcome)->toBe($outcome)
        ->and(Activity::query()->where('event', 'submission_administratively_screened')->count())->toBe(1)
        ->and($this->submission->fresh()->academic_status)->toBe('SUBMITTED');
})->with(['PASS', 'CORRECTION_REQUIRED', 'INELIGIBLE']);

test('cross Edition permissions cannot browse screen assign or decide the target', function () {
    $other = $this->edition->replicate();
    $other->edition_code = 'OTHER-2027';
    $other->save();
    $outsider = User::factory()->create();
    foreach (['submission.admin_screen', 'review.assign', 'academic.decide'] as $permission) {
        p04Grant($outsider, $other, $permission);
    }
    $this->actingAs($outsider)->withSession([ActiveConferenceEditionContext::SESSION_KEY => $other->id]);
    $this->get(route('academic.show', $this->submission))->assertForbidden();
    $this->post(route('academic.screen', $this->submission), ['version' => 1, 'outcome' => 'PASS', 'note' => 'Wrong Edition'])->assertForbidden();
    $this->post(route('academic.assign', $this->submission), ['version' => 1, 'reviewer_email' => $this->reviewer->email, 'coi_screened' => true, 'screening_note' => 'Wrong Edition'])->assertForbidden();
    $this->post(route('academic.decide', $this->submission), p04Decision($this, 'ACCEPTED'))->assertForbidden();
    $this->post(route('academic.edition'), ['edition_id' => $this->edition->id])->assertForbidden();
});

test('academic selection lists only authorized Editions and uses real pagination', function () {
    $this->actingAs($this->committee)->post('/academic/edition', ['edition_id' => $this->edition->id])->assertRedirect('/academic');
    $this->get('/academic')->assertOk()->assertInertia(fn (Assert $page) => $page->component('academic/Index')->has('editions', 1)->has('submissions', 1)->where('pagination.total', 1)->where('pagination.perPage', 20));
    $this->get(route('academic.show', $this->submission))->assertOk()->assertInertia(fn (Assert $page) => $page->where('abilities.screen', false)->where('abilities.assign', true)->where('abilities.decide', false));
});

test('assignment requires PASS screening rejects self review and records a version-specific resource', function () {
    expect(fn () => p04Assign($this))->toThrow(ValidationException::class);
    p04Screen($this);
    expect(fn () => p04Assign($this, $this->author))->toThrow(AuthorizationException::class);
    $assignment = p04Assign($this);
    expect($assignment->snapshot_id)->toBe($this->snapshot->id)->and($assignment->anonymity)->toBe('SINGLE_ANONYMOUS');
    expect(p04Assign($this)->id)->toBe($assignment->id);
    $this->assertDatabaseCount('review_assignments', 1);
    expect(Activity::query()->where('event', 'reviewer_assigned')->count())->toBe(1);
});

test('same affiliation is a warning requiring a reasoned Committee acknowledgement', function () {
    $otherOwner = User::factory()->create();
    $otherMembership = EditionMembership::query()->create(['edition_id' => $this->edition->id, 'user_id' => $otherOwner->id, 'membership_status' => 'ACTIVE', 'joined_at' => now()]);
    $otherRegistration = $this->registration->replicate();
    $otherRegistration->membership_id = $otherMembership->id;
    $otherRegistration->registration_code = 'OTHER-REG';
    $otherRegistration->save();
    $other = $this->submission->replicate();
    $other->registration_id = $otherRegistration->id;
    $other->paper_code = 'OTHER-PAPER';
    $other->save();
    $linked = $other->contributors()->create(['linked_user_id' => $this->reviewer->id, 'sequence' => 1, 'display_name' => 'Reviewer Scholar', 'given_name' => 'Reviewer', 'family_name' => 'Scholar', 'email' => $this->reviewer->email, 'country_code' => 'ID', 'single_name' => false, 'is_corresponding' => true, 'contributor_role' => 'AUTHOR']);
    $linked->affiliations()->create(['sequence' => 1, 'institution_name_snapshot' => 'TEST UNIVERSITY', 'country_code' => 'ID']);
    p04Screen($this);
    expect(fn () => p04Assign($this))->toThrow(ValidationException::class);
    $assignment = $this->workflow->assign($this->committee, $this->submission, ['version' => 1, 'reviewer_email' => $this->reviewer->email, 'coi_screened' => true, 'same_affiliation_acknowledged' => true, 'screening_note' => 'Shared institution; no direct relationship after human check']);
    expect($assignment->same_affiliation)->toBeTrue();
});

test('reviewer can accept decline declare COI and cannot browse another assignment', function (string $response) {
    p04Screen($this);
    $a = p04Assign($this);
    $this->actingAs($this->author)->get(route('academic.review', $a))->assertNotFound();
    $this->actingAs($this->reviewer)->get('/reviews')->assertOk()->assertInertia(fn (Assert $page) => $page->has('assignments', 1));
    $this->post(route('academic.respond', $a), ['response' => $response, 'note' => 'Reason for response'])->assertSessionHasNoErrors();
    expect($a->fresh()->status)->toBe($response);
    if ($response === 'COI') {
        $this->get(route('academic.review', $a))->assertForbidden();
    } elseif ($response !== 'ACCEPTED') {
        $this->get(route('academic.review', $a))->assertSessionHasErrors('academic');
    }
    $this->post(route('academic.decide', $this->submission), p04Decision($this, 'ACCEPTED'))->assertForbidden();
})->with(['ACCEPTED', 'DECLINED', 'COI']);

test('review draft is private final review is locked and completion is auditable', function () {
    p04Screen($this);
    $a = p04Assign($this);
    $this->workflow->respond($this->reviewer, $a, 'ACCEPTED', '');
    $data = ['final' => false, 'recommendation' => 'REVISION', 'author_comments' => 'Public reviewer feedback', 'confidential_comments' => 'SECRET EDITOR COMMENT'];
    $this->actingAs($this->reviewer)->put(route('academic.review.save', $a), $data)->assertSessionHasNoErrors();
    $this->actingAs($this->committee)->get(route('academic.show', $this->submission))->assertOk()->assertInertia(fn (Assert $page) => $page->where('assignments.0.review', null));
    $data['final'] = true;
    $this->actingAs($this->reviewer)->put(route('academic.review.save', $a), $data)->assertSessionHasNoErrors();
    $this->put(route('academic.review.save', $a), $data)->assertSessionHasNoErrors();
    $data['author_comments'] = 'Tampering';
    $this->put(route('academic.review.save', $a), $data)->assertSessionHasErrors('academic');
    expect($a->fresh()->status)->toBe('COMPLETED')->and(Activity::query()->where('event', 'academic_review_submitted')->count())->toBe(1);
    $this->actingAs($this->author)->get(route('academic.author', $this->submission))->assertOk()->assertDontSee('SECRET EDITOR COMMENT')->assertDontSee($this->reviewer->email)->assertInertia(fn (Assert $page) => $page->has('reports', 0)->missing('assignments'));
});

test('cancelled assignments retain history and revoke review and file access', function () {
    p04Screen($this);
    $a = p04Assign($this);
    $this->workflow->cancel($this->committee, $a, 'Reviewer unavailable');
    $this->actingAs($this->reviewer)->get(route('academic.review', $a))->assertSessionHasErrors('academic');
    $this->get(route('academic.review.file', $a))->assertSessionHasErrors('academic');
    expect($a->fresh()->status)->toBe('CANCELLED')->and(Activity::query()->where('event', 'review_assignment_cancelled')->count())->toBe(1);
});

test('screening authority alone cannot read confidential reports or reviewer identities', function () {
    p04Complete($this);
    $this->actingAs($this->screener)->get(route('academic.show', $this->submission))->assertOk()
        ->assertDontSee('SECRET EDITOR COMMENT')->assertDontSee($this->reviewer->email)
        ->assertInertia(fn (Assert $page) => $page->has('assignments', 0)->has('decisions', 0));
});

test('an Author with academic permissions cannot reveal their own reviewer or confidential reports', function () {
    p04Complete($this);
    p04Grant($this->author, $this->edition, 'review.assign');
    $this->actingAs($this->author)->get(route('academic.show', $this->submission))->assertForbidden();
    $this->get(route('academic.file', $this->submission))->assertForbidden();
});

test('declared COI overrides separately held Committee and decision permissions', function () {
    p04Screen($this);
    $a = p04Assign($this);
    $this->workflow->respond($this->reviewer, $a, 'COI', 'Known professional conflict');
    p04Grant($this->reviewer, $this->edition, 'academic.decide');
    p04Grant($this->reviewer, $this->edition, 'review.assign');
    $this->actingAs($this->reviewer)->get(route('academic.show', $this->submission))->assertForbidden();
    $this->post(route('academic.decide', $this->submission), p04Decision($this, 'ACCEPTED'))->assertForbidden();
    $this->assertDatabaseCount('academic_decisions', 0);
});

test('a recommendation never decides final acceptance and confidential content never reaches Author or LoA', function () {
    p04Complete($this, 'REJECT');
    expect($this->submission->fresh()->academic_status)->toBe('UNDER_REVIEW');
    $this->assertDatabaseCount('generated_documents', 0);
    $this->actingAs($this->decider)->post(route('academic.decide', $this->submission), p04Decision($this, 'ACCEPTED'))->assertSessionHasNoErrors();
    $document = GeneratedDocument::query()->where('document_type', 'PRESENTATION_LOA')->sole();
    expect($this->submission->fresh()->academic_status)->toBe('ACCEPTED')
        ->and($document->snapshot_json['decision_id'])->toBe(AcademicDecision::query()->sole()->id)
        ->and($document->snapshot_json['snapshot_checksum'])->toBe($this->snapshot->checksum);
    $this->actingAs($this->author)->get(route('academic.author', $this->submission))->assertOk()->assertDontSee('SECRET EDITOR COMMENT')->assertDontSee('SECRET AUTHORITY RATIONALE')->assertDontSee($this->reviewer->email)->assertInertia(fn (Assert $page) => $page->where('reports.0.comments', 'Public reviewer feedback')->where('loaUrl', route('academic.loa', $document)));
    $this->get(route('academic.loa', $document))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    $bytes = Storage::disk('private')->get($document->storedFile->path);
    expect(str_starts_with($bytes, '%PDF-'))->toBeTrue()->and(hash('sha256', $bytes))->toBe($document->storedFile->checksum_sha256);
    $this->actingAs($this->reviewer)->get(route('academic.loa', $document))->assertNotFound();
    $this->assertDatabaseCount('payments', 0);
    expect($this->registration->fresh()->status->value)->toBe('PENDING');
});

test('LoA is never issued for revision or rejection', function (string $outcome) {
    p04Complete($this);
    $this->workflow->decide($this->decider, $this->submission, p04Decision($this, $outcome));
    $this->assertDatabaseCount('generated_documents', 0);
    $this->assertDatabaseCount('payments', 0);
})->with(['REVISION_REQUIRED', 'REJECTED']);

test('duplicate acceptance is idempotent and a different second decision cannot replace history', function () {
    p04Complete($this);
    $data = p04Decision($this, 'ACCEPTED');
    $first = $this->workflow->decide($this->decider, $this->submission, $data);
    expect($this->workflow->decide($this->decider, $this->submission, $data)->id)->toBe($first->id);
    expect(fn () => $this->workflow->decide($this->decider, $this->submission, p04Decision($this, 'REJECTED')))->toThrow(ValidationException::class);
    $this->assertDatabaseCount('academic_decisions', 1);
    $this->assertDatabaseCount('generated_documents', 1);
    expect(NumberSequence::query()->where('sequence_type', 'PRESENTATION_LOA')->sole()->last_value)->toBe(1);
});

test('reviewer count is configurable and real completed reports are required', function () {
    p04Complete($this);
    $settings = $this->edition->settings_json;
    $settings['abstract_review'] = ['reviewer_count' => 2];
    $this->edition->update(['settings_json' => $settings]);
    expect(fn () => $this->workflow->decide($this->decider, $this->submission->fresh(), p04Decision($this, 'ACCEPTED')))->toThrow(ValidationException::class);
    $this->assertDatabaseCount('academic_decisions', 0);
});

test('one ordinary academic revision creates immutable version history and requires a new review assignment', function () {
    $old = p04Complete($this);
    $this->workflow->decide($this->decider, $this->submission, p04Decision($this, 'REVISION_REQUIRED'));
    $this->actingAs($this->author)->post(route('academic.revise', $this->submission), ['version' => 1, 'title' => 'Revised title', 'abstract' => 'Revised abstract.', 'keywords' => ['education', 'humanity', 'society'], 'response' => 'Addressed the academic comments'])->assertSessionHasNoErrors();
    expect($this->submission->fresh()->current_abstract_version)->toBe(2)
        ->and($this->snapshot->fresh()->checksum)->toBe($this->snapshot->checksum)
        ->and($this->snapshot->fresh()->metadata_json['translations'][0]['title'])->toBe('Judul Penelitian');
    $this->actingAs($this->reviewer)->get(route('academic.review', $old))->assertOk()->assertInertia(fn (Assert $page) => $page->where('packet.title', 'Judul Penelitian')->where('assignment.version', 1));
    p04Complete($this);
    expect(fn () => $this->workflow->decide($this->decider, $this->submission->fresh(), p04Decision($this, 'REVISION_REQUIRED')))->toThrow(ValidationException::class);
    $this->workflow->decide($this->decider, $this->submission->fresh(), p04Decision($this, 'REJECTED'));
    expect(AcademicDecision::query()->count())->toBe(2);
});

test('administrative correction does not consume the ordinary academic revision cycle', function () {
    p04Screen($this, 'CORRECTION_REQUIRED');
    $this->workflow->revise($this->author, $this->submission, ['version' => 1, 'title' => 'Corrected title', 'abstract' => 'Corrected abstract.', 'keywords' => ['one', 'two', 'three'], 'response' => 'Administrative correction only']);
    expect($this->submission->snapshots()->where('snapshot_type', 'ADMIN_CORRECTION')->count())->toBe(1)
        ->and(AcademicDecision::query()->count())->toBe(0);
    p04Complete($this);
    $this->workflow->decide($this->decider, $this->submission->fresh(), p04Decision($this, 'REVISION_REQUIRED'));
    expect($this->submission->fresh()->academic_status)->toBe('REVISION_REQUIRED');
});

test('revision eligibility and keyword rules are revalidated on the server without trusting hidden fields', function () {
    p04Screen($this, 'CORRECTION_REQUIRED');
    $data = ['version' => 1, 'title' => 'Corrected title', 'abstract' => 'Corrected abstract.', 'keywords' => ['only', 'two'], 'response' => 'Correction', 'academic_status' => 'ACCEPTED', 'current_abstract_version' => 999];
    $this->actingAs($this->author)->post(route('academic.revise', $this->submission), $data)->assertSessionHasErrors('academic');
    expect($this->submission->fresh()->current_abstract_version)->toBe(1);
    $this->actingAs($this->reviewer)->post(route('academic.revise', $this->submission), $data)->assertNotFound();
    $data['keywords'][] = 'three';
    $this->actingAs($this->author)->post(route('academic.revise', $this->submission), $data)->assertSessionHasNoErrors();
    expect($this->submission->fresh()->academic_status)->toBe('SUBMITTED')->and($this->submission->fresh()->current_abstract_version)->toBe(2);
    $this->assertDatabaseCount('academic_decisions', 0);
});

test('LoA uses configured numbering and can render Arabic acceptance without changing finance state', function () {
    $this->submission->update(['primary_locale' => 'ar']);
    $this->submission->translations()->create(['locale' => 'ar', 'title' => 'بحث في التعليم والمجتمع', 'abstract_text' => 'ملخص علمي.']);
    $data = $this->snapshot->metadata_json;
    $data['primary_locale'] = 'ar';
    $data['translations'][] = ['locale' => 'ar', 'title' => 'بحث في التعليم والمجتمع', 'abstract' => 'ملخص علمي.'];
    $this->snapshot->update(['metadata_json' => $data, 'checksum' => app(InitialSubmissionSnapshotBuilder::class)->checksum($data)]);
    NumberSequence::query()->create(['edition_id' => $this->edition->id, 'sequence_type' => 'PRESENTATION_LOA', 'prefix' => 'CUSTOM/LOA/', 'last_value' => 9, 'configuration_json' => ['padding' => 6]]);
    p04Complete($this);
    $this->workflow->decide($this->decider, $this->submission, p04Decision($this, 'ACCEPTED'));
    $document = GeneratedDocument::query()->sole();
    expect($document->document_number)->toBe('CUSTOM/LOA/000010')
        ->and($document->snapshot_json['locale'])->toBe('ar')
        ->and($document->snapshot_json['title'])->toBe('بحث في التعليم والمجتمع');
    $this->actingAs($this->author)->get(route('academic.loa', $document))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    $this->assertDatabaseCount('payments', 0);
});

test('stale versions and true author conflicts cannot mutate academic history', function () {
    p04Grant($this->author, $this->edition, 'academic.decide');
    p04Complete($this);
    expect(fn () => $this->workflow->decide($this->author, $this->submission, p04Decision($this, 'ACCEPTED')))->toThrow(AuthorizationException::class);
    $data = p04Decision($this, 'ACCEPTED');
    $data['version'] = 99;
    expect(fn () => $this->workflow->decide($this->decider, $this->submission, $data))->toThrow(ValidationException::class);
    $this->assertDatabaseCount('academic_decisions', 0);
});

test('audit failure rolls back acceptance document sequence file and status atomically', function () {
    p04Complete($this);
    Activity::creating(function (Activity $activity) {
        if ($activity->event === 'presentation_loa_issued') {
            throw new RuntimeException('Audit unavailable');
        }
    });
    try {
        expect(fn () => $this->workflow->decide($this->decider, $this->submission, p04Decision($this, 'ACCEPTED')))->toThrow(RuntimeException::class, 'Audit unavailable');
    } finally {
        Activity::flushEventListeners();
    }
    expect($this->submission->fresh()->academic_status)->toBe('UNDER_REVIEW');
    $this->assertDatabaseCount('academic_decisions', 0);
    $this->assertDatabaseCount('generated_documents', 0);
    $this->assertDatabaseCount('stored_files', 0);
    $this->assertDatabaseCount('number_sequences', 0);
    expect(Storage::disk('private')->allFiles())->toBe([]);
});

test('assigned file downloads are private checksum verified and bound to the snapshot', function () {
    $bytes = '%PDF-1.4 fixture';
    Storage::disk('private')->put('abstract.pdf', $bytes);
    $file = StoredFile::query()->create(['disk' => 'private', 'path' => 'abstract.pdf', 'original_name' => 'original.pdf', 'mime_type' => 'application/pdf', 'size_bytes' => strlen($bytes), 'checksum_sha256' => hash('sha256', $bytes), 'visibility_class' => 'PRIVATE', 'uploaded_by_user_id' => $this->author->id]);
    $metadata = $this->snapshot->metadata_json;
    $metadata['abstract_file'] = ['stored_file_id' => $file->id];
    $this->snapshot->update(['metadata_json' => $metadata, 'checksum' => app(InitialSubmissionSnapshotBuilder::class)->checksum($metadata)]);
    p04Screen($this);
    $a = p04Assign($this);
    $this->actingAs($this->reviewer)->get(route('academic.review.file', $a))->assertOk()->assertHeader('Cache-Control', 'no-store, private');
    $this->actingAs($this->author)->get(route('academic.review.file', $a))->assertNotFound();
    Storage::disk('private')->put('abstract.pdf', 'modified');
    $this->actingAs($this->reviewer)->get(route('academic.review.file', $a))->assertStatus(409);
});

test('two independent SQLite workers accept concurrently but issue exactly one decision and LoA', function () {
    p04Complete($this);
    $token = 'academic-race-'.Str::random(12);
    $directory = storage_path('framework/testing');
    if (! is_dir($directory)) {
        mkdir($directory, 0700, true);
    }
    $database = $directory.'/'.$token.'.sqlite';
    $barrier = $directory.'/'.$token.'.go';
    $fileRoot = $directory.'/'.$token.'-files';
    $pdo = new PDO('sqlite:'.$database);
    // Copy only isolated in-memory fixture data; do not connect to MySQL.
    expect(DB::connection()->getDriverName())->toBe('sqlite');
    $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'");
    foreach ($tables as $table) {
        if (! preg_match('/^[a-z_]+$/', $table->name)) {
            throw new RuntimeException('Unexpected fixture table name');
        }
        $pdo->exec($table->sql);
        foreach (DB::table($table->name)->get() as $row) {
            $values = (array) $row;
            $columns = implode(', ', array_map(fn ($key) => '"'.$key.'"', array_keys($values)));
            $insert = $pdo->prepare('INSERT INTO "'.$table->name.'" ('.$columns.') VALUES ('.implode(', ', array_fill(0, count($values), '?')).')');
            $insert->execute(array_values($values));
        }
    }
    $insert = null;
    $workers = [];
    try {
        $env = array_merge(getenv(), ['APP_ENV' => 'testing', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database, 'DB_URL' => '', 'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'sync']);
        for ($i = 0; $i < 2; $i++) {
            $process = proc_open([PHP_BINARY, base_path('tests/Support/academic-decision-worker.php'), $this->decider->id, $this->submission->id, $barrier, $fileRoot], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, base_path(), $env);
            if (! is_resource($process)) {
                throw new RuntimeException('Cannot start isolated worker');
            }
            fclose($pipes[0]);
            $workers[] = [$process, $pipes];
        }
        foreach ($workers as [, $pipes]) {
            expect(trim(fgets($pipes[1])))->toBe('READY');
        }
        touch($barrier);
        $ids = [];
        foreach ($workers as [$process, $pipes]) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            expect(proc_close($process))->toBe(0, $error);
            $ids[] = json_decode($output, true, flags: JSON_THROW_ON_ERROR)['decision'];
        }
        $workers = [];
        expect($ids[0])->toBe($ids[1]);
        foreach (['academic_decisions', 'generated_documents'] as $table) {
            expect((int) $pdo->query('SELECT COUNT(*) FROM '.$table)->fetchColumn())->toBe(1);
        }
        expect((int) $pdo->query("SELECT COUNT(*) FROM activity_log WHERE event = 'presentation_loa_issued'")->fetchColumn())->toBe(1);
        expect((int) $pdo->query("SELECT last_value FROM number_sequences WHERE sequence_type = 'PRESENTATION_LOA'")->fetchColumn())->toBe(1);
        expect((int) $pdo->query('SELECT COUNT(*) FROM payments')->fetchColumn())->toBe(0);
    } finally {
        touch($barrier);
        foreach ($workers as [$process, $pipes]) {
            if (is_resource($process)) {
                proc_terminate($process);
                proc_close($process);
            }
            foreach ($pipes as $pipe) {
                if (is_resource($pipe)) {
                    fclose($pipe);
                }
            }
        }
        foreach ($pdo->query('SELECT path FROM stored_files') as $file) {
            $path = $fileRoot.'/'.$file['path'];
            if (is_file($path) && str_starts_with((string) realpath($path), (string) realpath($fileRoot).DIRECTORY_SEPARATOR)) {
                unlink($path);
            }
        }
        foreach ([$fileRoot.'/academic/loa', $fileRoot.'/academic', $fileRoot] as $folder) {
            if (is_dir($folder)) {
                rmdir($folder);
            }
        }
        $pdo = null;
        // These exact paths are created above inside storage/framework/testing.
        foreach ([$database, $database.'-journal', $database.'-wal', $database.'-shm', $barrier] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
});
