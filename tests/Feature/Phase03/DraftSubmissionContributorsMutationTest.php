<?php

use App\Actions\Submission\UpdateDraftSubmissionContributorsAction;
use App\Enums\BillingMode;
use App\Enums\Locale;
use App\Enums\OrcidVerificationState;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\ContributorAffiliation;
use App\Models\EditionMembership;
use App\Models\Institution;
use App\Models\ParticipationPackage;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function contributorsMutationEdition(string $editionCode = 'ICHES27'): ConferenceEdition
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

function contributorsMutationPackage(ConferenceEdition $edition, bool $active = true): ParticipationPackage
{
    return ParticipationPackage::query()->create([
        'edition_id' => $edition->id,
        'code' => 'PRESENTER',
        'name_i18n' => ['id' => 'Presenter', 'en' => 'Presenter'],
        'billing_mode' => BillingMode::PAID->value,
        'price' => '750000.00',
        'currency_code' => 'IDR',
        'active' => $active,
        'display_order' => 1,
    ]);
}

function contributorsMutationRegistration(
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

function contributorsMutationSubmission(
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

function contributorsMutationContext(array $submissionOverrides = []): array
{
    $edition = contributorsMutationEdition();
    $package = contributorsMutationPackage($edition);

    ['user' => $user, 'membership' => $membership, 'registration' => $registration] = contributorsMutationRegistration(
        $edition,
        $package,
    );

    $submission = contributorsMutationSubmission($edition, $registration, $submissionOverrides);

    return compact('edition', 'package', 'user', 'membership', 'registration', 'submission');
}

function contributorsMutationInstitution(array $overrides = []): Institution
{
    return Institution::query()->create(array_merge([
        'preferred_name' => 'Universitas Islam Syarifuddin Lumajang',
        'ror_uri' => 'https://ror.org/01cab9d94',
        'country_code' => 'ID',
        'website' => null,
        'aliases_json' => null,
        'source' => 'MANUAL',
    ], $overrides));
}

function contributorsMutationAffiliation(array $overrides = []): array
{
    return array_merge([
        'institution_id' => null,
        'institution_name' => 'Universitas Indonesia',
        'subdivision' => null,
        'city' => 'Depok',
        'country_code' => 'id',
    ], $overrides);
}

function contributorsMutationContributor(array $overrides = []): array
{
    return array_merge([
        'id' => null,
        'display_name' => 'Budi Santoso',
        'given_name' => 'Budi',
        'family_name' => 'Santoso',
        'single_name' => false,
        'email' => 'budi@example.com',
        'country_code' => 'id',
        'orcid' => null,
        'is_corresponding' => false,
        'affiliations' => [contributorsMutationAffiliation()],
    ], $overrides);
}

function contributorsMutationPayload(array $contributors): array
{
    return ['contributors' => $contributors];
}

function contributorsMutationSeedContributor(Submission $submission, array $overrides = []): SubmissionContributor
{
    return $submission->contributors()->create(array_merge([
        'linked_user_id' => null,
        'display_name' => 'Existing Author',
        'given_name' => 'Existing',
        'family_name' => 'Author',
        'single_name' => false,
        'email' => 'existing@example.com',
        'country_code' => 'ID',
        'orcid_uri' => null,
        'orcid_verification_state' => null,
        'is_corresponding' => false,
        'sequence' => 1,
        'contributor_role' => 'AUTHOR',
    ], $overrides));
}

function contributorsMutationSeedAffiliation(
    SubmissionContributor $contributor,
    array $overrides = [],
): ContributorAffiliation {
    return $contributor->affiliations()->create(array_merge([
        'institution_id' => null,
        'institution_name_snapshot' => 'Existing Institution',
        'ror_uri_snapshot' => null,
        'subdivision_text' => null,
        'country_code' => 'ID',
        'city_text' => null,
        'sequence' => 1,
    ], $overrides));
}

function contributorsMutationSnapshot(Submission $submission): array
{
    $contributors = SubmissionContributor::query()
        ->where('submission_id', $submission->id)
        ->orderBy('sequence')
        ->get()
        ->map(fn (SubmissionContributor $contributor): array => [
            'id' => $contributor->id,
            'linked_user_id' => $contributor->linked_user_id,
            'display_name' => $contributor->display_name,
            'given_name' => $contributor->given_name,
            'family_name' => $contributor->family_name,
            'single_name' => $contributor->single_name,
            'email' => $contributor->email,
            'country_code' => $contributor->country_code,
            'orcid_uri' => $contributor->orcid_uri,
            'orcid_verification_state' => $contributor->orcid_verification_state,
            'is_corresponding' => $contributor->is_corresponding,
            'sequence' => $contributor->sequence,
            'contributor_role' => $contributor->contributor_role,
            'affiliations' => $contributor->affiliations()
                ->get()
                ->map(fn (ContributorAffiliation $affiliation): array => [
                    'id' => $affiliation->id,
                    'institution_id' => $affiliation->institution_id,
                    'institution_name_snapshot' => $affiliation->institution_name_snapshot,
                    'ror_uri_snapshot' => $affiliation->ror_uri_snapshot,
                    'subdivision_text' => $affiliation->subdivision_text,
                    'country_code' => $affiliation->country_code,
                    'city_text' => $affiliation->city_text,
                    'sequence' => $affiliation->sequence,
                ])
                ->all(),
        ])
        ->all();

    return [
        'contributors' => $contributors,
        'activity_count' => contributorsMutationActivityCount(),
    ];
}

function contributorsMutationActivityCount(): int
{
    return DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_contributors_updated')
        ->count();
}

function contributorsMutationActivityProperties(): ?array
{
    $activity = DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_contributors_updated')
        ->orderByDesc('id')
        ->first();

    return json_decode((string) $activity?->properties, true);
}

test('a complete contributor payload persists order identity affiliations and one audit event', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'display_name' => 'Siti Aminah',
            'given_name' => 'Siti',
            'family_name' => 'Aminah',
            'is_corresponding' => true,
        ]),
        contributorsMutationContributor([
            'display_name' => 'Ahmad Fauzi',
            'given_name' => 'Ahmad',
            'family_name' => 'Fauzi',
            'email' => 'ahmad@example.com',
            'affiliations' => [],
        ]),
    ]));

    $contributors = $updated->contributors;

    expect($updated->academic_status)->toBe('DRAFT')
        ->and($contributors)->toHaveCount(2)
        ->and($contributors->pluck('sequence')->all())->toBe([1, 2])
        ->and($contributors->pluck('display_name')->all())->toBe(['Siti Aminah', 'Ahmad Fauzi'])
        ->and(Str::isUuid($contributors[0]->id, 7))->toBeTrue()
        ->and(Str::isUuid($contributors[1]->id, 7))->toBeTrue()
        ->and($contributors->pluck('linked_user_id')->all())->toBe([null, null])
        ->and($contributors->pluck('contributor_role')->unique()->all())->toBe(['AUTHOR'])
        ->and($contributors[0]->country_code)->toBe('ID')
        ->and($contributors[0]->is_corresponding)->toBeTrue()
        ->and($contributors[1]->is_corresponding)->toBeFalse()
        ->and($contributors[0]->orcid_uri)->toBeNull()
        ->and($contributors[0]->orcid_verification_state)->toBeNull()
        ->and($contributors[0]->affiliations)->toHaveCount(1)
        ->and($contributors[0]->affiliations[0]->sequence)->toBe(1)
        ->and($contributors[0]->affiliations[0]->country_code)->toBe('ID')
        ->and($contributors[1]->affiliations)->toHaveCount(0);

    $activity = DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_contributors_updated')
        ->first();

    $properties = contributorsMutationActivityProperties();

    expect($activity)->not->toBeNull()
        ->and($activity->subject_id)->toBe($submission->id)
        ->and($activity->causer_id)->toBe($user->id)
        ->and($properties['conference_edition_id'])->toBe($edition->id)
        ->and($properties['registration_id'])->toBe($registration->id)
        ->and($properties['paper_code'])->toBe('ICHES27-P-0001')
        ->and($properties['contributor_count'])->toBe(2)
        ->and($properties['contributor_ids'])->toBe([$contributors[0]->id, $contributors[1]->id])
        ->and($properties['corresponding_count'])->toBe(1)
        ->and($properties['affiliation_counts'])->toBe([1, 0])
        ->and(contributorsMutationActivityCount())->toBe(1);
});

