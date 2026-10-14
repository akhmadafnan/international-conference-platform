<?php

declare(strict_types=1);

use App\Actions\Submission\SubmitDraftAbstractAction;
use App\Enums\BillingMode;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\Track;
use App\Models\User;
use App\Support\Submission\InitialSubmissionSnapshotBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** @return array{edition: ConferenceEdition, package: ParticipationPackage, actor: User, registration: Registration, submission: Submission} */
function p03hContext(bool $window = true, bool $active = true, array $policy = []): array
{
    $series = ConferenceSeries::query()->firstOrCreate(
        ['code' => 'ICHES'],
        ['name' => 'International Conference on Humanity Education and Society', 'status' => 'ACTIVE'],
    );
    $edition = ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => 'ICHES27',
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'Universitas Islam Syarifuddin Lumajang',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
        'settings_json' => ['abstract_submission' => array_replace([
            'min_keywords' => 3,
            'max_keywords' => 5,
            'track_required' => false,
            'abstract_file_required' => false,
        ], $policy)],
    ]);
    $package = ParticipationPackage::query()->create([
        'edition_id' => $edition->id,
        'code' => 'PRESENTER',
        'name_i18n' => ['id' => 'Presenter', 'en' => 'Presenter'],
        'billing_mode' => BillingMode::PAID->value,
        'price' => '750000.00',
        'currency_code' => 'IDR',
        'active' => true,
        'display_order' => 1,
    ]);
    $actor = User::factory()->create();
    $membership = EditionMembership::query()->create([
        'edition_id' => $edition->id,
        'user_id' => $actor->id,
        'membership_status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    $registration = Registration::query()->create([
        'membership_id' => $membership->id,
        'registration_code' => 'REG-'.Str::uuid(),
        'package_id' => $package->id,
        'package_name_snapshot' => 'Presenter',
        'billing_mode' => BillingMode::PAID->value,
        'fee_amount' => '750000.00',
        'currency_code' => 'IDR',
        'status' => 'PAYMENT_PENDING',
    ]);
    $submission = Submission::query()->create([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-P-'.Str::upper(Str::random(8)),
        'primary_locale' => 'id',
        'academic_status' => 'DRAFT',
        'current_abstract_version' => 0,
    ]);
    if ($window) {
        $edition->workflowWindows()->create([
            'window_code' => 'ABSTRACT_SUBMISSION',
            'active' => $active,
            'opens_at' => now()->subDay(),
            'closes_at' => now()->addDay(),
        ]);
    }

    return compact('edition', 'package', 'actor', 'registration', 'submission');
}

function p03hOtherEdition(): ConferenceEdition
{
    $series = ConferenceSeries::query()->firstOrFail();

    return ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => 'OTHER27',
        'edition_number' => 7,
        'year' => 2027,
        'host_name' => 'Another Host',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);
}

function p03hPopulate(Submission $submission, bool $allTranslations = true): void
{
    foreach ($allTranslations ? ['id', 'en', 'ar'] : ['id'] as $locale) {
        $submission->translations()->create([
            'locale' => $locale,
            'title' => 'Title '.$locale,
            'abstract_text' => 'Abstract '.$locale,
        ]);
    }
    foreach ([1, 2, 3] as $i) {
        $submission->keywords()->create([
            'locale' => 'id',
            'value' => 'keyword '.$i,
            'sequence' => $i,
        ]);
    }
    $author = $submission->contributors()->create([
        'display_name' => 'Budi Santoso',
        'given_name' => 'Budi',
        'family_name' => 'Santoso',
        'single_name' => false,
        'email' => 'budi@example.com',
        'country_code' => 'ID',
        'orcid_uri' => $allTranslations ? 'https://orcid.org/0000-0002-1825-0097' : null,
        'orcid_verification_state' => $allTranslations ? 'UNVERIFIED' : null,
        'is_corresponding' => true,
        'sequence' => 1,
        'contributor_role' => 'AUTHOR',
    ]);
    $author->affiliations()->create([
        'institution_id' => null,
        'institution_name_snapshot' => 'Universitas Islam Syarifuddin Lumajang',
        'ror_uri_snapshot' => $allTranslations ? 'https://ror.org/01cab9d94' : null,
        'country_code' => 'ID',
        'sequence' => 1,
    ]);
}

function p03hSubmit(User $actor, Submission $submission): Submission
{
    return app(SubmitDraftAbstractAction::class)->handle($actor, $submission);
}

function p03hAssertNoWrite(Submission $submission): void
{
    expect($submission->fresh()->academic_status)->toBe('DRAFT')
        ->and($submission->fresh()->submitted_at)->toBeNull()
        ->and($submission->fresh()->current_abstract_version)->toBe(0)
        ->and($submission->snapshots()->count())->toBe(0)
        ->and(DB::table('activity_log')->where('event', 'submission_officially_submitted')->count())->toBe(0);
}

