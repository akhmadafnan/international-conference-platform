<?php

use App\Actions\Submission\UpdateDraftSubmissionDetailsAction;
use App\Enums\BillingMode;
use App\Enums\Locale;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Track;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function detailsMutationEdition(string $editionCode = 'ICHES27'): ConferenceEdition
{
    $series = ConferenceSeries::query()->firstOrCreate(
        ['code' => 'ICHES'],
        [
            'name' => 'International Conference on Humanity Education and Society',
            'status' => 'ACTIVE',
        ],
    );

    return ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => $editionCode,
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'Universitas Islam Syarifuddin Lumajang',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);
}

function detailsMutationPackage(ConferenceEdition $edition): ParticipationPackage
{
    return ParticipationPackage::query()->create([
        'edition_id' => $edition->id,
        'code' => 'PRESENTER',
        'name_i18n' => ['id' => 'Presenter', 'en' => 'Presenter'],
        'billing_mode' => BillingMode::PAID->value,
        'price' => '750000.00',
        'currency_code' => 'IDR',
        'active' => true,
        'display_order' => 1,
    ]);
}

function detailsMutationRegistration(
    ConferenceEdition $edition,
    ParticipationPackage $package,
    string $status = 'PENDING',
): array {
    $user = User::factory()->create();

    $membership = EditionMembership::query()->create([
        'edition_id' => $edition->id,
        'user_id' => $user->id,
        'membership_status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $registration = Registration::query()->create([
        'membership_id' => $membership->id,
        'registration_code' => 'REG-'.Str::uuid()->toString(),
        'package_id' => $package->id,
        'package_name_snapshot' => 'Presenter',
        'billing_mode' => BillingMode::PAID->value,
        'fee_amount' => '750000.00',
        'currency_code' => 'IDR',
        'status' => $status,
    ]);

    return compact('user', 'membership', 'registration');
}

function detailsMutationSubmission(
    ConferenceEdition $edition,
    Registration $registration,
    array $overrides = [],
): Submission {
    return Submission::query()->create(array_merge([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-P-0001',
        'track_id' => null,
        'primary_locale' => Locale::Indonesian->value,
        'academic_status' => 'DRAFT',
        'current_abstract_version' => 0,
    ], $overrides));
}

function detailsMutationTrack(ConferenceEdition $edition, bool $active = true): Track
{
    return Track::query()->create([
        'edition_id' => $edition->id,
        'code' => $active ? 'EDUCATION' : 'INACTIVE_TRACK',
        'name_i18n' => ['id' => 'Pendidikan', 'en' => 'Education'],
        'active' => $active,
    ]);
}

function detailsMutationParticipation(): array
{
    $edition = detailsMutationEdition();
    $package = detailsMutationPackage($edition);

    ['user' => $user, 'membership' => $membership, 'registration' => $registration] = detailsMutationRegistration(
        $edition,
        $package,
    );

    return compact('edition', 'package', 'user', 'membership', 'registration');
}

function detailsMutationContext(array $submissionOverrides = []): array
{
    [
        'edition' => $edition,
        'package' => $package,
        'user' => $user,
        'membership' => $membership,
        'registration' => $registration,
    ] = detailsMutationParticipation();

    $submission = detailsMutationSubmission($edition, $registration, $submissionOverrides);

    return compact('edition', 'package', 'user', 'membership', 'registration', 'submission');
}

function detailsMutationPayload(array $overrides = []): array
{
    return array_merge([
        'primary_locale' => 'id',
        'track_id' => null,
        'translations' => [
            'id' => [
                'title' => 'Judul Abstrak',
                'subtitle' => 'Sub judul abstrak',
                'abstract' => 'Isi abstrak.',
            ],
        ],
        'keywords' => [
            'id' => ['pendidikan', 'sosial'],
        ],
    ], $overrides);
}

function detailsMutationSeedExistingDetails(Submission $submission): void
{
    $submission->translations()->create([
        'locale' => Locale::Indonesian->value,
        'title' => 'Existing Title',
        'abstract_text' => 'Existing abstract.',
    ]);

    $submission->keywords()->create([
        'locale' => Locale::Indonesian->value,
        'value' => 'existing',
        'sequence' => 1,
    ]);
}

