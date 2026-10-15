<?php

use App\Actions\Submission\UpdateDraftSubmissionReferencesAction;
use App\Enums\BillingMode;
use App\Enums\Locale;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\SubmissionReference;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function referencesMutationEdition(string $editionCode = 'ICHES27'): ConferenceEdition
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

function referencesMutationPackage(ConferenceEdition $edition, bool $active = true): ParticipationPackage
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

function referencesMutationRegistration(
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

function referencesMutationSubmission(
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

function referencesMutationContext(array $submissionOverrides = []): array
{
    $edition = referencesMutationEdition();
    $package = referencesMutationPackage($edition);

    ['user' => $user, 'membership' => $membership, 'registration' => $registration] = referencesMutationRegistration(
        $edition,
        $package,
    );

    $submission = referencesMutationSubmission($edition, $registration, $submissionOverrides);

    return compact('edition', 'package', 'user', 'membership', 'registration', 'submission');
}

function referencesMutationReference(array $overrides = []): array
{
    return array_merge([
        'id' => null,
        'raw_citation' => 'Santoso, B. (2024). Example Study. Journal of Examples, 12(3), 45-67.',
        'doi' => null,
        'url' => null,
    ], $overrides);
}

function referencesMutationPayload(array $references): array
{
    return ['references' => $references];
}

function referencesMutationSeedReference(Submission $submission, array $overrides = []): SubmissionReference
{
    return $submission->references()->create(array_merge([
        'raw_citation' => 'Existing Citation',
        'doi' => null,
        'url' => null,
        'structured_json' => null,
        'sequence' => 1,
    ], $overrides));
}

function referencesMutationSnapshot(Submission $submission): array
{
    $references = SubmissionReference::query()
        ->where('submission_id', $submission->id)
        ->orderBy('sequence')
        ->get()
        ->map(fn (SubmissionReference $reference): array => [
            'id' => $reference->id,
            'submission_id' => $reference->submission_id,
            'sequence' => $reference->sequence,
            'raw_citation' => $reference->raw_citation,
            'doi' => $reference->doi,
            'url' => $reference->url,
            'structured_json' => $reference->structured_json,
        ])
        ->all();

    return [
        'references' => $references,
        'activity_count' => referencesMutationActivityCount(),
    ];
}

function referencesMutationActivityCount(): int
{
    return DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_references_updated')
        ->count();
}

function referencesMutationActivityProperties(): ?array
{
    $activity = DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_references_updated')
        ->orderByDesc('id')
        ->first();

    return json_decode((string) $activity?->properties, true);
}

test('a complete reference payload persists order identity and one privacy safe audit event', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user, 'submission' => $submission] = referencesMutationContext();

    $updated = app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'raw_citation' => '  Santoso, B. (2024). Example Study. Journal of Examples, 12(3), 45-67.  ',
            'doi' => 'doi:10.1000/example-1',
            'url' => 'https://example.org/articles/1',
        ]),
        referencesMutationReference([
            'raw_citation' => 'Aminah, S. (2025). Second Example.',
            'doi' => null,
            'url' => null,
        ]),
    ]));

    $references = $updated->references;

    expect($updated->academic_status)->toBe('DRAFT')
        ->and($references)->toHaveCount(2)
        ->and($references->pluck('sequence')->all())->toBe([1, 2])
        ->and($references->pluck('raw_citation')->all())->toBe([
            'Santoso, B. (2024). Example Study. Journal of Examples, 12(3), 45-67.',
            'Aminah, S. (2025). Second Example.',
        ])
        ->and($references->pluck('doi')->all())->toBe(['10.1000/example-1', null])
        ->and($references->pluck('url')->all())->toBe(['https://example.org/articles/1', null])
        ->and(Str::isUuid($references[0]->id, 7))->toBeTrue()
        ->and(Str::isUuid($references[1]->id, 7))->toBeTrue()
        ->and($references[0]->id)->not->toBe($references[1]->id)
        ->and($references->pluck('structured_json')->unique()->all())->toBe([null]);

    $activity = DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_references_updated')
        ->first();

    $properties = referencesMutationActivityProperties();

    expect($activity)->not->toBeNull()
        ->and($activity->subject_id)->toBe($submission->id)
        ->and($activity->causer_id)->toBe($user->id)
        ->and($properties['conference_edition_id'])->toBe($edition->id)
        ->and($properties['registration_id'])->toBe($registration->id)
        ->and($properties['paper_code'])->toBe('ICHES27-P-0001')
        ->and($properties['reference_count'])->toBe(2)
        ->and($properties['reference_ids'])->toBe([$references[0]->id, $references[1]->id])
        ->and(referencesMutationActivityCount())->toBe(1);
});