test('retained contributor identifiers survive edits and reordering while new contributors are added', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $first = contributorsMutationSeedContributor($submission, [
        'display_name' => 'First Author',
        'given_name' => 'First',
        'family_name' => 'Author',
        'sequence' => 1,
    ]);

    $second = contributorsMutationSeedContributor($submission, [
        'display_name' => 'Second Author',
        'given_name' => 'Second',
        'family_name' => 'Author',
        'email' => 'second@example.com',
        'sequence' => 2,
    ]);

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'id' => $second->id,
            'display_name' => 'Second Author Edited',
        ]),
        contributorsMutationContributor([
            'id' => $first->id,
            'display_name' => 'First Author Edited',
        ]),
        contributorsMutationContributor([
            'display_name' => 'Third Author',
            'given_name' => 'Third',
            'family_name' => 'Author',
            'email' => 'third@example.com',
        ]),
    ]));

    $contributors = $updated->contributors;

    expect($contributors)->toHaveCount(3)
        ->and($contributors->pluck('id')->all())->toBe([$second->id, $first->id, $contributors[2]->id])
        ->and($contributors->pluck('sequence')->all())->toBe([1, 2, 3])
        ->and($contributors->pluck('display_name')->all())
        ->toBe(['Second Author Edited', 'First Author Edited', 'Third Author'])
        ->and($contributors[2]->id)->not->toBe($first->id)
        ->and(Str::isUuid($contributors[2]->id, 7))->toBeTrue()
        ->and(SubmissionContributor::query()->where('submission_id', $submission->id)->count())->toBe(3);
});