function detailsMutationAssertUnchanged(Submission $submission): void
{
    $fresh = $submission->fresh();

    expect($fresh->primary_locale)->toBe(Locale::Indonesian)
        ->and($fresh->track_id)->toBeNull()
        ->and($fresh->academic_status)->toBe('DRAFT')
        ->and($fresh->translations)->toHaveCount(1)
        ->and($fresh->translations->first()->title)->toBe('Existing Title')
        ->and($fresh->translations->first()->abstract_text)->toBe('Existing abstract.')
        ->and($fresh->keywords)->toHaveCount(1)
        ->and($fresh->keywords->first()->value)->toBe('existing')
        ->and($fresh->keywords->first()->sequence)->toBe(1)
        ->and(DB::table('activity_log')
            ->where('log_name', 'submission')
            ->where('event', 'submission_details_updated')
            ->count())->toBe(0);
}

test('draft submission details replacement persists primary translation and ordered keywords', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user, 'submission' => $submission] = detailsMutationContext();

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'translations' => [
            'id' => [
                'title' => '  Judul Abstrak  ',
                'subtitle' => '   ',
                'abstract' => ' Isi abstrak. ',
            ],
        ],
        'keywords' => [
            'id' => ['pendidikan', 'sosial'],
        ],
    ]));

    expect($updated->primary_locale)->toBe(Locale::Indonesian)
        ->and($updated->track_id)->toBeNull()
        ->and($updated->academic_status)->toBe('DRAFT')
        ->and($updated->translations)->toHaveCount(1)
        ->and($updated->translations->first()->title)->toBe('Judul Abstrak')
        ->and($updated->translations->first()->subtitle)->toBeNull()
        ->and($updated->translations->first()->abstract_text)->toBe('Isi abstrak.')
        ->and($updated->keywords->pluck('value')->all())->toBe(['pendidikan', 'sosial'])
        ->and($updated->keywords->pluck('sequence')->all())->toBe([1, 2]);

    $activity = DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_details_updated')
        ->first();

    $properties = json_decode((string) $activity?->properties, true);

    expect($activity)->not->toBeNull()
        ->and($activity->subject_id)->toBe($submission->id)
        ->and($activity->causer_id)->toBe($user->id)
        ->and($properties['conference_edition_id'])->toBe($edition->id)
        ->and($properties['registration_id'])->toBe($registration->id)
        ->and($properties['paper_code'])->toBe('ICHES27-P-0001')
        ->and($properties['primary_locale'])->toBe('id')
        ->and($properties['track_id'])->toBeNull()
        ->and($properties['scholarly_locales'])->toBe(['id'])
        ->and($properties['keyword_counts'])->toBe(['id' => 2])
        ->and(json_encode($properties))->not->toContain('Judul Abstrak')
        ->and(json_encode($properties))->not->toContain('Isi abstrak');
});

test('optional secondary locale translations and keywords are persisted', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'translations' => [
            'id' => [
                'title' => 'Judul Abstrak',
                'abstract' => 'Isi abstrak.',
            ],
            'ar' => [
                'title' => 'عنوان الملخص',
                'abstract' => 'ملخص البحث.',
            ],
        ],
        'keywords' => [
            'id' => ['pendidikan'],
            'ar' => ['تعليم'],
        ],
    ]));

    expect($updated->translations)->toHaveCount(2)
        ->and($updated->translations->first(fn ($translation) => $translation->locale === Locale::Arabic)->title)->toBe('عنوان الملخص')
        ->and($updated->translations->first(fn ($translation) => $translation->locale === Locale::Arabic)->subtitle)->toBeNull()
        ->and($updated->keywords)->toHaveCount(2)
        ->and($updated->keywords->first(fn ($keyword) => $keyword->locale === Locale::Arabic)->value)->toBe('تعليم')
        ->and($updated->keywords->first(fn ($keyword) => $keyword->locale === Locale::Arabic)->sequence)->toBe(1);
});

