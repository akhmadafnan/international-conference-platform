<?php

use App\Enums\BillingMode;
use App\Enums\Locale;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\ContributorAffiliation;
use App\Models\EditionMembership;
use App\Models\GeneratedDocument;
use App\Models\Institution;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\SubmissionFile;
use App\Models\SubmissionKeyword;
use App\Models\SubmissionReference;
use App\Models\SubmissionSnapshot;
use App\Models\SubmissionTranslation;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

function makePhase03Context(): array
{
    $series = ConferenceSeries::query()->create([
        'code' => 'ICHES',
        'name' => 'International Conference on Humanity Education and Society',
        'status' => 'ACTIVE',
    ]);

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

    $user = User::factory()->create();

    $membership = EditionMembership::query()->create([
        'edition_id' => $edition->id,
        'user_id' => $user->id,
        'membership_status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $registration = Registration::query()->create([
        'membership_id' => $membership->id,
        'registration_code' => 'REG-ICHES27-0001',
        'package_id' => $package->id,
        'package_name_snapshot' => 'Presenter',
        'billing_mode' => BillingMode::PAID->value,
        'fee_amount' => '750000.00',
        'currency_code' => 'IDR',
        'status' => 'PENDING',
    ]);

    $track = Track::query()->create([
        'edition_id' => $edition->id,
        'code' => 'EDUCATION',
        'name_i18n' => ['id' => 'Pendidikan', 'en' => 'Education'],
        'active' => true,
    ]);

    return compact('edition', 'registration', 'track', 'user');
}

test('phase 03 scholarly records persist with UUIDv7 identities and explicit relationships', function () {
    ['edition' => $edition, 'registration' => $registration, 'track' => $track] = makePhase03Context();

    $submission = Submission::query()->create([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-001',
        'track_id' => $track->id,
        'primary_locale' => Locale::Indonesian->value,
        'academic_status' => 'DRAFT',
        'current_abstract_version' => 0,
    ]);

    $translation = SubmissionTranslation::query()->create([
        'submission_id' => $submission->id,
        'locale' => Locale::Indonesian->value,
        'title' => 'Judul Abstrak',
        'abstract_text' => 'Isi abstrak.',
    ]);

    $keyword = SubmissionKeyword::query()->create([
        'submission_id' => $submission->id,
        'locale' => Locale::Indonesian->value,
        'value' => 'pendidikan',
        'sequence' => 1,
    ]);

    expect(Str::isUuid($submission->id, 7))->toBeTrue()
        ->and(Str::isUuid($translation->id, 7))->toBeTrue()
        ->and(Str::isUuid($keyword->id, 7))->toBeTrue()
        ->and($submission->primary_locale)->toBe(Locale::Indonesian)
        ->and($submission->edition->is($edition))->toBeTrue()
        ->and($submission->registration->is($registration))->toBeTrue()
        ->and($submission->track->is($track))->toBeTrue()
        ->and($submission->translations()->count())->toBe(1)
        ->and($submission->keywords()->count())->toBe(1);
});

test('phase 03 database constraints preserve scoped scholarly ordering and identity', function () {
    ['edition' => $edition, 'registration' => $registration] = makePhase03Context();

    $submission = Submission::query()->create([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-002',
        'primary_locale' => Locale::English->value,
        'academic_status' => 'DRAFT',
    ]);

    SubmissionTranslation::query()->create([
        'submission_id' => $submission->id,
        'locale' => Locale::English->value,
        'title' => 'Canonical Title',
        'abstract_text' => 'Canonical abstract.',
    ]);

    expect(fn () => SubmissionTranslation::query()->create([
        'submission_id' => $submission->id,
        'locale' => Locale::English->value,
        'title' => 'Duplicate locale',
        'abstract_text' => 'Duplicate.',
    ]))->toThrow(QueryException::class);

    SubmissionContributor::query()->create([
        'submission_id' => $submission->id,
        'display_name' => 'Afnan',
        'given_name' => 'Afnan',
        'family_name' => null,
        'single_name' => true,
        'email' => 'author@example.test',
        'country_code' => 'ID',
        'is_corresponding' => true,
        'sequence' => 1,
        'contributor_role' => 'AUTHOR',
    ]);

    expect(fn () => SubmissionContributor::query()->create([
        'submission_id' => $submission->id,
        'display_name' => 'Second Author',
        'given_name' => 'Second',
        'family_name' => 'Author',
        'single_name' => false,
        'email' => 'second@example.test',
        'country_code' => 'ID',
        'is_corresponding' => false,
        'sequence' => 1,
        'contributor_role' => 'AUTHOR',
    ]))->toThrow(QueryException::class);
});