test('retained reference identifiers survive edits and reordering while new references are added', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $first = referencesMutationSeedReference($submission, [
        'raw_citation' => 'First Citation',
        'sequence' => 1,
    ]);

    $second = referencesMutationSeedReference($submission, [
        'raw_citation' => 'Second Citation',
        'sequence' => 2,
    ]);

    $updated = app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'id' => $second->id,
            'raw_citation' => 'Second Citation Edited',
        ]),
        referencesMutationReference([
            'id' => $first->id,
            'raw_citation' => 'First Citation',
        ]),
        referencesMutationReference([
            'raw_citation' => 'Third Citation',
        ]),
    ]));

    $references = $updated->references;

    expect($references)->toHaveCount(3)
        ->and($references->pluck('id')->all())->toBe([$second->id, $first->id, $references[2]->id])
        ->and($references->pluck('sequence')->all())->toBe([1, 2, 3])
        ->and($references->pluck('raw_citation')->all())
        ->toBe(['Second Citation Edited', 'First Citation', 'Third Citation'])
        ->and($references[2]->id)->not->toBe($first->id)
        ->and(Str::isUuid($references[2]->id, 7))->toBeTrue()
        ->and(SubmissionReference::query()->where('submission_id', $submission->id)->count())->toBe(3);
});

test('existing references are swapped and reordered onto consecutive one based sequences', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $first = referencesMutationSeedReference($submission, ['raw_citation' => 'Alpha', 'sequence' => 1]);
    $second = referencesMutationSeedReference($submission, ['raw_citation' => 'Beta', 'sequence' => 2]);
    $third = referencesMutationSeedReference($submission, ['raw_citation' => 'Gamma', 'sequence' => 3]);

    $action = app(UpdateDraftSubmissionReferencesAction::class);

    $swapped = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['id' => $second->id, 'raw_citation' => 'Beta']),
        referencesMutationReference(['id' => $first->id, 'raw_citation' => 'Alpha']),
        referencesMutationReference(['id' => $third->id, 'raw_citation' => 'Gamma']),
    ]));

    expect($swapped->references->pluck('id')->all())->toBe([$second->id, $first->id, $third->id])
        ->and($swapped->references->pluck('sequence')->all())->toBe([1, 2, 3])
        ->and($swapped->references->pluck('raw_citation')->all())->toBe(['Beta', 'Alpha', 'Gamma']);

    $reordered = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['id' => $third->id, 'raw_citation' => 'Gamma']),
        referencesMutationReference(['id' => $first->id, 'raw_citation' => 'Alpha']),
        referencesMutationReference(['id' => $second->id, 'raw_citation' => 'Beta']),
    ]));

    expect($reordered->references->pluck('id')->all())->toBe([$third->id, $first->id, $second->id])
        ->and($reordered->references->pluck('sequence')->all())->toBe([1, 2, 3])
        ->and($reordered->references->pluck('sequence')->unique()->count())->toBe(3)
        ->and(SubmissionReference::query()->where('submission_id', $submission->id)->count())->toBe(3);
});