test('primary scholarly locale can change and the previous primary translation remains a secondary translation', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'primary_locale' => 'en',
        'translations' => [
            'id' => [
                'title' => 'Judul Abstrak',
                'abstract' => 'Isi abstrak.',
            ],
            'en' => [
                'title' => 'Abstract Title',
                'abstract' => 'Abstract body.',
            ],
        ],
        'keywords' => [
            'en' => ['education'],
        ],
    ]));

    expect($updated->primary_locale)->toBe(Locale::English)
        ->and($updated->primaryTranslation->title)->toBe('Abstract Title')
        ->and($updated->translations)->toHaveCount(2)
        ->and($updated->translations->first(fn ($translation) => $translation->locale === Locale::Indonesian)->title)->toBe('Judul Abstrak')
        ->and($updated->keywords)->toHaveCount(1)
        ->and($updated->keywords->first()->value)->toBe('education');

    $properties = json_decode((string) DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_details_updated')
        ->first()
        ->properties, true);

    expect($properties['primary_locale'])->toBe('en')
        ->and($properties['scholarly_locales'])->toBe(['id', 'en'])
        ->and($properties['keyword_counts'])->toBe(['en' => 1]);
});

test('an explicitly null track clears the submission track', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user] = detailsMutationParticipation();

    $track = detailsMutationTrack($edition);

    $submission = detailsMutationSubmission($edition, $registration, ['track_id' => $track->id]);

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'track_id' => null,
    ]));

    expect($updated->track_id)->toBeNull()
        ->and($updated->fresh()->track_id)->toBeNull();
});

test('an active track from the same conference edition is accepted', function () {
    ['edition' => $edition, 'user' => $user, 'submission' => $submission] = detailsMutationContext();

    $track = detailsMutationTrack($edition);

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'track_id' => $track->id,
    ]));

    expect($updated->track_id)->toBe($track->id)
        ->and($updated->track->is($track))->toBeTrue();
});

test('a track from another conference edition is rejected without partial mutation', function () {
    ['edition' => $edition, 'user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $otherEdition = detailsMutationEdition('OTHER27');
    $foreignTrack = detailsMutationTrack($otherEdition);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'track_id' => $foreignTrack->id,
    ])))->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('an inactive track is rejected without partial mutation', function () {
    ['edition' => $edition, 'user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $inactiveTrack = detailsMutationTrack($edition, active: false);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'track_id' => $inactiveTrack->id,
    ])))->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('a nonexistent track is rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'track_id' => Str::uuid()->toString(),
    ])))->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('unsupported scholarly locales are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $action = app(UpdateDraftSubmissionDetailsAction::class);

    expect(fn () => $action->handle($user, $submission, detailsMutationPayload([
        'translations' => [
            'id' => ['title' => 'Judul', 'abstract' => 'Abstrak'],
            'fr' => ['title' => 'Titre', 'abstract' => 'Résumé'],
        ],
    ])))->toThrow(DomainException::class)
        ->and(fn () => $action->handle($user, $submission, detailsMutationPayload([
            'keywords' => [
                'id' => ['pendidikan'],
                'fr' => ['éducation'],
            ],
        ])))->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('malformed details payload shapes are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $action = app(UpdateDraftSubmissionDetailsAction::class);

    $malformedPayloads = [
        ['primary_locale' => 42],
        ['primary_locale' => 'fr'],
        ['track_id' => 123],
        ['translations' => 'nope'],
        ['translations' => [['title' => 'Judul', 'abstract' => 'Abstrak']]],
        ['translations' => ['id' => 'nope']],
        ['translations' => ['id' => ['abstract' => 'Abstrak']]],
        ['translations' => ['id' => ['title' => 'Judul']]],
        ['translations' => ['id' => ['title' => ['Judul'], 'abstract' => 'Abstrak']]],
        ['translations' => ['id' => ['title' => 'Judul', 'subtitle' => 7, 'abstract' => 'Abstrak']]],
        ['translations' => ['id' => ['title' => 'Judul', 'abstract' => 'Abstrak'], 'en' => ['title' => '  ', 'abstract' => 'Body']]],
        ['keywords' => 'nope'],
        ['keywords' => ['id' => 'nope']],
        ['keywords' => ['id' => ['pendidikan', 7]]],
    ];

    foreach ($malformedPayloads as $overrides) {
        expect(fn () => $action->handle($user, $submission, detailsMutationPayload($overrides)))
            ->toThrow(DomainException::class);
    }

    detailsMutationAssertUnchanged($submission);
});

