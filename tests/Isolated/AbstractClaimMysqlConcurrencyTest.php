<?php

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\ReviewAssignment;
use App\Models\ReviewerTopicEligibility;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use App\Support\Submission\InitialSubmissionSnapshotBuilder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\Process;
use Tests\Support\ClaimMysqlConnectionGuard;
use Tests\TestCase;

uses(TestCase::class);

test('two real MySQL claim processes yield one owner and one rejected contender', function () {
    $settings = ClaimMysqlConnectionGuard::environment();
    if (! isset($settings['ENABLED'])) {
        Assert::markTestSkipped('Requires explicit MySQL verification opt-in and restricted test-only credentials.');
    }
    $guard = ClaimMysqlConnectionGuard::capture(app('db'), app('config'), base_path());
    $guard->connect($settings);
    $schema = $settings['DATABASE'];
    if (Schema::connection('mysql')->getTables() !== []) {
        throw new RuntimeException('Provision a new empty test schema; this test never resets existing tables.');
    }
    $guard->assertVerified();
    expect(Artisan::call('migrate', ['--database' => 'mysql', '--force' => true]))->toBe(0);
    $series = ConferenceSeries::query()->create(['code' => 'RACE', 'name' => 'Isolated race fixture', 'status' => 'ACTIVE']);
    $edition = ConferenceEdition::query()->create(['series_id' => $series->id, 'edition_code' => 'RACE-2027', 'year' => 2027, 'host_name' => 'Test', 'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta', 'lifecycle_status' => 'DRAFT', 'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonths(2)]);
    $track = $edition->tracks()->create(['code' => 'T01', 'name_i18n' => ['en' => 'Topic'], 'active' => true]);
    $author = User::factory()->create();
    $reviewers = [User::factory()->create(), User::factory()->create()];
    setPermissionsTeamId($edition->id);
    $role = Role::create(['name' => 'reviewer', 'guard_name' => 'web', 'conference_edition_id' => $edition->id]);
    foreach ($reviewers as $reviewer) {
        $reviewer->assignRole($role);
        ReviewerTopicEligibility::query()->create(['edition_id' => $edition->id, 'reviewer_id' => $reviewer->id, 'track_id' => $track->id, 'assigned_by' => $author->id, 'active' => true]);
    }
    $membership = EditionMembership::query()->create(['edition_id' => $edition->id, 'user_id' => $author->id, 'membership_status' => 'ACTIVE', 'joined_at' => now()]);
    $package = ParticipationPackage::query()->create(['edition_id' => $edition->id, 'code' => 'AUTHOR', 'name_i18n' => ['en' => 'Author'], 'billing_mode' => 'FREE', 'price' => 0, 'currency_code' => 'IDR', 'active' => true]);
    $registration = Registration::query()->create(['membership_id' => $membership->id, 'registration_code' => 'RACE-REG', 'package_id' => $package->id, 'package_name_snapshot' => 'Author', 'billing_mode' => 'FREE', 'fee_amount' => 0, 'currency_code' => 'IDR', 'status' => 'PENDING']);
    $paper = Submission::query()->create(['edition_id' => $edition->id, 'registration_id' => $registration->id, 'paper_code' => 'RACE-P-1', 'primary_locale' => 'en', 'academic_status' => 'SUBMITTED', 'current_abstract_version' => 1, 'track_id' => $track->id]);
    $paper->translations()->create(['locale' => 'en', 'title' => 'Race fixture', 'abstract_text' => 'Abstract']);
    $builder = app(InitialSubmissionSnapshotBuilder::class);
    $metadata = $builder->build($paper);
    $snapshot = $paper->snapshots()->create(['snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1, 'metadata_json' => $metadata, 'checksum' => $builder->checksum($metadata), 'created_by_user_id' => $author->id]);
    $barrier = tempnam(sys_get_temp_dir(), 'iches_claim_');
    $processes = [];
    try {
        foreach ($reviewers as $reviewer) {
            $workerEnvironment = ['APP_ENV' => 'testing'];
            foreach ($settings as $key => $value) {
                $workerEnvironment['ICHES_CLAIM_MYSQL_TEST_'.$key] = $value;
            }
            $process = new Process([PHP_BINARY, base_path('tests/Support/abstract-claim-worker.php'), $schema, $edition->id, $paper->id, $reviewer->id, $snapshot->id, $barrier], base_path(), $workerEnvironment);
            $process->setTimeout(30);
            $process->start();
            $processes[] = $process;
        }
        foreach ($processes as $process) {
            expect($process->waitUntil(fn ($type, $output) => str_contains($output, 'READY')))->toBeTrue();
        }
        file_put_contents($barrier, 'go');
        $outputs = [];
        foreach ($processes as $process) {
            expect($process->wait())->toBe(0);
            $outputs[] = $process->getOutput();
        }
        expect(count(array_filter($outputs, fn ($output) => str_contains($output, 'CLAIMED:'))))->toBe(1)
            ->and(count(array_filter($outputs, fn ($output) => str_contains($output, 'REJECTED'))))->toBe(1)
            ->and(ReviewAssignment::query()->where('academic_stage', 'ABSTRACT')->whereIn('status', ['INVITED', 'ACCEPTED'])->count())->toBe(1)
            ->and(DB::table('activity_log')->where('event', 'abstract_review_claimed')->count())->toBe(1);
    } finally {
        foreach ($processes as $process) {
            if ($process->isRunning()) {
                $process->stop();
            }
        }
        unlink($barrier);
        setPermissionsTeamId(null);
    }
    // Deliberately retain this isolated schema and evidence for inspection; no drop/reset/cleanup SQL.
});