test('references omitted from the complete payload are removed only after validation succeeds', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $retained = referencesMutationSeedReference($submission, [
        'raw_citation' => 'Retained Citation',
        'structured_json' => ['title' => 'Retained Enrichment'],
        'sequence' => 1,
    ]);

    $omitted = referencesMutationSeedReference($submission, [
        'raw_citation' => 'Omitted Citation',
        'doi' => '10.1000/omitted',
        'sequence' => 2,
    ]);

    $before = referencesMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionReferencesAction::class);

    expect(fn () => $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'id' => $retained->id,
            'raw_citation' => 'Retained Citation Edited',
        ]),
        referencesMutationReference(['doi' => 'not-a-doi']),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before)
        ->and(referencesMutationActivityCount())->toBe(0);

    $updated = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'id' => $retained->id,
            'raw_citation' => 'Retained Citation Edited',
        ]),
    ]));

    expect($updated->references)->toHaveCount(1)
        ->and($updated->references->first()->id)->toBe($retained->id)
        ->and($updated->references->first()->raw_citation)->toBe('Retained Citation Edited')
        ->and(SubmissionReference::query()->whereKey($omitted->id)->exists())->toBeFalse()
        ->and(referencesMutationActivityCount())->toBe(1);
});

test('a reference identifier from another submission is rejected atomically', function () {
    ['edition' => $edition, 'registration' => $registration, 'user' => $user, 'submission' => $submission] = referencesMutationContext();

    $own = referencesMutationSeedReference($submission, ['raw_citation' => 'Own Citation']);

    $otherSubmission = referencesMutationSubmission($edition, $registration, [
        'paper_code' => 'ICHES27-P-0002',
    ]);

    $foreign = referencesMutationSeedReference($otherSubmission, [
        'raw_citation' => 'Foreign Citation',
        'sequence' => 1,
    ]);

    $before = referencesMutationSnapshot($submission);
    $foreignBefore = referencesMutationSnapshot($otherSubmission);

    expect(fn () => app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'id' => $own->id,
            'raw_citation' => 'Own Citation Edited',
        ]),
        referencesMutationReference([
            'id' => $foreign->id,
            'raw_citation' => 'Foreign Citation Edited',
        ]),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before)
        ->and(referencesMutationSnapshot($otherSubmission))->toEqual($foreignBefore);
});

test('duplicate reference identifiers in one payload are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $existing = referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['id' => $existing->id, 'raw_citation' => 'Renamed Once']),
        referencesMutationReference(['id' => $existing->id, 'raw_citation' => 'Renamed Twice']),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('duplicate citations and dois are preserved without automatic deduplication', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $updated = app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'raw_citation' => 'Shared Citation',
            'doi' => '10.1000/shared',
        ]),
        referencesMutationReference([
            'raw_citation' => 'Shared Citation',
            'doi' => '10.1000/shared',
        ]),
        referencesMutationReference([
            'raw_citation' => 'Shared Citation',
            'doi' => null,
        ]),
    ]));

    expect($updated->references)->toHaveCount(3)
        ->and($updated->references->pluck('sequence')->all())->toBe([1, 2, 3])
        ->and($updated->references->pluck('raw_citation')->unique()->all())->toBe(['Shared Citation'])
        ->and($updated->references->pluck('doi')->all())
        ->toBe(['10.1000/shared', '10.1000/shared', null]);
});