test('missing required top-level details payload keys are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $action = app(UpdateDraftSubmissionDetailsAction::class);

    foreach (['primary_locale', 'track_id', 'translations', 'keywords'] as $missingKey) {
        $payload = detailsMutationPayload();
        unset($payload[$missingKey]);

        expect(fn () => $action->handle($user, $submission, $payload))
            ->toThrow(DomainException::class);
    }

    detailsMutationAssertUnchanged($submission);
});

test('a payload without the primary locale translation is rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'translations' => [
            'en' => ['title' => 'Abstract Title', 'abstract' => 'Abstract body.'],
        ],
    ])))->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('blank primary title and abstract values are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $action = app(UpdateDraftSubmissionDetailsAction::class);

    expect(fn () => $action->handle($user, $submission, detailsMutationPayload([
        'translations' => ['id' => ['title' => '   ', 'abstract' => 'Abstrak']],
    ])))->toThrow(DomainException::class)
        ->and(fn () => $action->handle($user, $submission, detailsMutationPayload([
            'translations' => ['id' => ['title' => 'Judul', 'abstract' => "\n\t  "]],
        ])))->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('a blank supplied keyword is rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'keywords' => ['id' => ['pendidikan', '   ']],
    ])))->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('replacement removes translations and keywords for omitted locales', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    $submission->translations()->create([
        'locale' => Locale::English->value,
        'title' => 'Existing English Title',
        'abstract_text' => 'Existing English abstract.',
    ]);

    $submission->keywords()->create([
        'locale' => Locale::English->value,
        'value' => 'english',
        'sequence' => 1,
    ]);

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'translations' => [
            'id' => ['title' => 'Judul Abstrak', 'abstract' => 'Isi abstrak.'],
        ],
        'keywords' => [
            'id' => ['pendidikan'],
        ],
    ]));

    expect($updated->translations)->toHaveCount(1)
        ->and($updated->translations->first()->locale)->toBe(Locale::Indonesian)
        ->and($updated->keywords)->toHaveCount(1)
        ->and($updated->keywords->first()->locale)->toBe(Locale::Indonesian)
        ->and($updated->keywords->first()->value)->toBe('pendidikan');
});

test('a non-owner actor cannot mutate draft submission details', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $stranger = User::factory()->create();

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($stranger, $submission, detailsMutationPayload()))
        ->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('a registration with a package from another conference edition is rejected without partial mutation', function () {
    $edition = detailsMutationEdition();
    $otherEdition = detailsMutationEdition('OTHER27');
    $foreignPackage = detailsMutationPackage($otherEdition);

    ['user' => $user, 'registration' => $registration] = detailsMutationRegistration($edition, $foreignPackage);

    $submission = detailsMutationSubmission($edition, $registration);

    detailsMutationSeedExistingDetails($submission);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload()))
        ->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('a cancelled registration is rejected without partial mutation', function () {
    $edition = detailsMutationEdition();
    $package = detailsMutationPackage($edition);

    ['user' => $user, 'registration' => $registration] = detailsMutationRegistration($edition, $package, 'CANCELLED');

    $submission = detailsMutationSubmission($edition, $registration);

    detailsMutationSeedExistingDetails($submission);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload()))
        ->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('pending payment and confirmed registration states do not gate details editing', function () {
    ['registration' => $registration, 'user' => $user, 'submission' => $submission] = detailsMutationContext();

    $action = app(UpdateDraftSubmissionDetailsAction::class);

    foreach (['PENDING', 'PAYMENT_PENDING', 'CONFIRMED'] as $status) {
        Registration::query()
            ->whereKey($registration->id)
            ->update(['status' => $status]);

        $updated = $action->handle($user, $submission, detailsMutationPayload([
            'translations' => [
                'id' => ['title' => "Judul {$status}", 'abstract' => "Abstrak {$status}."],
            ],
        ]));

        expect($updated->translations->first()->title)->toBe("Judul {$status}")
            ->and($updated->academic_status)->toBe('DRAFT')
            ->and($updated->registration->status->value)->toBe($status);
    }

    expect(Payment::query()->count())->toBe(0)
        ->and(DB::table('activity_log')
            ->where('log_name', 'submission')
            ->where('event', 'submission_details_updated')
            ->count())->toBe(3);
});