test('contributors omitted from the complete payload are removed only after validation succeeds', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $retained = contributorsMutationSeedContributor($submission, [
        'display_name' => 'Retained Author',
        'sequence' => 1,
    ]);

    $omitted = contributorsMutationSeedContributor($submission, [
        'display_name' => 'Omitted Author',
        'email' => 'omitted@example.com',
        'sequence' => 2,
    ]);

    contributorsMutationSeedAffiliation($omitted, ['institution_name_snapshot' => 'Omitted Institution']);

    $before = contributorsMutationSnapshot($submission);

    $action = app(UpdateDraftSubmissionContributorsAction::class);

    expect(fn () => $action->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'id' => $retained->id,
            'display_name' => 'Retained Author Edited',
        ]),
        contributorsMutationContributor([
            'affiliations' => [
                contributorsMutationAffiliation(['institution_id' => Str::uuid()->toString()]),
            ],
        ]),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);

    $updated = $action->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'id' => $retained->id,
            'display_name' => 'Retained Author Edited',
        ]),
    ]));

    expect($updated->contributors)->toHaveCount(1)
        ->and($updated->contributors->first()->id)->toBe($retained->id)
        ->and($updated->contributors->first()->display_name)->toBe('Retained Author Edited')
        ->and(SubmissionContributor::query()->whereKey($omitted->id)->exists())->toBeFalse()
        ->and(ContributorAffiliation::query()->count())->toBe(1)
        ->and(contributorsMutationActivityCount())->toBe(1);
});

test('a contributor identifier from another submission is rejected atomically', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $own = contributorsMutationSeedContributor($submission, ['display_name' => 'Own Author']);
    contributorsMutationSeedAffiliation($own);

    $otherSubmission = contributorsMutationSubmission($edition, $registration, [
        'paper_code' => 'ICHES27-P-0002',
    ]);

    $foreign = contributorsMutationSeedContributor($otherSubmission, [
        'display_name' => 'Foreign Author',
        'email' => 'foreign@example.com',
    ]);

    $before = contributorsMutationSnapshot($submission);
    $foreignBefore = contributorsMutationSnapshot($otherSubmission);

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'id' => $own->id,
            'display_name' => 'Own Author Edited',
        ]),
        contributorsMutationContributor([
            'id' => $foreign->id,
            'display_name' => 'Foreign Author Edited',
        ]),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before)
        ->and(contributorsMutationSnapshot($otherSubmission))->toEqual($foreignBefore);
});