test('doi values are normalized to the bare form and invalid doi values are rejected atomically', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $action = app(UpdateDraftSubmissionReferencesAction::class);

    $updated = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['doi' => ' 10.1000/xyz123 ']),
        referencesMutationReference(['raw_citation' => 'Second Citation', 'doi' => 'doi:10.1000/xyz123']),
        referencesMutationReference(['raw_citation' => 'Third Citation', 'doi' => 'DOI: 10.1000/xyz123']),
        referencesMutationReference(['raw_citation' => 'Fourth Citation', 'doi' => 'https://doi.org/10.1000/xyz123']),
        referencesMutationReference(['raw_citation' => 'Fifth Citation', 'doi' => 'http://dx.doi.org/10.1000/xyz123']),
        referencesMutationReference(['raw_citation' => 'Sixth Citation', 'doi' => 'doi.org/10.1000/xyz123']),
        referencesMutationReference(['raw_citation' => 'Seventh Citation', 'doi' => null]),
        referencesMutationReference(['raw_citation' => 'Eighth Citation', 'doi' => '   ']),
    ]));

    expect($updated->references->pluck('doi')->all())->toBe([
        '10.1000/xyz123',
        '10.1000/xyz123',
        '10.1000/xyz123',
        '10.1000/xyz123',
        '10.1000/xyz123',
        '10.1000/xyz123',
        null,
        null,
    ]);

    $before = referencesMutationSnapshot($submission);

    foreach ([
        'not-a-doi',
        '10.1000',
        '10.1000/',
        '10.1/xyz',
        '10.1234567890/xyz',
        'doi:garbage',
        '10.1000/with space',
        '10.1000/xyz?foo=bar',
        '10.1000/xyz#fragment',
        'https://example.org/10.1000/xyz123',
        'https://doi.org/not-a-doi',
        'doi:10.1000/xyz123?query=1',
    ] as $doi) {
        expect(fn () => $action->handle($user, $submission, referencesMutationPayload([
            referencesMutationReference(['doi' => $doi]),
        ])))->toThrow(DomainException::class);
    }

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('urls are stored only when they are absolute http or https urls', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $action = app(UpdateDraftSubmissionReferencesAction::class);

    $updated = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['url' => ' https://example.org/articles/1 ']),
        referencesMutationReference(['raw_citation' => 'Second Citation', 'url' => 'http://example.org/articles/2']),
        referencesMutationReference(['raw_citation' => 'Third Citation', 'url' => null]),
        referencesMutationReference(['raw_citation' => 'Fourth Citation', 'url' => '   ']),
    ]));

    expect($updated->references->pluck('url')->all())->toBe([
        'https://example.org/articles/1',
        'http://example.org/articles/2',
        null,
        null,
    ]);

    $before = referencesMutationSnapshot($submission);

    foreach ([
        'example.org/articles/1',
        'www.example.org/articles/1',
        '//example.org/articles/1',
        'javascript:alert(1)',
        'ftp://example.org/articles/1',
        'file:///etc/passwd',
        'https://exa mple.org/articles/1',
        'not a url',
        'https://',
    ] as $url) {
        expect(fn () => $action->handle($user, $submission, referencesMutationPayload([
            referencesMutationReference(['url' => $url]),
        ])))->toThrow(DomainException::class);
    }

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('optional doi and url metadata remain nullable when the keys are absent', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $updated = app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        ['raw_citation' => 'Citation Without Optional Metadata'],
        ['id' => null, 'raw_citation' => 'Second Citation Without Optional Metadata'],
    ]));

    expect($updated->references)->toHaveCount(2)
        ->and($updated->references->pluck('doi')->all())->toBe([null, null])
        ->and($updated->references->pluck('url')->all())->toBe([null, null])
        ->and($updated->references->pluck('sequence')->all())->toBe([1, 2]);
});