test('a non draft submission cannot be mutated', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext([
        'academic_status' => 'SUBMITTED',
        'submitted_at' => now(),
    ]);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload()))
        ->toThrow(DomainException::class);

    $fresh = $submission->fresh();

    expect($fresh->academic_status)->toBe('SUBMITTED')
        ->and($fresh->translations)->toHaveCount(0)
        ->and($fresh->keywords)->toHaveCount(0);
});

test('a stale caller draft model cannot bypass the authoritative non draft guard', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    detailsMutationSeedExistingDetails($submission);

    $staleSubmission = Submission::query()->findOrFail($submission->id);

    expect($staleSubmission->academic_status)->toBe('DRAFT');

    Submission::query()
        ->whereKey($submission->id)
        ->update(['academic_status' => 'SUBMITTED']);

    expect($staleSubmission->academic_status)->toBe('DRAFT');

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $staleSubmission, detailsMutationPayload()))
        ->toThrow(DomainException::class);

    $fresh = $submission->fresh();

    expect($fresh->academic_status)->toBe('SUBMITTED')
        ->and($fresh->primary_locale)->toBe(Locale::Indonesian)
        ->and($fresh->translations)->toHaveCount(1)
        ->and($fresh->translations->first()->title)->toBe('Existing Title')
        ->and($fresh->keywords)->toHaveCount(1)
        ->and($fresh->keywords->first()->value)->toBe('existing')
        ->and(DB::table('activity_log')
            ->where('log_name', 'submission')
            ->where('event', 'submission_details_updated')
            ->count())->toBe(0);
});

test('paper identity edition and registration relationships remain immutable', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user, 'submission' => $submission] = detailsMutationContext();

    $before = [
        'id' => $submission->id,
        'paper_code' => $submission->paper_code,
        'edition_id' => $submission->edition_id,
        'registration_id' => $submission->registration_id,
        'current_abstract_version' => $submission->current_abstract_version,
    ];

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'primary_locale' => 'en',
        'translations' => [
            'en' => ['title' => 'Abstract Title', 'abstract' => 'Abstract body.'],
        ],
        'keywords' => [
            'en' => ['education'],
        ],
    ]));

    expect($updated->id)->toBe($before['id'])
        ->and(Str::isUuid($updated->id, 7))->toBeTrue()
        ->and($updated->paper_code)->toBe($before['paper_code'])
        ->and($updated->edition_id)->toBe($before['edition_id'])
        ->and($updated->edition->is($edition))->toBeTrue()
        ->and($updated->registration_id)->toBe($before['registration_id'])
        ->and($updated->registration->is($registration))->toBeTrue()
        ->and($updated->current_abstract_version)->toBe($before['current_abstract_version'])
        ->and($updated->academic_status)->toBe('DRAFT')
        ->and($updated->primary_locale)->toBe(Locale::English)
        ->and(Submission::query()->count())->toBe(1);
});

test('an inactive historical participation package does not block draft details editing', function () {
    ['package' => $package, 'user' => $user, 'submission' => $submission] = detailsMutationContext();

    ParticipationPackage::query()
        ->whereKey($package->id)
        ->update(['active' => false]);

    expect($package->fresh()->active)->toBeFalse();

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'translations' => [
            'id' => ['title' => 'Judul Setelah Paket Nonaktif', 'abstract' => 'Isi abstrak setelah paket nonaktif.'],
        ],
    ]));

    expect($updated->academic_status)->toBe('DRAFT')
        ->and($updated->translations)->toHaveCount(1)
        ->and($updated->translations->first()->title)->toBe('Judul Setelah Paket Nonaktif')
        ->and($updated->registration->package->id)->toBe($package->id)
        ->and($updated->registration->package->edition_id)->toBe($updated->edition_id)
        ->and($updated->registration->package->active)->toBeFalse()
        ->and(Payment::query()->count())->toBe(0);
});

