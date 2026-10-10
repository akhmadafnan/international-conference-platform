<?php

declare(strict_types=1);

use App\Actions\Submission\EvaluateDraftSubmissionReadinessAction;
use App\Enums\BillingMode;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\Track;
use App\Models\User;
use App\Support\Submission\SubmissionReadinessResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** @return array{min_keywords: int, max_keywords: int, track_required: bool, abstract_file_required: bool} */
function p03gPolicy(array $overrides = []): array
{
    return array_replace([
        'min_keywords' => 3,
        'max_keywords' => 5,
        'track_required' => false,
        'abstract_file_required' => false,
    ], $overrides);
}

function p03gEdition(?array $policy = null, string $code = 'ICHES27'): ConferenceEdition
{
    $series = ConferenceSeries::query()->firstOrCreate(
        ['code' => 'ICHES'],
        ['name' => 'International Conference on Humanity Education and Society', 'status' => 'ACTIVE'],
    );

    return ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => $code,
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'Universitas Islam Syarifuddin Lumajang',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
        'settings_json' => $policy === null ? null : ['abstract_submission' => $policy],
    ]);
}

/** @return array{edition: ConferenceEdition, package: ParticipationPackage, actor: User, registration: Registration, submission: Submission} */
function p03gContext(?array $policy = null, string $status = 'PENDING'): array
{
    $edition = p03gEdition($policy ?? p03gPolicy());
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
        'status' => $status,
    ]);
    $submission = Submission::query()->create([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-P-'.Str::upper(Str::random(8)),
        'track_id' => null,
        'primary_locale' => 'id',
        'academic_status' => 'DRAFT',
        'current_abstract_version' => 0,
    ]);

    return compact('edition', 'package', 'actor', 'registration', 'submission');
}

function p03gPopulate(Submission $submission, int $keywordCount = 3): SubmissionContributor
{
    foreach (['id', 'en', 'ar'] as $locale) {
        $submission->translations()->create([
            'locale' => $locale,
            'title' => 'Title '.$locale,
            'abstract_text' => 'Abstract '.$locale,
        ]);
    }

    for ($i = 1; $i <= $keywordCount; $i++) {
        $submission->keywords()->create([
            'locale' => 'id',
            'value' => 'keyword '.$i,
            'sequence' => $i,
        ]);
    }

    $contributor = $submission->contributors()->create([
        'display_name' => 'Budi Santoso',
        'given_name' => 'Budi',
        'family_name' => 'Santoso',
        'single_name' => false,
        'email' => 'budi@example.com',
        'country_code' => 'ID',
        'orcid_uri' => 'https://orcid.org/0000-0002-1825-0097',
        'orcid_verification_state' => 'UNVERIFIED',
        'is_corresponding' => true,
        'sequence' => 1,
        'contributor_role' => 'AUTHOR',
    ]);
    $contributor->affiliations()->create([
        'institution_id' => null,
        'institution_name_snapshot' => 'Universitas Islam Syarifuddin Lumajang',
        'ror_uri_snapshot' => 'https://ror.org/01cab9d94',
        'country_code' => 'ID',
        'sequence' => 1,
    ]);

    return $contributor;
}

function p03gResult(User $actor, Submission $submission): SubmissionReadinessResult
{
    return app(EvaluateDraftSubmissionReadinessAction::class)->handle($actor, $submission);
}

/** @return list<string> */
function p03gCodes(SubmissionReadinessResult $result): array
{
    return array_column($result->findings, 'code');
}

function p03gTrack(ConferenceEdition $edition, bool $active = true): Track
{
    return Track::query()->create([
        'edition_id' => $edition->id,
        'code' => 'TRACK-'.Str::upper(Str::random(6)),
        'name_i18n' => ['id' => 'Pendidikan', 'en' => 'Education'],
        'active' => $active,
    ]);
}

