<?php

use App\Actions\Submission\CreateDraftSubmissionAction;
use App\Actions\Submission\DeleteDraftSubmissionAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\NumberSequence;
use App\Models\ParticipationPackage;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function singleAbstractContext(?User $user = null, ?ConferenceEdition $edition = null): array
{
    $user ??= User::factory()->create();
    $series = ConferenceSeries::query()->firstOrCreate(['code' => 'ONE'], ['name' => 'ONE', 'status' => 'ACTIVE']);
    $edition ??= ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => 'ONE-'.Str::random(8), 'year' => 2027,
        'host_name' => 'Host', 'mode' => 'OFFLINE', 'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonths(2), 'lifecycle_status' => 'DRAFT',
    ]);
    $package = ParticipationPackage::query()->firstOrCreate(['edition_id' => $edition->id, 'code' => 'AUTHOR'], [
        'name_i18n' => ['en' => 'Author'], 'billing_mode' => 'PAID', 'price' => '100.00', 'currency_code' => 'IDR', 'active' => true,
    ]);
    $membership = EditionMembership::query()->create([
        'edition_id' => $edition->id, 'user_id' => $user->id, 'membership_status' => 'ACTIVE', 'joined_at' => now(),
    ]);
    $registration = Registration::query()->create([
        'membership_id' => $membership->id, 'registration_code' => 'ONE-'.Str::random(12),
        'package_id' => $package->id, 'package_name_snapshot' => 'Author', 'billing_mode' => 'PAID',
        'fee_amount' => '100.00', 'currency_code' => 'IDR', 'status' => 'PENDING',
    ]);

    return compact('user', 'edition', 'registration', 'membership', 'package');
}

function singleAbstractMigration(): Migration
{
    return require database_path('migrations/2026_10_23_120000_add_unique_submission_registration.php');
}

beforeEach(function () {
    $this->withoutVite();
    $this->context = singleAbstractContext();
    ['user' => $user, 'edition' => $edition, 'registration' => $registration] = $this->context;
    $this->draft = app(CreateDraftSubmissionAction::class)->handle($user, $edition, $registration, 'en');
});

test('HTTP duplicate validation is semantic and eligibility updates per owned registration', function (string $status) {
    ['user' => $user, 'registration' => $registration] = $this->context;
    $this->draft->update(['academic_status' => $status]);
    $this->actingAs($user)->post(route('submissions.store'), [
        'registration_id' => $registration->id, 'primary_locale' => 'en',
    ])->assertSessionHasErrors(['registration_id' => 'SUBMISSION_ALREADY_EXISTS']);
    $this->get(route('submissions.index'))->assertOk()->assertInertia(fn ($page) => $page
        ->where('registrations.0.canCreateDraft', false)
        ->where('submissions.0.destroyUrl', $status === 'DRAFT' ? route('submissions.destroy', $this->draft) : null));
    expect(Submission::query()->count())->toBe(1)->and(NumberSequence::query()->sole()->last_value)->toBe(1);
})->with(['DRAFT', 'SUBMITTED', 'UNDER_REVIEW', 'ACCEPTED', 'REJECTED', 'REVISION_REQUIRED']);

test('same owner may create in another Edition and another owner may create in the same Edition', function () {
    $action = app(CreateDraftSubmissionAction::class);
    foreach ([singleAbstractContext($this->context['user']), singleAbstractContext(edition: $this->context['edition'])] as $context) {
        $action->handle($context['user'], $context['edition'], $context['registration'], 'ar');
    }
    expect(Submission::query()->count())->toBe(3);
});

test('co-authorship on another owner submission does not consume the submitting owner limit', function () {
    $coauthor = singleAbstractContext(edition: $this->context['edition']);
    $this->draft->contributors()->create([
        'linked_user_id' => $coauthor['user']->id,
        'display_name' => 'Co-author', 'email' => 'co@iches.test', 'country_code' => 'ID',
        'sequence' => 1, 'is_corresponding' => false,
    ]);
    app(CreateDraftSubmissionAction::class)->handle($coauthor['user'], $coauthor['edition'], $coauthor['registration'], 'en');
    expect(Submission::query()->count())->toBe(2);
});