test('server owned and unsupported payload fields are rejected instead of silently ignored', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionReferencesAction::class);

    $referenceOwnedFields = [
        'submission_id' => $submission->id,
        'sequence' => 7,
        'structured_json' => ['title' => 'Injected Enrichment'],
        'created_at' => now()->toIso8601String(),
        'updated_at' => now()->toIso8601String(),
    ];

    foreach ($referenceOwnedFields as $field => $value) {
        expect(fn () => $action->handle($user, $submission, referencesMutationPayload([
            referencesMutationReference([$field => $value]),
        ])))->toThrow(DomainException::class);
    }

    expect(fn () => $action->handle($user, $submission, [
        'references' => [],
        'submission_id' => $submission->id,
    ]))->toThrow(DomainException::class)
        ->and(fn () => $action->handle($user, $submission, referencesMutationPayload([]) + [
            'structured_json' => ['title' => 'Injected Enrichment'],
        ]))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('malformed reference payload shapes are rejected without partial mutation', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionReferencesAction::class);

    $base = referencesMutationReference();

    $withoutCitationKey = $base;
    unset($withoutCitationKey['raw_citation']);

    $malformedPayloads = [
        [],
        ['references' => 'nope'],
        ['references' => ['not-a-list' => $base]],
        ['references' => ['raw_citation' => 'Not A List']],
        ['references' => ['nope']],
        ['references' => [null]],
        ['references' => [$withoutCitationKey]],
        ['references' => [referencesMutationReference(['raw_citation' => 42])]],
        ['references' => [referencesMutationReference(['raw_citation' => '   '])]],
        ['references' => [referencesMutationReference(['raw_citation' => ['not a string']])]],
        ['references' => [referencesMutationReference(['id' => 42])]],
        ['references' => [referencesMutationReference(['id' => '   '])]],
        ['references' => [referencesMutationReference(['doi' => 42])]],
        ['references' => [referencesMutationReference(['url' => 42])]],
        ['references' => [referencesMutationReference(['url' => ['https://example.org']])]],
        ['references' => [referencesMutationReference(['raw_citation' => 'Valid', 'unknown' => 'value'])]],
    ];

    foreach ($malformedPayloads as $payload) {
        expect(fn () => $action->handle($user, $submission, $payload))->toThrow(DomainException::class);
    }

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('over long strings are rejected before any database failure', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);
    $action = app(UpdateDraftSubmissionReferencesAction::class);

    $overlongPayloads = [
        referencesMutationPayload([referencesMutationReference([
            'doi' => '10.1000/'.str_repeat('a', 300),
        ])]),
        referencesMutationPayload([referencesMutationReference([
            'url' => 'https://example.org/articles/'.str_repeat('a', 300),
        ])]),
        referencesMutationPayload([referencesMutationReference([
            'raw_citation' => str_repeat('a', 65536),
        ])]),
    ];

    foreach ($overlongPayloads as $payload) {
        expect(fn () => $action->handle($user, $submission, $payload))->toThrow(DomainException::class);
    }

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('structured enrichment survives a pure reorder but is invalidated when the source changes', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $first = referencesMutationSeedReference($submission, [
        'raw_citation' => 'First Citation',
        'structured_json' => ['title' => 'First Enriched Title'],
        'sequence' => 1,
    ]);

    $second = referencesMutationSeedReference($submission, [
        'raw_citation' => 'Second Citation',
        'doi' => '10.1000/second',
        'structured_json' => ['title' => 'Second Enriched Title'],
        'sequence' => 2,
    ]);

    $action = app(UpdateDraftSubmissionReferencesAction::class);

    $reordered = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'id' => $second->id,
            'raw_citation' => 'Second Citation',
            'doi' => '10.1000/second',
        ]),
        referencesMutationReference([
            'id' => $first->id,
            'raw_citation' => 'First Citation',
        ]),
    ]));

    expect($reordered->references->pluck('id')->all())->toBe([$second->id, $first->id])
        ->and($reordered->references->pluck('structured_json')->all())->toBe([
            ['title' => 'Second Enriched Title'],
            ['title' => 'First Enriched Title'],
        ]);

    $citationChanged = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'id' => $second->id,
            'raw_citation' => 'Second Citation Edited',
            'doi' => '10.1000/second',
        ]),
        referencesMutationReference([
            'id' => $first->id,
            'raw_citation' => 'First Citation',
        ]),
    ]));

    expect($citationChanged->references->pluck('structured_json')->all())->toBe([
        null,
        ['title' => 'First Enriched Title'],
    ]);

    $doiChanged = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'id' => $second->id,
            'raw_citation' => 'Second Citation Edited',
            'doi' => '10.1000/second',
        ]),
        referencesMutationReference([
            'id' => $first->id,
            'raw_citation' => 'First Citation',
            'doi' => '10.1000/first',
        ]),
    ]));

    expect($doiChanged->references->pluck('structured_json')->all())->toBe([null, null]);

    $urlChanged = $action->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'id' => $second->id,
            'raw_citation' => 'Second Citation Edited',
            'doi' => '10.1000/second',
            'url' => 'https://example.org/articles/2',
        ]),
        referencesMutationReference([
            'id' => $first->id,
            'raw_citation' => 'First Citation',
            'doi' => '10.1000/first',
        ]),
    ]));

    expect($urlChanged->references->pluck('structured_json')->all())->toBe([null, null])
        ->and($urlChanged->references->pluck('url')->all())
        ->toBe(['https://example.org/articles/2', null]);
});