function p03gFile(Submission $submission, string $role = 'ABSTRACT_FILE', string $status = 'ACTIVE', int $version = 1): void
{
    $userId = $submission->registration->membership->user_id;
    $stored = StoredFile::query()->create([
        'disk' => 'private',
        'path' => 'submission-files/abstracts/example-v'.$version.'.pdf',
        'original_name' => 'abstract.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 50,
        'checksum_sha256' => str_repeat('a', 64),
        'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $userId,
    ]);
    $submission->files()->create([
        'stored_file_id' => $stored->id,
        'file_role' => $role,
        'manuscript_version' => $version,
        'uploaded_by_user_id' => $userId,
        'submitted_at' => now(),
        'status' => $status,
    ]);
}

test('three and five keywords allow READY without payment or an abstract attachment', function (int $count) {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission, $count);

    $result = p03gResult($actor, $submission);

    expect($result->status)->toBe('READY')
        ->and($result->findings)->toBe([])
        ->and($result->toArray())->toBe(['status' => 'READY', 'findings' => []]);
})->with([3, 5]);

test('two or six keywords are blocked under explicit edition policy', function (int $count) {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission, $count);

    $result = p03gResult($actor, $submission);
    expect($result->status)->toBe('BLOCKED')
        ->and(p03gCodes($result))->toContain('KEYWORDS_COUNT_INVALID');
})->with([2, 6]);

test('unconfigured policy fails closed even with complete metadata', function () {
    ['edition' => $edition, 'actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $edition->update(['settings_json' => null]);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('EDITION_POLICY_UNCONFIGURED');
});

test('malformed policy fails closed without guessing defaults', function (array $policy) {
    ['edition' => $edition, 'actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $edition->update(['settings_json' => ['abstract_submission' => $policy]]);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('EDITION_POLICY_UNCONFIGURED');
})->with([
    [['min_keywords' => '3', 'max_keywords' => 5, 'track_required' => false, 'abstract_file_required' => false]],
    [['min_keywords' => 5, 'max_keywords' => 3, 'track_required' => false, 'abstract_file_required' => false]],
    [['min_keywords' => 3, 'max_keywords' => 5, 'track_required' => 'false', 'abstract_file_required' => false]],
    [['min_keywords' => 3, 'max_keywords' => 5, 'track_required' => false]],
]);

test('empty draft exposes meaningful blockers without modifying it', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    $result = p03gResult($actor, $submission);

    expect($result->status)->toBe('BLOCKED')
        ->and(p03gCodes($result))->toContain('PRIMARY_TITLE_MISSING', 'PRIMARY_ABSTRACT_MISSING', 'KEYWORDS_COUNT_INVALID', 'CONTRIBUTORS_MISSING');
});

test('only primary scholarly locale keywords are counted and optional translations do not block', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $submission->translations()->where('locale', 'ar')->delete();
    $submission->keywords()->create(['locale' => 'en', 'value' => 'other', 'sequence' => 1]);

    $result = p03gResult($actor, $submission);
    expect($result->status)->toBe('WARNING')
        ->and(p03gCodes($result))->toContain('OPTIONAL_TRANSLATIONS_MISSING')
        ->not->toContain('KEYWORDS_COUNT_INVALID');
});

test('duplicate and noncontiguous primary keywords are blocked', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $submission->keywords()->where('sequence', 2)->update(['value' => 'KEYWORD 1']);
    $submission->keywords()->where('sequence', 3)->update(['sequence' => 5]);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('KEYWORDS_DUPLICATE', 'KEYWORDS_SEQUENCE_INVALID');
});

test('invalid primary locale is blocked rather than triggering an enum cast error', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    DB::table('submissions')->where('id', $submission->id)->update(['primary_locale' => 'xx']);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('PRIMARY_LOCALE_INVALID');
});

test('track is required only by edition policy and must belong to that active edition', function () {
    ['edition' => $edition, 'actor' => $actor, 'submission' => $submission] = p03gContext(p03gPolicy(['track_required' => true]));
    p03gPopulate($submission);
    $track = p03gTrack($edition);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('TRACK_REQUIRED');
    $submission->update(['track_id' => $track->id]);
    expect(p03gResult($actor, $submission)->status)->toBe('READY');
    $track->update(['active' => false]);
    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('TRACK_INVALID', 'EDITION_TRACKS_UNAVAILABLE');
});