test('duplicate contributor identifiers in one payload are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $existing = contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['id' => $existing->id, 'display_name' => 'Renamed Once']),
        contributorsMutationContributor(['id' => $existing->id, 'display_name' => 'Renamed Twice']),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('mononym contributors are preserved without fabricated names', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'display_name' => 'Sukarno',
            'given_name' => null,
            'family_name' => null,
            'single_name' => true,
            'email' => 'sukarno@example.com',
            'affiliations' => [],
        ]),
        contributorsMutationContributor([
            'display_name' => 'Megawati',
            'given_name' => null,
            'family_name' => null,
            'single_name' => true,
            'email' => 'megawati@example.com',
            'affiliations' => [],
        ]),
    ]));

    $contributors = $updated->contributors;

    expect($contributors)->toHaveCount(2)
        ->and($contributors[0]->single_name)->toBeTrue()
        ->and($contributors[0]->display_name)->toBe('Sukarno')
        ->and($contributors[0]->given_name)->toBeNull()
        ->and($contributors[0]->family_name)->toBeNull()
        ->and($contributors[1]->single_name)->toBeTrue()
        ->and($contributors[1]->given_name)->toBeNull()
        ->and($contributors[1]->family_name)->toBeNull();
});

test('incomplete or contradictory name payloads are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionContributorsAction::class);

    $withoutBothNames = contributorsMutationContributor([
        'given_name' => null,
        'family_name' => null,
    ]);

    $missingGivenKey = contributorsMutationContributor();
    unset($missingGivenKey['given_name'], $missingGivenKey['family_name']);

    $contradictoryMononym = contributorsMutationContributor(['single_name' => true]);

    $blankDisplayName = contributorsMutationContributor(['display_name' => '   ']);

    $missingDisplayName = contributorsMutationContributor();
    unset($missingDisplayName['display_name']);

    $nonStringGivenName = contributorsMutationContributor(['given_name' => 42]);

    foreach ([
        ['contributors' => [$withoutBothNames]],
        ['contributors' => [$missingGivenKey]],
        ['contributors' => [$contradictoryMononym]],
        ['contributors' => [$blankDisplayName]],
        ['contributors' => [$missingDisplayName]],
        ['contributors' => [$nonStringGivenName]],
    ] as $payload) {
        expect(fn () => $action->handle($user, $submission, $payload))->toThrow(DomainException::class);
    }

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('blank and invalid emails are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionContributorsAction::class);

    foreach (['   ', 'not-an-email', 123] as $email) {
        expect(fn () => $action->handle($user, $submission, contributorsMutationPayload([
            contributorsMutationContributor(['email' => $email]),
        ])))->toThrow(DomainException::class);
    }

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('country codes are normalized to the uppercase two-letter form and invalid codes are rejected', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $action = app(UpdateDraftSubmissionContributorsAction::class);

    $updated = $action->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'country_code' => ' id ',
            'affiliations' => [contributorsMutationAffiliation(['country_code' => 'sg'])],
        ]),
    ]));

    $contributor = $updated->contributors->first();

    expect($contributor->country_code)->toBe('ID')
        ->and($contributor->affiliations->first()->country_code)->toBe('SG');

    $before = contributorsMutationSnapshot($submission);

    foreach (['IDN', 'I', '1D', ''] as $countryCode) {
        expect(fn () => $action->handle($user, $submission, contributorsMutationPayload([
            contributorsMutationContributor(['country_code' => $countryCode]),
        ])))->toThrow(DomainException::class);
    }

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('an absent orcid persists a null uri and a null verification state', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['orcid' => null]),
        contributorsMutationContributor(['orcid' => '   ', 'email' => 'second@example.com']),
    ]));

    expect($updated->contributors->pluck('orcid_uri')->all())->toBe([null, null])
        ->and($updated->contributors->pluck('orcid_verification_state')->all())->toBe([null, null]);
});

test('a manually entered orcid is normalized to the canonical uri with an unverified state', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['orcid' => ' 0000-0002-1825-0097 ']),
        contributorsMutationContributor([
            'orcid' => 'https://orcid.org/0000-0002-1825-0097/',
            'email' => 'second@example.com',
        ]),
        contributorsMutationContributor([
            'orcid' => '0000000218250097',
            'email' => 'third@example.com',
        ]),
    ]));

    $contributors = $updated->contributors;

    expect($contributors->pluck('orcid_uri')->all())->toBe([
        'https://orcid.org/0000-0002-1825-0097',
        'https://orcid.org/0000-0002-1825-0097',
        'https://orcid.org/0000-0002-1825-0097',
    ])
        ->and($contributors->pluck('orcid_verification_state')->unique()->all())
        ->toBe([OrcidVerificationState::UNVERIFIED->value])
        ->and(json_encode($contributors->pluck('orcid_verification_state')->all()))
        ->not->toContain(OrcidVerificationState::AUTHENTICATED->value);
});