function p03hFile(Submission $submission, int $version = 1, string $status = 'ACTIVE'): void
{
    $ownerId = $submission->registration->membership->user_id;
    $stored = StoredFile::query()->create([
        'disk' => 'private',
        'path' => 'submission-files/abstracts/test-v'.$version.'.pdf',
        'original_name' => 'private-paper.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'checksum_sha256' => str_repeat('a', 64),
        'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $ownerId,
    ]);
    $submission->files()->create([
        'stored_file_id' => $stored->id,
        'file_role' => 'ABSTRACT_FILE',
        'manuscript_version' => $version,
        'uploaded_by_user_id' => $ownerId,
        'submitted_at' => now(),
        'status' => $status,
    ]);
}

test('official submit writes exactly one immutable initial snapshot and one safe audit with pending payment', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $submitted = p03hSubmit($actor, $submission);
    $snapshot = $submitted->snapshots()->sole();
    $builder = app(InitialSubmissionSnapshotBuilder::class);
    $activity = DB::table('activity_log')->where('event', 'submission_officially_submitted')->sole();

    expect($submitted->academic_status)->toBe('SUBMITTED')
        ->and($submitted->submitted_at)->not->toBeNull()
        ->and($submitted->current_abstract_version)->toBe(1)
        ->and($snapshot->snapshot_type)->toBe('INITIAL_SUBMISSION')
        ->and($snapshot->version)->toBe(1)
        ->and($snapshot->metadata_json['schema'])->toBe('iches.initial_submission.v1')
        ->and($snapshot->metadata_json['abstract_file'])->toBeNull()
        ->and($snapshot->metadata_json['contributors'][0]['email'])->toBe('budi@example.com')
        ->and($snapshot->checksum)->toBe($builder->checksum($snapshot->metadata_json))
        ->and(strlen($snapshot->checksum))->toBe(64)
        ->and($snapshot->created_by_user_id)->toBe($actor->id)
        ->and($activity->description)->toBe('Abstract officially submitted')
        ->and($activity->properties)->not->toContain('budi@example.com');
});

test('snapshot freezes Edition and Track scholarly labels as of official submission', function () {
    ['edition' => $edition, 'actor' => $actor, 'submission' => $submission] = p03hContext(policy: ['track_required' => true]);
    p03hPopulate($submission);
    $track = Track::query()->create([
        'edition_id' => $edition->id,
        'code' => 'EDUCATION',
        'name_i18n' => ['id' => 'Pendidikan', 'en' => 'Education'],
        'active' => true,
    ]);
    $submission->update(['track_id' => $track->id]);
    $snapshot = p03hSubmit($actor, $submission)->snapshots()->sole();
    expect($snapshot->metadata_json['track']['code'])->toBe('EDUCATION')
        ->and($snapshot->metadata_json['track']['name_i18n']['id'])->toBe('Pendidikan')
        ->and($snapshot->metadata_json['edition']['code'])->toBe('ICHES27');
    $track->update(['name_i18n' => ['id' => 'Berubah', 'en' => 'Changed']]);
    expect($snapshot->fresh()->metadata_json['track']['name_i18n']['id'])->toBe('Pendidikan');
});

test('WARNING readiness allows official submission without all optional translation or orcid', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission, false);
    expect(p03hSubmit($actor, $submission)->academic_status)->toBe('SUBMITTED');
});

test('historically inactive paid package does not gate official submit', function () {
    ['actor' => $actor, 'package' => $package, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $package->update(['active' => false]);
    expect(p03hSubmit($actor, $submission)->academic_status)->toBe('SUBMITTED');
});

test('no abstract submission window row blocks and causes zero writes', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext(false);
    p03hPopulate($submission);
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
});

test('inactive abstract submission window blocks and causes zero writes', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext(true, false);
    p03hPopulate($submission);
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
});