test('database unique violation is translated to the semantic duplicate error and numbering rolls back', function () {
    $context = singleAbstractContext(edition: $this->context['edition']);
    Submission::creating(function (Submission $submission): void {
        // Simulate a conflicting registration at insertion, after the precheck.
        $submission->registration_id = $this->draft->registration_id;
    });
    try {
        expect(fn () => app(CreateDraftSubmissionAction::class)->handle($context['user'], $context['edition'], $context['registration'], 'en'))
            ->toThrow(DomainException::class, 'SUBMISSION_ALREADY_EXISTS');
        expect(Submission::query()->count())->toBe(1)->and(NumberSequence::query()->sole()->last_value)->toBe(1);
    } finally {
        Submission::flushEventListeners();
    }
});

test('foreign and anonymous callers cannot delete and stale caller state cannot reopen a submission', function () {
    $this->delete(route('submissions.destroy', $this->draft))->assertRedirect();
    $stranger = User::factory()->create();
    $this->actingAs($stranger)->delete(route('submissions.destroy', $this->draft))->assertNotFound();
    expect(fn () => app(DeleteDraftSubmissionAction::class)->handle($stranger, $this->draft))
        ->toThrow(DomainException::class, 'SUBMISSION_NOT_OWNED');
    $this->draft->newQuery()->whereKey($this->draft->id)->update(['academic_status' => 'SUBMITTED']);
    expect(fn () => app(DeleteDraftSubmissionAction::class)->handle($this->context['user'], $this->draft))
        ->toThrow(DomainException::class, 'SUBMISSION_NOT_DRAFT');
    expect(Submission::query()->count())->toBe(1);
});

test('every non-draft state rejects direct HTTP deletion and normal editing', function (string $status) {
    $this->draft->update(['academic_status' => $status]);
    $this->actingAs($this->context['user'])->delete(route('submissions.destroy', $this->draft))
        ->assertSessionHasErrors(['delete' => 'DRAFT_DELETE_NOT_ALLOWED']);
    $this->put(route('submissions.details.update', $this->draft), [])->assertStatus(409);
    expect($this->draft->fresh()->academic_status)->toBe($status);
})->with(['SUBMITTED', 'UNDER_REVIEW', 'ACCEPTED', 'REJECTED', 'REVISION_REQUIRED', 'CORRECTION_REQUIRED', 'WITHDRAWN']);

test('academic evidence prevents deletion even if the status was reset to DRAFT', function (string $evidence) {
    if ($evidence === 'snapshot') {
        $this->draft->snapshots()->create([
            'snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => ['title' => 'Protected'],
            'checksum' => hash('sha256', 'protected'), 'created_by_user_id' => $this->context['user']->id,
        ]);
    } elseif ($evidence === 'document') {
        $this->draft->generatedDocuments()->create([
            'edition_id' => $this->draft->edition_id, 'document_type' => 'PRESENTATION_LOA', 'status' => 'ISSUED', 'snapshot_json' => [],
        ]);
    } elseif ($evidence === 'screening') {
        DB::table('academic_screenings')->insert([
            'id' => Str::uuid()->toString(), 'submission_id' => $this->draft->id,
            'abstract_version' => 1, 'outcome' => 'PASS', 'note' => 'Evidence',
            'actor_id' => $this->context['user']->id, 'created_at' => now(), 'updated_at' => now(),
        ]);
    } elseif ($evidence === 'decision') {
        DB::table('academic_decisions')->insert([
            'id' => Str::uuid()->toString(), 'submission_id' => $this->draft->id, 'abstract_version' => 1,
            'outcome' => 'ACCEPTED', 'author_message' => 'Accepted', 'rationale' => 'Evidence',
            'actor_id' => $this->context['user']->id, 'created_at' => now(), 'updated_at' => now(),
        ]);
    } else {
        $this->draft->update(['submitted_at' => now()]);
    }
    $before = [];
    foreach (['submissions', 'submission_snapshots', 'academic_screenings', 'academic_decisions'] as $table) {
        $before[$table] = DB::table($table)->get()->toJson();
    }
    expect(fn () => app(DeleteDraftSubmissionAction::class)->handle($this->context['user'], $this->draft))
        ->toThrow(DomainException::class, 'SUBMISSION_HAS_ACADEMIC_EVIDENCE');
    foreach ($before as $table => $rows) {
        expect(DB::table($table)->get()->toJson())->toBe($rows);
    }
})->with(['snapshot', 'screening', 'decision', 'document', 'submitted timestamp']);