test('phase 03 supports institution first affiliations with manual fallback and raw references', function () {
    ['edition' => $edition, 'registration' => $registration] = makePhase03Context();

    $submission = Submission::query()->create([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-003',
        'primary_locale' => Locale::Arabic->value,
        'academic_status' => 'DRAFT',
    ]);

    $contributor = SubmissionContributor::query()->create([
        'submission_id' => $submission->id,
        'display_name' => 'Afnan',
        'given_name' => 'Afnan',
        'family_name' => null,
        'single_name' => true,
        'email' => 'private-author@example.test',
        'country_code' => 'ID',
        'is_corresponding' => true,
        'sequence' => 1,
        'contributor_role' => 'AUTHOR',
    ]);

    $institution = Institution::query()->create([
        'preferred_name' => 'Universitas Islam Syarifuddin Lumajang',
        'country_code' => 'ID',
        'source' => 'MANUAL',
    ]);

    $affiliation = ContributorAffiliation::query()->create([
        'submission_contributor_id' => $contributor->id,
        'institution_id' => $institution->id,
        'institution_name_snapshot' => $institution->preferred_name,
        'subdivision_text' => 'Fakultas Tarbiyah dan Keguruan',
        'country_code' => 'ID',
        'sequence' => 1,
    ]);

    $manualAffiliation = ContributorAffiliation::query()->create([
        'submission_contributor_id' => $contributor->id,
        'institution_id' => null,
        'institution_name_snapshot' => 'Independent Research Group',
        'ror_uri_snapshot' => null,
        'sequence' => 2,
    ]);

    $reference = SubmissionReference::query()->create([
        'submission_id' => $submission->id,
        'sequence' => 1,
        'raw_citation' => 'Author. (2026). A preserved raw citation.',
    ]);

    expect($contributor->family_name)->toBeNull()
        ->and($contributor->email)->toBe('private-author@example.test')
        ->and($affiliation->institution->is($institution))->toBeTrue()
        ->and($manualAffiliation->institution_id)->toBeNull()
        ->and($reference->raw_citation)->toBe('Author. (2026). A preserved raw citation.');
});

test('phase 03 preserves immutable file lineage snapshots and nullable actual presenter reference', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user] = makePhase03Context();

    $submission = Submission::query()->create([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-004',
        'primary_locale' => Locale::English->value,
        'academic_status' => 'DRAFT',
    ]);

    expect($submission->actual_presenter_contributor_id)->toBeNull();

    $contributor = SubmissionContributor::query()->create([
        'submission_id' => $submission->id,
        'display_name' => 'Presenter Candidate',
        'given_name' => 'Presenter',
        'family_name' => 'Candidate',
        'single_name' => false,
        'email' => 'presenter@example.test',
        'country_code' => 'ID',
        'is_corresponding' => true,
        'sequence' => 1,
        'contributor_role' => 'AUTHOR',
    ]);

    $firstStoredFile = StoredFile::query()->create([
        'disk' => 'private',
        'path' => 'submissions/original.pdf',
        'original_name' => 'abstract.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'checksum_sha256' => str_repeat('a', 64),
        'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $user->id,
    ]);

    $replacementStoredFile = StoredFile::query()->create([
        'disk' => 'private',
        'path' => 'submissions/replacement.pdf',
        'original_name' => 'abstract-replacement.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 120,
        'checksum_sha256' => str_repeat('b', 64),
        'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $user->id,
    ]);

    $firstFile = SubmissionFile::query()->create([
        'submission_id' => $submission->id,
        'stored_file_id' => $firstStoredFile->id,
        'file_role' => 'ABSTRACT',
        'uploaded_by_user_id' => $user->id,
        'submitted_at' => now(),
        'status' => 'SUPERSEDED',
    ]);

    $replacement = SubmissionFile::query()->create([
        'submission_id' => $submission->id,
        'stored_file_id' => $replacementStoredFile->id,
        'file_role' => 'ABSTRACT',
        'uploaded_by_user_id' => $user->id,
        'submitted_at' => now(),
        'supersedes_submission_file_id' => $firstFile->id,
        'status' => 'ACTIVE',
    ]);

    $snapshot = SubmissionSnapshot::query()->create([
        'submission_id' => $submission->id,
        'snapshot_type' => 'INITIAL_SUBMISSION',
        'version' => 1,
        'metadata_json' => ['paper_code' => $submission->paper_code],
        'checksum' => str_repeat('c', 64),
        'created_by_user_id' => $user->id,
    ]);

    $submission->update(['actual_presenter_contributor_id' => $contributor->id]);

    $document = GeneratedDocument::query()->create([
        'edition_id' => $edition->id,
        'document_type' => 'TEST_SUBMISSION_DOCUMENT',
        'registration_id' => $registration->id,
        'submission_id' => $submission->id,
        'recipient_user_id' => $user->id,
        'status' => 'ISSUED',
        'snapshot_json' => ['paper_code' => $submission->paper_code],
    ]);

    expect($replacement->storedFile->is($replacementStoredFile))->toBeTrue()
        ->and($replacement->supersedes->is($firstFile))->toBeTrue()
        ->and($snapshot->metadata_json['paper_code'])->toBe($submission->paper_code)
        ->and($submission->fresh()->actualPresenter->is($contributor))->toBeTrue()
        ->and($document->submission->is($submission))->toBeTrue();
});