test('future and expired submission windows are both blocked', function (string $mode) {
    ['edition' => $edition, 'actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $window = $edition->workflowWindows()->firstOrFail();
    if ($mode === 'future') {
        $window->update(['opens_at' => now()->addHour(), 'closes_at' => now()->addDays(2)]);
    } else {
        $window->update(['opens_at' => now()->subDays(2), 'closes_at' => now()->subHour()]);
    }
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
})->with(['future', 'expired']);

test('malformed or unbounded window is blocked by strict P03-H rule', function (string $mode) {
    ['edition' => $edition, 'actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $window = $edition->workflowWindows()->firstOrFail();
    if ($mode === 'inverted') {
        $window->update(['opens_at' => now()->addDay(), 'closes_at' => now()->subDay()]);
    } elseif ($mode === 'no-start') {
        $window->update(['opens_at' => null]);
    } else {
        $window->update(['closes_at' => null]);
    }
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
})->with(['inverted', 'no-start', 'no-close']);

test('empty scholarly metadata cannot officially submit', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
});

test('missing or invalid edition readiness configuration blocks official submit', function (string $mode) {
    ['edition' => $edition, 'actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $edition->update(['settings_json' => $mode === 'missing' ? null : ['abstract_submission' => ['min_keywords' => '3']]]);
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
})->with(['missing', 'malformed']);

test('incomplete author and invalid keyword count deny official submission', function (string $mode) {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    if ($mode === 'author') {
        $submission->contributors()->update(['is_corresponding' => false]);
    } else {
        $submission->keywords()->where('sequence', 3)->delete();
    }
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
})->with(['author', 'keywords']);

test('required abstract file policy is enforced and valid private file is snapshotted', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext(policy: ['abstract_file_required' => true]);
    p03hPopulate($submission);
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
    p03hFile($submission);
    $submitted = p03hSubmit($actor, $submission);
    $file = $submitted->snapshots()->sole()->metadata_json['abstract_file'];
    expect($file['manuscript_version'])->toBe(1)
        ->and($file['sha256'])->toBe(str_repeat('a', 64))
        ->and($file)->not->toHaveKey('path');
});

test('even optional uploaded files must have consistent current version lineage for evidence', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    p03hFile($submission, 1, 'ACTIVE');
    p03hFile($submission, 2, 'SUPERSEDED');
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
});

test('cross-owner cancelled and foreign-edition registrations deny official submission', function (string $mode) {
    ['edition' => $edition, 'actor' => $actor, 'registration' => $registration, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    if ($mode === 'cross-owner') {
        $actor = User::factory()->create();
    } elseif ($mode === 'cancelled') {
        $registration->update(['status' => 'CANCELLED']);
    } else {
        $registration->membership->update(['edition_id' => p03hOtherEdition()->id]);
    }
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
})->with(['cross-owner', 'cancelled', 'foreign-edition']);

test('foreign participation package is refused even if registration is still pending', function () {
    ['actor' => $actor, 'registration' => $registration, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $otherEdition = p03hOtherEdition();
    $other = ParticipationPackage::query()->create([
        'edition_id' => $otherEdition->id,
        'code' => 'OTHER',
        'name_i18n' => ['id' => 'Lain', 'en' => 'Other'],
        'billing_mode' => BillingMode::FREE->value,
        'price' => '0.00',
        'currency_code' => 'IDR',
        'active' => true,
        'display_order' => 1,
    ]);
    $registration->update(['package_id' => $other->id]);
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
});

test('stale caller metadata is ignored at authoritative submit-time readiness', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $stale = clone $submission;
    $submission->translations()->where('locale', 'id')->delete();
    expect(fn () => p03hSubmit($actor, $stale))->toThrow(DomainException::class);
    p03hAssertNoWrite($submission);
});

test('duplicate official submit never produces another snapshot or success audit', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    p03hSubmit($actor, $submission);
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class)
        ->and($submission->snapshots()->count())->toBe(1)
        ->and(DB::table('activity_log')->where('event', 'submission_officially_submitted')->count())->toBe(1);
});

test('a preexisting INITIAL_SUBMISSION snapshot cannot be silently replaced', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $submission->snapshots()->create([
        'snapshot_type' => 'INITIAL_SUBMISSION', 'version' => 1,
        'metadata_json' => ['historical' => true], 'checksum' => str_repeat('0', 64),
        'created_by_user_id' => $actor->id,
    ]);
    expect(fn () => p03hSubmit($actor, $submission))->toThrow(DomainException::class);
    expect($submission->fresh()->academic_status)->toBe('DRAFT')
        ->and($submission->snapshots()->count())->toBe(1)
        ->and(DB::table('activity_log')->where('event', 'submission_officially_submitted')->count())->toBe(0);
});

test('snapshot keeps immutable scholarly facts after later raw metadata changes', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $submitted = p03hSubmit($actor, $submission);
    $snapshot = $submitted->snapshots()->sole();
    $before = $snapshot->metadata_json;
    $checksum = $snapshot->checksum;
    $submission->translations()->where('locale', 'id')->update(['title' => 'Changed by direct SQL']);
    expect($snapshot->fresh()->metadata_json)->toBe($before)
        ->and($snapshot->fresh()->checksum)->toBe($checksum);
});

test('a failure during privacy-safe activity insertion rolls back snapshot and status', function () {
    ['actor' => $actor, 'submission' => $submission] = p03hContext();
    p03hPopulate($submission);
    $armed = true;
    DB::listen(function ($event) use (&$armed): void {
        if ($armed && preg_match('/^\s*insert\s+into\s+["`]?activity_log/i', $event->sql) === 1) {
            throw new RuntimeException('Simulated audit failure');
        }
    });
    try {
        expect(fn () => p03hSubmit($actor, $submission))->toThrow(RuntimeException::class);
    } finally {
        $armed = false;
    }
    p03hAssertNoWrite($submission);
});