test('an invalid orcid is rejected atomically before any destructive write', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $existing = contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);
    contributorsMutationSeedAffiliation($existing);

    $before = contributorsMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionContributorsAction::class);

    foreach ([
        '0000-0002-1825-0098',
        'not-an-orcid',
        '0000-0002-1825-009',
        'https://orcid.org/0000-0000-0000-0000',
        'https://orcid.org/0000-0002-1825-0097/extra',
        'https://orcid.org/0000-0002-1825-0097?foo=bar',
        'https://orcid.org/0000-0002-1825-0097#profile',
    ] as $orcid) {
        expect(fn () => $action->handle($user, $submission, contributorsMutationPayload([
            contributorsMutationContributor(['orcid' => $orcid]),
        ])))->toThrow(DomainException::class);
    }

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('corresponding author flags are stored without official submit readiness enforcement', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $action = app(UpdateDraftSubmissionContributorsAction::class);

    $withoutCorresponding = $action->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['is_corresponding' => false]),
        contributorsMutationContributor(['is_corresponding' => false, 'email' => 'second@example.com']),
    ]));

    expect($withoutCorresponding->contributors->pluck('is_corresponding')->all())->toBe([false, false])
        ->and($withoutCorresponding->correspondingAuthor()->exists())->toBeFalse()
        ->and(contributorsMutationActivityProperties()['corresponding_count'])->toBe(0);

    $withCorresponding = $action->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['is_corresponding' => true]),
        contributorsMutationContributor(['is_corresponding' => false, 'email' => 'second@example.com']),
    ]));

    expect($withCorresponding->correspondingAuthor()->exists())->toBeTrue()
        ->and(contributorsMutationActivityProperties()['corresponding_count'])->toBe(1);

    $before = contributorsMutationSnapshot($submission);

    expect(fn () => $action->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['is_corresponding' => true]),
        contributorsMutationContributor(['is_corresponding' => true, 'email' => 'second@example.com']),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('multiple affiliations are ordered and institution backed snapshots come from the institution record', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $institution = contributorsMutationInstitution();

    $institutionBackedAffiliation = contributorsMutationAffiliation([
        'institution_id' => $institution->id,
        'institution_name' => 'Client Supplied Wrong Name',
        'subdivision' => 'Fakultas Pendidikan',
        'city' => 'Lumajang',
        'country_code' => 'sg',
    ]);

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'affiliations' => [
                $institutionBackedAffiliation,
                contributorsMutationAffiliation([
                    'institution_name' => 'Independent Research Lab',
                    'subdivision' => null,
                    'city' => null,
                    'country_code' => 'sg',
                ]),
            ],
        ]),
    ]));

    $affiliations = $updated->contributors->first()->affiliations;

    expect($affiliations)->toHaveCount(2)
        ->and($affiliations->pluck('sequence')->all())->toBe([1, 2])
        ->and($affiliations[0]->institution_id)->toBe($institution->id)
        ->and($affiliations[0]->institution_name_snapshot)->toBe('Universitas Islam Syarifuddin Lumajang')
        ->and($affiliations[0]->institution_name_snapshot)->not->toBe('Client Supplied Wrong Name')
        ->and($affiliations[0]->ror_uri_snapshot)->toBe('https://ror.org/01cab9d94')
        ->and($affiliations[0]->subdivision_text)->toBe('Fakultas Pendidikan')
        ->and($affiliations[0]->city_text)->toBe('Lumajang')
        ->and($affiliations[0]->country_code)->toBe('ID')
        ->and($affiliations[1]->institution_id)->toBeNull()
        ->and($affiliations[1]->institution_name_snapshot)->toBe('Independent Research Lab')
        ->and($affiliations[1]->ror_uri_snapshot)->toBeNull()
        ->and($affiliations[1]->country_code)->toBe('SG');
});