test('draft deletion cascades draft metadata and private files after commit while preserving payment and numbering', function () {
    ['user' => $user, 'registration' => $registration, 'membership' => $membership, 'package' => $package] = $this->context;
    Storage::fake('private');
    $path = 'submission-files/abstracts/'.Str::uuid().'.pdf';
    Storage::disk('private')->put($path, 'Draft file');
    $stored = StoredFile::query()->create([
        'disk' => 'private', 'path' => $path, 'original_name' => 'draft.pdf', 'mime_type' => 'application/pdf',
        'size_bytes' => 10, 'checksum_sha256' => hash('sha256', 'Draft file'), 'visibility_class' => 'PRIVATE', 'uploaded_by_user_id' => $user->id,
    ]);
    $this->draft->files()->create([
        'stored_file_id' => $stored->id, 'file_role' => 'ABSTRACT_FILE', 'manuscript_version' => 1,
        'uploaded_by_user_id' => $user->id, 'submitted_at' => now(), 'status' => 'ACTIVE',
    ]);
    $this->actingAs($user)->put(route('submissions.details.update', $this->draft), [
        'title' => 'Draft title', 'abstract' => 'Draft abstract', 'keywords_text' => "one\ntwo\nthree",
    ])->assertSessionHasNoErrors();
    $contributor = $this->draft->contributors()->create([
        'display_name' => 'Author', 'email' => $user->email, 'country_code' => 'ID', 'sequence' => 1,
    ]);
    $contributor->affiliations()->create(['institution_name_snapshot' => 'Institution', 'sequence' => 1]);
    $this->draft->references()->create(['sequence' => 1, 'raw_citation' => 'Citation']);
    $payment = Payment::query()->create([
        'registration_id' => $registration->id, 'package_id_snapshot' => $package->id,
        'package_name_snapshot' => 'Author', 'expected_amount' => '100.00', 'currency_code' => 'IDR',
        'payment_destination_snapshot_json' => [], 'status' => 'PENDING',
    ]);
    $paymentBefore = $payment->fresh()->getRawOriginal();
    $registrationBefore = $registration->fresh()->getRawOriginal();
    $code = $this->draft->paper_code;

    DB::beginTransaction();
    app(DeleteDraftSubmissionAction::class)->handle($user, $this->draft);
    Storage::disk('private')->assertExists($path);
    DB::rollBack();
    expect($this->draft->fresh())->not->toBeNull();
    Storage::disk('private')->assertExists($path);

    $this->delete(route('submissions.destroy', $this->draft))->assertRedirect(route('submissions.index'));
    Storage::disk('private')->assertMissing($path);
    foreach (['submissions', 'submission_files', 'stored_files', 'submission_translations', 'submission_keywords', 'submission_contributors', 'contributor_affiliations', 'submission_references'] as $table) {
        $this->assertDatabaseCount($table, 0);
    }
    expect($payment->fresh()->getRawOriginal())->toBe($paymentBefore)
        ->and($registration->fresh()->getRawOriginal())->toBe($registrationBefore)
        ->and($membership->fresh())->not->toBeNull()
        ->and(NumberSequence::query()->sole()->last_value)->toBe(1);
    $this->assertDatabaseHas('activity_log', ['event' => 'submission_draft_deleted', 'subject_id' => $this->draft->id, 'causer_id' => $user->id]);
    $this->get(route('submissions.index'))->assertInertia(fn ($page) => $page->where('registrations.0.canCreateDraft', true)->has('submissions', 0));
    $this->post(route('submissions.store'), ['registration_id' => $registration->id, 'primary_locale' => 'en'])->assertSessionHasNoErrors();
    expect(Submission::query()->sole()->paper_code)->not->toBe($code)
        ->and(NumberSequence::query()->sole()->last_value)->toBe(2);
});