test('foreign edition track is rejected even when track selection is optional', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $otherEdition = p03gEdition(p03gPolicy(), 'OTHER27');
    $foreign = p03gTrack($otherEdition);
    $submission->update(['track_id' => $foreign->id]);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('TRACK_INVALID');
});

test('contributor corresponding author and sequence must be valid at readiness', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    $contributor = p03gPopulate($submission);
    $contributor->update(['is_corresponding' => false, 'sequence' => 2]);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('CORRESPONDING_AUTHOR_INVALID', 'CONTRIBUTOR_ORDER_INVALID');
});

test('two corresponding authors are blocked', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    $first = p03gPopulate($submission);
    $second = $first->replicate(['id']);
    $second->sequence = 2;
    $second->email = 'second@example.com';
    $second->save();
    $second->affiliations()->create([
        'institution_name_snapshot' => 'University of Indonesia',
        'ror_uri_snapshot' => 'https://ror.org/123456789',
        'country_code' => 'ID',
        'sequence' => 1,
    ]);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('CORRESPONDING_AUTHOR_INVALID');
});

test('invalid contributor email and supplied orcid block while missing orcid only warns', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    $contributor = p03gPopulate($submission);
    $contributor->update(['email' => 'not-an-email', 'orcid_uri' => 'https://orcid.org/0000-0000-0000-0000']);
    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('CONTRIBUTOR_EMAIL_INVALID', 'CONTRIBUTOR_ORCID_INVALID');

    $contributor->update(['email' => 'valid@example.com', 'orcid_uri' => null]);
    $result = p03gResult($actor, $submission);
    expect($result->status)->toBe('WARNING')
        ->and(p03gCodes($result))->toContain('CONTRIBUTOR_ORCID_MISSING');
});

test('missing affiliation blocks but valid manual affiliation only warns for ROR', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    $contributor = p03gPopulate($submission);
    $affiliation = $contributor->affiliations()->firstOrFail();
    $affiliation->delete();
    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('CONTRIBUTOR_AFFILIATION_MISSING');

    $contributor->affiliations()->create([
        'institution_name_snapshot' => 'Local Institution',
        'ror_uri_snapshot' => null,
        'country_code' => 'ID',
        'sequence' => 1,
    ]);
    $result = p03gResult($actor, $submission);
    expect($result->status)->toBe('WARNING')
        ->and(p03gCodes($result))->toContain('AFFILIATION_ROR_MISSING');
});

test('required file policy checks latest active version and private stored metadata', function () {
    ['edition' => $edition, 'actor' => $actor, 'submission' => $submission] = p03gContext(p03gPolicy(['abstract_file_required' => true]));
    p03gPopulate($submission);
    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('ABSTRACT_FILE_REQUIRED');

    p03gFile($submission, 'ABSTRACT_FILE', 'ACTIVE', 1);
    expect(p03gResult($actor, $submission)->status)->toBe('READY');
    $submission->files()->update(['status' => 'SUPERSEDED']);
    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('ABSTRACT_FILE_REQUIRED');

    $submission->files()->update(['status' => 'ACTIVE']);
    $submission->files()->firstOrFail()->storedFile->update(['visibility_class' => 'PUBLIC']);
    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('ABSTRACT_FILE_METADATA_INVALID');
});

test('wrong-role file never fulfills mandatory abstract attachment', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext(p03gPolicy(['abstract_file_required' => true]));
    p03gPopulate($submission);
    p03gFile($submission, 'FULL_ARTICLE');

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('ABSTRACT_FILE_REQUIRED');
});

test('optional references remain nonblocking and a missing doi may warn', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $submission->references()->create(['sequence' => 1, 'raw_citation' => 'A published reference', 'doi' => null]);

    $result = p03gResult($actor, $submission);
    expect($result->status)->toBe('WARNING')
        ->and(p03gCodes($result))->toContain('REFERENCE_DOI_MISSING');
});