test('an invalid institution is rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionContributorsAction::class);

    expect(fn () => $action->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'affiliations' => [
                contributorsMutationAffiliation(['institution_id' => Str::uuid()->toString()]),
            ],
        ]),
    ])))->toThrow(DomainException::class)
        ->and(fn () => $action->handle($user, $submission, contributorsMutationPayload([
            contributorsMutationContributor([
                'affiliations' => [
                    contributorsMutationAffiliation(['institution_id' => 42]),
                ],
            ]),
        ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('server owned and unsupported payload fields are rejected instead of silently ignored', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionContributorsAction::class);

    $contributorOwnedFields = [
        'submission_id' => $submission->id,
        'linked_user_id' => null,
        'sequence' => 7,
        'contributor_role' => 'REVIEWER',
        'orcid_verification_state' => 'AUTHENTICATED',
    ];

    foreach ($contributorOwnedFields as $field => $value) {
        expect(fn () => $action->handle($user, $submission, contributorsMutationPayload([
            contributorsMutationContributor([$field => $value]),
        ])))->toThrow(DomainException::class);
    }

    $affiliationOwnedFields = [
        'submission_contributor_id' => Str::uuid()->toString(),
        'institution_name_snapshot' => 'Snapshot Name',
        'ror_uri_snapshot' => 'https://ror.org/000000001',
        'sequence' => 3,
    ];

    foreach ($affiliationOwnedFields as $field => $value) {
        expect(fn () => $action->handle($user, $submission, contributorsMutationPayload([
            contributorsMutationContributor([
                'affiliations' => [contributorsMutationAffiliation([$field => $value])],
            ]),
        ])))->toThrow(DomainException::class);
    }

    expect(fn () => $action->handle($user, $submission, [
        'contributors' => [],
        'submission_id' => $submission->id,
    ]))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('malformed contributor payload shapes are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionContributorsAction::class);

    $base = contributorsMutationContributor();

    $missingKeyPayloads = [];

    foreach (['display_name', 'single_name', 'email', 'country_code', 'is_corresponding', 'affiliations'] as $missingKey) {
        $contributor = $base;
        unset($contributor[$missingKey]);

        $missingKeyPayloads[] = ['contributors' => [$contributor]];
    }

    $malformedPayloads = array_merge([
        [],
        ['contributors' => 'nope'],
        ['contributors' => ['not-a-list' => $base]],
        ['contributors' => ['nope']],
        ['contributors' => [contributorsMutationContributor(['display_name' => 42])]],
        ['contributors' => [contributorsMutationContributor(['single_name' => 'yes'])]],
        ['contributors' => [contributorsMutationContributor(['is_corresponding' => 1])]],
        ['contributors' => [contributorsMutationContributor(['email' => 'not-an-email'])]],
        ['contributors' => [contributorsMutationContributor(['country_code' => 'IDN'])]],
        ['contributors' => [contributorsMutationContributor(['orcid' => 42])]],
        ['contributors' => [contributorsMutationContributor(['id' => 42])]],
        ['contributors' => [contributorsMutationContributor(['id' => '   '])]],
        ['contributors' => [contributorsMutationContributor(['affiliations' => 'nope'])]],
        ['contributors' => [contributorsMutationContributor(['affiliations' => ['nope']])]],
        ['contributors' => [contributorsMutationContributor([
            'affiliations' => [['institution_name' => 'Missing Country', 'country_code' => null]],
        ])]],
        ['contributors' => [contributorsMutationContributor([
            'affiliations' => [['country_code' => 'ID']],
        ])]],
        ['contributors' => [contributorsMutationContributor([
            'affiliations' => [contributorsMutationAffiliation(['institution_name' => '   '])],
        ])]],
        ['contributors' => [contributorsMutationContributor([
            'affiliations' => [contributorsMutationAffiliation(['country_code' => 'IDN'])],
        ])]],
        ['contributors' => [contributorsMutationContributor([
            'affiliations' => [contributorsMutationAffiliation(['institution_id' => '   '])],
        ])]],
        ['contributors' => [contributorsMutationContributor([
            'affiliations' => [contributorsMutationAffiliation(['subdivision' => 42])],
        ])]],
        ['contributors' => [contributorsMutationContributor([
            'affiliations' => [contributorsMutationAffiliation(['institution_id' => Str::uuid()->toString()])],
        ])]],
        ['contributors' => [contributorsMutationContributor(['id' => Str::uuid()->toString()])]],
    ], $missingKeyPayloads);

    foreach ($malformedPayloads as $payload) {
        expect(fn () => $action->handle($user, $submission, $payload))->toThrow(DomainException::class);
    }

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('a non owner actor cannot mutate draft contributors', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);

    $stranger = User::factory()->create();

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle($stranger, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['display_name' => 'Stranger Edit']),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('a cancelled registration is rejected without partial mutation', function () {
    $edition = contributorsMutationEdition();
    $package = contributorsMutationPackage($edition);

    ['user' => $user, 'registration' => $registration] = contributorsMutationRegistration($edition, $package, 'CANCELLED');

    $submission = contributorsMutationSubmission($edition, $registration);

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['display_name' => 'Cancelled Edit']),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('a registration with a package from another conference edition is rejected without partial mutation', function () {
    $edition = contributorsMutationEdition();
    $otherEdition = contributorsMutationEdition('OTHER27');
    $foreignPackage = contributorsMutationPackage($otherEdition);

    ['user' => $user, 'registration' => $registration] = contributorsMutationRegistration($edition, $foreignPackage);

    $submission = contributorsMutationSubmission($edition, $registration);

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['display_name' => 'Foreign Package Edit']),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('a membership belonging to another conference edition is rejected without partial mutation', function () {
    $editionA = contributorsMutationEdition('ICHES27');
    $editionB = contributorsMutationEdition('OTHER27');
    $package = contributorsMutationPackage($editionA);

    ['user' => $user, 'registration' => $registration] = contributorsMutationRegistration($editionB, $package);

    $submission = contributorsMutationSubmission($editionA, $registration);

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['display_name' => 'Foreign Membership Edit']),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('registration payment states do not gate draft contributor editing', function () {
    ['registration' => $registration, 'user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $action = app(UpdateDraftSubmissionContributorsAction::class);
    $contributorId = null;

    foreach (['PENDING', 'PAYMENT_PENDING', 'CONFIRMED'] as $status) {
        Registration::query()
            ->whereKey($registration->id)
            ->update(['status' => $status]);

        $updated = $action->handle($user, $submission, contributorsMutationPayload([
            contributorsMutationContributor([
                'id' => $contributorId,
                'display_name' => "Author {$status}",
            ]),
        ]));

        $contributorId = $updated->contributors->first()->id;

        expect($updated->contributors)->toHaveCount(1)
            ->and($updated->contributors->first()->display_name)->toBe("Author {$status}")
            ->and($updated->academic_status)->toBe('DRAFT')
            ->and($updated->registration->status->value)->toBe($status);
    }

    expect(Payment::query()->count())->toBe(0)
        ->and(contributorsMutationActivityCount())->toBe(3);
});

test('a non draft submission cannot be mutated', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext([
        'academic_status' => 'SUBMITTED',
        'submitted_at' => now(),
    ]);

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $before = contributorsMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['display_name' => 'Submitted Edit']),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before);
});

test('a stale caller draft model cannot bypass the authoritative non draft guard', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    $staleSubmission = Submission::query()->findOrFail($submission->id);

    expect($staleSubmission->academic_status)->toBe('DRAFT');

    Submission::query()
        ->whereKey($submission->id)
        ->update(['academic_status' => 'SUBMITTED']);

    expect($staleSubmission->academic_status)->toBe('DRAFT');

    $before = contributorsMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $staleSubmission, contributorsMutationPayload([
        contributorsMutationContributor(['display_name' => 'Stale Edit']),
    ])))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before)
        ->and($submission->fresh()->academic_status)->toBe('SUBMITTED');
});

test('a draft submission with an assigned actual presenter rejects contributor mutation without side effects', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $presenter = contributorsMutationSeedContributor($submission, [
        'display_name' => 'Presenter Candidate',
    ]);
    contributorsMutationSeedAffiliation($presenter);

    $submission->update([
        'actual_presenter_contributor_id' => $presenter->id,
    ]);

    $before = contributorsMutationSnapshot($submission);
    $beforePresenterId = $submission->fresh()->actual_presenter_contributor_id;

    expect(fn () => app(UpdateDraftSubmissionContributorsAction::class)->handle(
        $user,
        $submission,
        contributorsMutationPayload([]),
    ))->toThrow(DomainException::class);

    expect(contributorsMutationSnapshot($submission))->toEqual($before)
        ->and($submission->fresh()->actual_presenter_contributor_id)->toBe($beforePresenterId)
        ->and($submission->fresh()->actualPresenter?->id)->toBe($presenter->id)
        ->and(contributorsMutationActivityCount())->toBe(0);
});

test('submission identity relationships remain immutable during contributor mutation', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $before = [
        'id' => $submission->id,
        'paper_code' => $submission->paper_code,
        'edition_id' => $submission->edition_id,
        'registration_id' => $submission->registration_id,
        'current_abstract_version' => $submission->current_abstract_version,
        'actual_presenter_contributor_id' => $submission->actual_presenter_contributor_id,
        'academic_status' => $submission->academic_status,
    ];

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['display_name' => 'Identity Author']),
    ]));

    expect($updated->id)->toBe($before['id'])
        ->and(Str::isUuid($updated->id, 7))->toBeTrue()
        ->and($updated->paper_code)->toBe($before['paper_code'])
        ->and($updated->edition_id)->toBe($before['edition_id'])
        ->and($updated->edition->is($edition))->toBeTrue()
        ->and($updated->registration_id)->toBe($before['registration_id'])
        ->and($updated->registration->is($registration))->toBeTrue()
        ->and($updated->current_abstract_version)->toBe($before['current_abstract_version'])
        ->and($updated->actual_presenter_contributor_id)->toBeNull()
        ->and($updated->academic_status)->toBe('DRAFT')
        ->and(Submission::query()->count())->toBe(1);
});