test('keyword replacement normalizes input indexes and permits an empty list', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    $action = app(UpdateDraftSubmissionDetailsAction::class);

    $updated = $action->handle($user, $submission, detailsMutationPayload([
        'keywords' => [
            'id' => [4 => ' pendidikan ', 9 => ' sosial '],
        ],
    ]));

    $storedKeywords = $updated->keywords()->orderBy('sequence')->get();

    expect($storedKeywords)->toHaveCount(2)
        ->and($storedKeywords->pluck('sequence')->all())->toBe([1, 2])
        ->and($storedKeywords->pluck('value')->all())->toBe(['pendidikan', 'sosial'])
        ->and($storedKeywords->pluck('locale')->map(fn (Locale $locale) => $locale->value)->all())
        ->toBe(['id', 'id']);

    $emptied = $action->handle($user, $updated, detailsMutationPayload([
        'keywords' => ['id' => []],
    ]));

    expect($emptied->keywords()->count())->toBe(0)
        ->and(Submission::query()->find($submission->id)->keywords()->count())->toBe(0)
        ->and($emptied->translations)->toHaveCount(1)
        ->and($emptied->translations->first()->title)->toBe('Judul Abstrak');
});

test('a membership belonging to another conference edition is rejected without partial mutation', function () {
    $editionA = detailsMutationEdition('ICHES27');
    $editionB = detailsMutationEdition('OTHER27');
    $package = detailsMutationPackage($editionA);

    ['user' => $user, 'registration' => $registration] = detailsMutationRegistration($editionB, $package);

    $submission = detailsMutationSubmission($editionA, $registration);

    expect($submission->edition_id)->toBe($editionA->id)
        ->and($registration->membership->edition_id)->toBe($editionB->id);

    detailsMutationSeedExistingDetails($submission);

    expect(fn () => app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload()))
        ->toThrow(DomainException::class);

    detailsMutationAssertUnchanged($submission);
});

test('keyword locales do not require a matching scholarly translation locale', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'primary_locale' => 'id',
        'translations' => [
            'id' => ['title' => 'Judul Abstrak', 'abstract' => 'Isi abstrak.'],
        ],
        'keywords' => [
            'id' => ['pendidikan'],
            'en' => ['education-first', 'education-second'],
        ],
    ]));

    $englishKeywords = $updated->keywords()
        ->where('locale', Locale::English->value)
        ->orderBy('sequence')
        ->get();

    expect($updated->primary_locale)->toBe(Locale::Indonesian)
        ->and($updated->translations)->toHaveCount(1)
        ->and($updated->translations->first()->locale)->toBe(Locale::Indonesian)
        ->and($updated->keywords)->toHaveCount(3)
        ->and($englishKeywords)->toHaveCount(2)
        ->and($englishKeywords->pluck('sequence')->all())->toBe([1, 2])
        ->and($englishKeywords->pluck('value')->all())->toBe(['education-first', 'education-second']);
});

test('audit metadata never contains keyword text', function () {
    ['user' => $user, 'submission' => $submission] = detailsMutationContext();

    $updated = app(UpdateDraftSubmissionDetailsAction::class)->handle($user, $submission, detailsMutationPayload([
        'keywords' => [
            'id' => ['SECRET-KEYWORD-ALPHA', 'SECRET-KEYWORD-BETA'],
        ],
    ]));

    expect($updated->keywords()->count())->toBe(2);

    $activity = DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_details_updated')
        ->first();

    $serializedProperties = (string) $activity?->properties;
    $properties = json_decode($serializedProperties, true);

    expect($activity)->not->toBeNull()
        ->and($serializedProperties)->not->toContain('SECRET-KEYWORD-ALPHA')
        ->and($serializedProperties)->not->toContain('SECRET-KEYWORD-BETA')
        ->and($properties['keyword_counts'])->toBe(['id' => 2])
        ->and($properties['scholarly_locales'])->toBe(['id']);
});