test('an empty reference list clears draft references', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation', 'sequence' => 1]);
    referencesMutationSeedReference($submission, [
        'raw_citation' => 'Second Existing Citation',
        'doi' => '10.1000/existing',
        'sequence' => 2,
    ]);

    $updated = app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([]));

    expect($updated->references)->toHaveCount(0)
        ->and(SubmissionReference::query()->where('submission_id', $submission->id)->count())->toBe(0)
        ->and(referencesMutationActivityProperties()['reference_count'])->toBe(0)
        ->and(referencesMutationActivityProperties()['reference_ids'])->toBe([])
        ->and(referencesMutationActivityCount())->toBe(1);
});

test('registration payment states do not gate draft reference editing', function () {
    ['registration' => $registration, 'user' => $user, 'submission' => $submission] = referencesMutationContext();

    $action = app(UpdateDraftSubmissionReferencesAction::class);
    $referenceId = null;

    foreach (['PENDING', 'PAYMENT_PENDING', 'CONFIRMED'] as $status) {
        Registration::query()
            ->whereKey($registration->id)
            ->update(['status' => $status]);

        $updated = $action->handle($user, $submission, referencesMutationPayload([
            referencesMutationReference([
                'id' => $referenceId,
                'raw_citation' => "Citation for {$status}",
            ]),
        ]));

        $referenceId = $updated->references->first()->id;

        expect($updated->references)->toHaveCount(1)
            ->and($updated->references->first()->raw_citation)->toBe("Citation for {$status}")
            ->and($updated->academic_status)->toBe('DRAFT')
            ->and($updated->registration->status->value)->toBe($status);
    }

    expect(Payment::query()->count())->toBe(0)
        ->and(referencesMutationActivityCount())->toBe(3);
});

test('a cancelled registration is rejected without partial mutation', function () {
    $edition = referencesMutationEdition();
    $package = referencesMutationPackage($edition);

    ['user' => $user, 'registration' => $registration] = referencesMutationRegistration($edition, $package, 'CANCELLED');

    $submission = referencesMutationSubmission($edition, $registration);

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['raw_citation' => 'Cancelled Edit']),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('an inactive historical participation package does not block draft reference editing', function () {
    ['package' => $package, 'user' => $user, 'submission' => $submission] = referencesMutationContext();

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    ParticipationPackage::query()
        ->whereKey($package->id)
        ->update(['active' => false]);

    expect($package->fresh()->active)->toBeFalse();

    $updated = app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['raw_citation' => 'Inactive Package Citation']),
    ]));

    expect($updated->references)->toHaveCount(1)
        ->and($updated->references->first()->raw_citation)->toBe('Inactive Package Citation')
        ->and($updated->registration->package->active)->toBeFalse()
        ->and($updated->registration->package->edition_id)->toBe($updated->edition_id)
        ->and(Payment::query()->count())->toBe(0);
});