test('cross-owner, cancelled registration and non-draft state deny access', function () {
    ['actor' => $actor, 'registration' => $registration, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $another = User::factory()->create();

    expect(fn () => p03gResult($another, $submission))->toThrow(DomainException::class);
    $registration->update(['status' => 'CANCELLED']);
    expect(fn () => p03gResult($actor, $submission))->toThrow(DomainException::class);
    $registration->update(['status' => 'PENDING']);
    $submission->update(['academic_status' => 'SUBMITTED']);
    expect(fn () => p03gResult($actor, $submission))->toThrow(DomainException::class);
});

test('stale supplied model is ignored and current draft metadata is evaluated', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $stale = clone $submission;
    $submission->translations()->where('locale', 'id')->delete();

    expect(p03gCodes(p03gResult($actor, $stale)))->toContain('PRIMARY_TITLE_MISSING', 'PRIMARY_ABSTRACT_MISSING');
});

test('unpaid and inactive historical same-edition package do not gate author readiness', function () {
    ['actor' => $actor, 'package' => $package, 'submission' => $submission] = p03gContext(p03gPolicy(), 'PAYMENT_PENDING');
    p03gPopulate($submission);
    $package->update(['active' => false]);

    expect(p03gResult($actor, $submission)->status)->toBe('READY');
});

test('evaluator is deterministic and causes no SQL writes, audit, snapshots or storage changes', function () {
    Storage::fake('private');
    ['actor' => $actor, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $activityBefore = DB::table('activity_log')->count();
    $snapshotBefore = DB::table('submission_snapshots')->count();
    $updatedBefore = $submission->fresh()->updated_at?->toDateTimeString();
    $mutating = [];
    $capture = true;

    DB::listen(function ($query) use (&$capture, &$mutating): void {
        if ($capture && preg_match('/^\s*(INSERT|UPDATE|DELETE|REPLACE|CREATE|ALTER|DROP|TRUNCATE)\b/i', $query->sql) === 1) {
            $mutating[] = $query->sql;
        }
    });
    $first = p03gResult($actor, $submission)->toArray();
    $second = p03gResult($actor, $submission)->toArray();
    $capture = false;

    expect($first)->toBe($second)
        ->and($mutating)->toBe([])
        ->and(DB::table('activity_log')->count())->toBe($activityBefore)
        ->and(DB::table('submission_snapshots')->count())->toBe($snapshotBefore)
        ->and($submission->fresh()->academic_status)->toBe('DRAFT')
        ->and($submission->fresh()->submitted_at)->toBeNull()
        ->and($submission->fresh()->updated_at?->toDateTimeString())->toBe($updatedBefore)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('required tracks with no active choices cause a safe edition configuration blocker', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext(p03gPolicy(['track_required' => true]));
    p03gPopulate($submission);

    $result = p03gResult($actor, $submission);
    expect(p03gCodes($result))->toContain('EDITION_TRACKS_UNAVAILABLE', 'TRACK_REQUIRED');
});

test('a newer superseded file must not be masked by an older active version', function () {
    ['actor' => $actor, 'submission' => $submission] = p03gContext(p03gPolicy(['abstract_file_required' => true]));
    p03gPopulate($submission);
    p03gFile($submission, 'ABSTRACT_FILE', 'ACTIVE', 1);
    p03gFile($submission, 'ABSTRACT_FILE', 'SUPERSEDED', 2);

    expect(p03gCodes(p03gResult($actor, $submission)))->toContain('ABSTRACT_FILE_METADATA_INVALID');
});

test('registration from another edition never authorizes access to this submission', function () {
    ['actor' => $actor, 'registration' => $registration, 'submission' => $submission] = p03gContext();
    p03gPopulate($submission);
    $otherEdition = p03gEdition(p03gPolicy(), 'OTHER27');
    $registration->membership->update(['edition_id' => $otherEdition->id]);

    expect(fn () => p03gResult($actor, $submission))->toThrow(DomainException::class);
});