test('an inactive historical participation package does not block draft contributor editing', function () {
    ['package' => $package, 'user' => $user, 'submission' => $submission] = contributorsMutationContext();

    contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);

    ParticipationPackage::query()
        ->whereKey($package->id)
        ->update(['active' => false]);

    expect($package->fresh()->active)->toBeFalse();

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor(['display_name' => 'Inactive Package Author']),
    ]));

    expect($updated->contributors)->toHaveCount(1)
        ->and($updated->contributors->first()->display_name)->toBe('Inactive Package Author')
        ->and($updated->registration->package->active)->toBeFalse()
        ->and($updated->registration->package->edition_id)->toBe($updated->edition_id)
        ->and(Payment::query()->count())->toBe(0);
});

test('an empty contributor list clears draft contributors', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $existing = contributorsMutationSeedContributor($submission, ['display_name' => 'Existing Author']);
    contributorsMutationSeedAffiliation($existing);

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([]));

    expect($updated->contributors)->toHaveCount(0)
        ->and(SubmissionContributor::query()->where('submission_id', $submission->id)->count())->toBe(0)
        ->and(ContributorAffiliation::query()->count())->toBe(0)
        ->and(contributorsMutationActivityProperties()['contributor_count'])->toBe(0)
        ->and(contributorsMutationActivityCount())->toBe(1);
});