test('a registration with a package from another conference edition is rejected without partial mutation', function () {
    $edition = referencesMutationEdition();
    $otherEdition = referencesMutationEdition('OTHER27');
    $foreignPackage = referencesMutationPackage($otherEdition);

    ['user' => $user, 'registration' => $registration] = referencesMutationRegistration($edition, $foreignPackage);

    $submission = referencesMutationSubmission($edition, $registration);

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['raw_citation' => 'Foreign Package Edit']),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('a membership belonging to another conference edition is rejected without partial mutation', function () {
    $editionA = referencesMutationEdition('ICHES27');
    $editionB = referencesMutationEdition('OTHER27');
    $package = referencesMutationPackage($editionA);

    ['user' => $user, 'registration' => $registration] = referencesMutationRegistration($editionB, $package);

    $submission = referencesMutationSubmission($editionA, $registration);

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['raw_citation' => 'Foreign Membership Edit']),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('a non owner actor cannot mutate draft references', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);

    $stranger = User::factory()->create();

    expect(fn () => app(UpdateDraftSubmissionReferencesAction::class)->handle($stranger, $submission, referencesMutationPayload([
        referencesMutationReference(['raw_citation' => 'Stranger Edit']),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('a non draft submission cannot be mutated', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext([
        'academic_status' => 'SUBMITTED',
        'submitted_at' => now(),
    ]);

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $before = referencesMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['raw_citation' => 'Submitted Edit']),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before);
});

test('a stale caller draft model cannot bypass the authoritative non draft guard', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    referencesMutationSeedReference($submission, ['raw_citation' => 'Existing Citation']);

    $staleSubmission = Submission::query()->findOrFail($submission->id);

    expect($staleSubmission->academic_status)->toBe('DRAFT');

    Submission::query()
        ->whereKey($submission->id)
        ->update(['academic_status' => 'SUBMITTED']);

    expect($staleSubmission->academic_status)->toBe('DRAFT');

    $before = referencesMutationSnapshot($submission);

    expect(fn () => app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $staleSubmission, referencesMutationPayload([
        referencesMutationReference(['raw_citation' => 'Stale Edit']),
    ])))->toThrow(DomainException::class);

    expect(referencesMutationSnapshot($submission))->toEqual($before)
        ->and($submission->fresh()->academic_status)->toBe('SUBMITTED');
});

test('submission identity and other metadata remain immutable during reference mutation', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $beforeAttributes = Submission::query()->findOrFail($submission->id)->getAttributes();

    $updated = app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference(['raw_citation' => 'Immutable Identity Citation']),
    ]));

    $afterAttributes = Submission::query()->findOrFail($submission->id)->getAttributes();

    expect($updated->id)->toBe($submission->id)
        ->and(Str::isUuid($updated->id, 7))->toBeTrue()
        ->and($updated->paper_code)->toBe('ICHES27-P-0001')
        ->and($updated->edition_id)->toBe($submission->edition_id)
        ->and($updated->registration_id)->toBe($submission->registration_id)
        ->and($updated->track_id)->toBeNull()
        ->and($updated->primary_locale)->toBe(Locale::Indonesian)
        ->and($updated->academic_status)->toBe('DRAFT')
        ->and($updated->actual_presenter_contributor_id)->toBeNull()
        ->and($updated->current_abstract_version)->toBe(0)
        ->and($afterAttributes)->toBe($beforeAttributes)
        ->and(Submission::query()->count())->toBe(1);
});

test('audit properties never contain private reference metadata', function () {
    ['user' => $user, 'submission' => $submission] = referencesMutationContext();

    $updated = app(UpdateDraftSubmissionReferencesAction::class)->handle($user, $submission, referencesMutationPayload([
        referencesMutationReference([
            'raw_citation' => 'Rahasia Judul Jurnal Terbatas (2026). 12(3), 45-67.',
            'doi' => '10.1234/rahasia-jurnal-terbatas',
            'url' => 'https://rahasia.example.org/artikel/terbatas',
        ]),
    ]));

    expect($updated->references)->toHaveCount(1);

    $properties = referencesMutationActivityProperties();
    $serializedProperties = json_encode($properties);

    foreach ([
        'Rahasia Judul Jurnal Terbatas',
        '10.1234/rahasia-jurnal-terbatas',
        'https://rahasia.example.org/artikel/terbatas',
        'rahasia.example.org',
    ] as $secret) {
        expect($serializedProperties)->not->toContain($secret);
    }

    $actualPropertyKeys = array_keys($properties);
    $expectedPropertyKeys = [
        'conference_edition_id',
        'registration_id',
        'paper_code',
        'reference_count',
        'reference_ids',
    ];

    sort($actualPropertyKeys);
    sort($expectedPropertyKeys);

    expect($actualPropertyKeys)->toBe($expectedPropertyKeys)
        ->and($properties['reference_count'])->toBe(1)
        ->and($properties['reference_ids'])->toBe([$updated->references->first()->id]);
});