test('a stored file referenced by another resource is preserved', function () {
    Storage::fake('private');
    $path = 'submission-files/abstracts/'.Str::uuid().'.pdf';
    Storage::disk('private')->put($path, 'Shared file');
    $stored = StoredFile::query()->create([
        'disk' => 'private', 'path' => $path, 'original_name' => 'draft.pdf', 'mime_type' => 'application/pdf',
        'size_bytes' => 11, 'checksum_sha256' => hash('sha256', 'Shared file'), 'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $this->context['user']->id,
    ]);
    $this->draft->files()->create([
        'stored_file_id' => $stored->id, 'file_role' => 'ABSTRACT_FILE', 'uploaded_by_user_id' => $this->context['user']->id, 'submitted_at' => now(),
    ]);
    ConferenceSeries::query()->sole()->update(['logo_file_id' => $stored->id]);
    app(DeleteDraftSubmissionAction::class)->handle($this->context['user'], $this->draft);
    expect($stored->fresh())->not->toBeNull();
    Storage::disk('private')->assertExists($path);
});

test('migration refuses legacy duplicates without modifying them and can be applied after explicit draft deletion', function () {
    singleAbstractMigration()->down();
    $this->draft->update(['academic_status' => 'SUBMITTED', 'submitted_at' => now()]);
    $snapshot = $this->draft->snapshots()->create([
        'snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => ['title' => 'Official'],
        'checksum' => hash('sha256', 'official'), 'created_by_user_id' => $this->context['user']->id,
    ]);
    $legacy = $this->draft->replicate();
    $legacy->fill(['paper_code' => $this->draft->paper_code.'-LEGACY', 'academic_status' => 'DRAFT', 'submitted_at' => null]);
    $legacy->save();
    expect(fn () => singleAbstractMigration()->up())->toThrow(RuntimeException::class, 'duplicate registration_id');
    expect(Submission::query()->count())->toBe(2);
    $this->actingAs($this->context['user'])->delete(route('submissions.destroy', $legacy))->assertSessionHasNoErrors();
    singleAbstractMigration()->up();
    expect(Submission::query()->count())->toBe(1)->and($snapshot->fresh()->metadata_json)->toBe(['title' => 'Official'])
        ->and($this->draft->fresh()->academic_status)->toBe('SUBMITTED');
    expect(fn () => $this->draft->replicate()->fill(['paper_code' => 'DUPLICATE'])->save())
        ->toThrow(UniqueConstraintViolationException::class);
});

test('two independent SQLite workers create exactly one draft with or without the unique index', function (bool $unique) {
    app(DeleteDraftSubmissionAction::class)->handle($this->context['user'], $this->draft);
    if (! $unique) {
        singleAbstractMigration()->down();
    }
    expect(DB::connection()->getDriverName())->toBe('sqlite');
    $directory = storage_path('framework/testing');
    if (! is_dir($directory)) {
        mkdir($directory, 0700, true);
    }
    $database = $directory.'/submission-race-'.Str::random(12).'.sqlite';
    $barrier = $database.'.go';
    $pdo = new PDO('sqlite:'.$database);
    foreach (DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'") as $table) {
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
    foreach (DB::select("SELECT sql FROM sqlite_master WHERE type = 'index' AND sql IS NOT NULL") as $index) {
        $pdo->exec($index->sql);
    }
    $insert = null;
    $workers = [];
    try {
        $env = array_merge(getenv(), ['APP_ENV' => 'testing', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database, 'DB_URL' => '', 'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'sync']);
        for ($i = 0; $i < 2; $i++) {
            $process = proc_open([PHP_BINARY, base_path('tests/Support/submission-create-worker.php'), $this->context['user']->id, $this->context['registration']->id, $barrier], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, base_path(), $env);
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
        $outcomes = [];
        foreach ($workers as [$process, $pipes]) {
            $outcomes[] = trim(stream_get_contents($pipes[1]));
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            expect(proc_close($process))->toBe(0, $error);
        }
        $workers = [];
        sort($outcomes);
        expect($outcomes)->toBe(['CREATED', 'EXISTS'])
            ->and((int) $pdo->query('SELECT COUNT(*) FROM submissions')->fetchColumn())->toBe(1)
            ->and((int) $pdo->query("SELECT last_value FROM number_sequences WHERE sequence_type = 'PAPER'")->fetchColumn())->toBe(2);
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
        $pdo = null;
        foreach ([$database, $database.'-journal', $database.'-wal', $database.'-shm', $barrier] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
})->with([false, true]);