test('audit properties never contain private contributor or affiliation metadata', function () {
    ['user' => $user, 'submission' => $submission] = contributorsMutationContext();

    $updated = app(UpdateDraftSubmissionContributorsAction::class)->handle($user, $submission, contributorsMutationPayload([
        contributorsMutationContributor([
            'display_name' => 'Rahasia Penulis Utama',
            'given_name' => 'Rahasia',
            'family_name' => 'Penulis',
            'email' => 'rahasia.mentah@example.com',
            'orcid' => '0000-0002-1825-0097',
            'affiliations' => [
                contributorsMutationAffiliation([
                    'institution_name' => 'Institusi Rahasia Terbatas',
                    'subdivision' => 'Fakultas Rahasia',
                    'city' => 'Kota Rahasia',
                ]),
            ],
        ]),
    ]));

    expect($updated->contributors)->toHaveCount(1);

    $properties = contributorsMutationActivityProperties();
    $serializedProperties = json_encode($properties);

    foreach ([
        'Rahasia Penulis Utama',
        'Rahasia',
        'rahasia.mentah@example.com',
        '0000-0002-1825-0097',
        'Institusi Rahasia Terbatas',
        'Fakultas Rahasia',
        'Kota Rahasia',
    ] as $secret) {
        expect($serializedProperties)->not->toContain($secret);
    }

    $actualPropertyKeys = array_keys($properties);
    $expectedPropertyKeys = [
        'conference_edition_id',
        'registration_id',
        'paper_code',
        'contributor_count',
        'contributor_ids',
        'corresponding_count',
        'affiliation_counts',
    ];

    sort($actualPropertyKeys);
    sort($expectedPropertyKeys);

    expect($actualPropertyKeys)->toBe($expectedPropertyKeys)
        ->and($properties['contributor_count'])->toBe(1)
        ->and($properties['contributor_ids'])->toBe([$updated->contributors->first()->id])
        ->and($properties['affiliation_counts'])->toBe([1]);
});
