<?php

use App\Actions\Numbering\NextEditionNumberAction;
use App\Actions\Submission\CreateDraftSubmissionAction;
use App\Enums\BillingMode;
use App\Enums\Locale;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\NumberSequence;
use App\Models\ParticipationPackage;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function makeDraftSubmissionEdition(string $editionCode = 'ICHES27'): ConferenceEdition
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

function makeDraftSubmissionPackage(ConferenceEdition $edition): ParticipationPackage
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

function makeDraftSubmissionRegistration(
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

function makeDraftSubmissionContext(): array
{
    $edition = makeDraftSubmissionEdition();
    $package = makeDraftSubmissionPackage($edition);
    ['user' => $user, 'membership' => $membership, 'registration' => $registration] = makeDraftSubmissionRegistration(
        $edition,
        $package,
    );

    return compact('edition', 'package', 'user', 'membership', 'registration');
}

test('draft submission creation allocates an edition scoped paper id and persists draft state', function () {
    ['edition' => $edition, 'user' => $user, 'registration' => $registration] = makeDraftSubmissionContext();

    $action = app(CreateDraftSubmissionAction::class);

    $submission = $action->handle($user, $edition, $registration, Locale::Indonesian->value);

    expect($submission->paper_code)->toBe('ICHES27-P-0001')
        ->and($submission->academic_status)->toBe('DRAFT')
        ->and($submission->primary_locale)->toBe(Locale::Indonesian)
        ->and($submission->track_id)->toBeNull()
        ->and($submission->current_abstract_version)->toBe(0)
        ->and($submission->actual_presenter_contributor_id)->toBeNull()
        ->and($submission->submitted_at)->toBeNull()
        ->and($submission->accepted_at)->toBeNull()
        ->and($submission->rejected_at)->toBeNull()
        ->and($submission->final_academic_approved_at)->toBeNull()
        ->and(Str::isUuid($submission->id, 7))->toBeTrue()
        ->and($submission->edition->is($edition))->toBeTrue()
        ->and($submission->registration->is($registration))->toBeTrue();

    $second = $action->handle($user, $edition, $registration, Locale::English->value);

    $sequence = NumberSequence::query()
        ->where('edition_id', $edition->id)
        ->where('sequence_type', 'PAPER')
        ->first();

    expect($second->paper_code)->toBe('ICHES27-P-0002')
        ->and($sequence)->not->toBeNull()
        ->and($sequence->prefix)->toBe('ICHES27-P-')
        ->and($sequence->last_value)->toBe(2)
        ->and(Submission::query()->count())->toBe(2);

    $activity = DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_draft_created')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->subject_id)->toBe($submission->id)
        ->and($activity->causer_id)->toBe($user->id)
        ->and(json_decode($activity->properties, true)['paper_code'])->toBe('ICHES27-P-0001')
        ->and(json_decode($activity->properties, true)['conference_edition_id'])->toBe($edition->id);
});

test('payment state does not gate draft submission creation', function () {
    ['edition' => $edition, 'package' => $package] = makeDraftSubmissionContext();

    $action = app(CreateDraftSubmissionAction::class);

    $codes = [];

    foreach (['PENDING', 'PAYMENT_PENDING', 'CONFIRMED'] as $status) {
        ['user' => $user, 'registration' => $registration] = makeDraftSubmissionRegistration(
            $edition,
            $package,
            $status,
        );

        $submission = $action->handle($user, $edition, $registration, 'id');

        expect($submission->academic_status)->toBe('DRAFT')
            ->and($submission->registration->status->value)->toBe($status);

        $codes[] = $submission->paper_code;
    }

    expect($codes)->toBe(['ICHES27-P-0001', 'ICHES27-P-0002', 'ICHES27-P-0003'])
        ->and(Submission::query()->count())->toBe(3)
        ->and(Payment::query()->count())->toBe(0);
});

test('cancelled registration is rejected as an invalid participation context', function () {
    ['edition' => $edition, 'package' => $package] = makeDraftSubmissionContext();
    ['user' => $user, 'registration' => $registration] = makeDraftSubmissionRegistration(
        $edition,
        $package,
        'CANCELLED',
    );

    expect(fn () => app(CreateDraftSubmissionAction::class)->handle($user, $edition, $registration, 'id'))
        ->toThrow(DomainException::class);

    expect(Submission::query()->count())->toBe(0)
        ->and(NumberSequence::query()->where('sequence_type', 'PAPER')->exists())->toBeFalse();
});

test('stale caller registration state cannot bypass the cancelled guard', function () {
    ['edition' => $edition, 'user' => $user, 'registration' => $registration] = makeDraftSubmissionContext();

    $staleRegistration = Registration::query()->findOrFail($registration->id);

    expect($staleRegistration->status->value)->toBe('PENDING');

    Registration::query()
        ->whereKey($registration->id)
        ->update(['status' => 'CANCELLED']);

    expect($staleRegistration->status->value)->toBe('PENDING');

    expect(fn () => app(CreateDraftSubmissionAction::class)->handle($user, $edition, $staleRegistration, 'id'))
        ->toThrow(DomainException::class);

    $freshRegistration = Registration::query()->findOrFail($registration->id);

    expect($freshRegistration->status->value)->toBe('CANCELLED')
        ->and(Submission::query()->count())->toBe(0)
        ->and(NumberSequence::query()->where('sequence_type', 'PAPER')->exists())->toBeFalse();
});

test('draft creation enforces edition and registration ownership guards', function () {
    ['edition' => $edition, 'user' => $owner, 'registration' => $registration] = makeDraftSubmissionContext();

    $action = app(CreateDraftSubmissionAction::class);

    $stranger = User::factory()->create();

    expect(fn () => $action->handle($stranger, $edition, $registration, 'id'))
        ->toThrow(DomainException::class);

    $otherEdition = makeDraftSubmissionEdition('OTHER27');
    $otherPackage = makeDraftSubmissionPackage($otherEdition);
    ['user' => $otherUser, 'registration' => $otherRegistration] = makeDraftSubmissionRegistration(
        $otherEdition,
        $otherPackage,
    );

    expect(fn () => $action->handle($otherUser, $edition, $otherRegistration, 'id'))
        ->toThrow(DomainException::class)
        ->and($action->handle($owner, $edition, $registration, 'id')->paper_code)->toBe('ICHES27-P-0001')
        ->and(Submission::query()->count())->toBe(1);
});

test('draft creation rejects an unsupported primary scholarly locale', function () {
    ['edition' => $edition, 'user' => $user, 'registration' => $registration] = makeDraftSubmissionContext();

    expect(fn () => app(CreateDraftSubmissionAction::class)->handle($user, $edition, $registration, 'fr'))
        ->toThrow(DomainException::class);

    expect(Submission::query()->count())->toBe(0)
        ->and(NumberSequence::query()->where('sequence_type', 'PAPER')->exists())->toBeFalse();
});

test('multiple draft submissions are allowed for one registration', function () {
    ['edition' => $edition, 'user' => $user, 'registration' => $registration] = makeDraftSubmissionContext();

    $action = app(CreateDraftSubmissionAction::class);

    $first = $action->handle($user, $edition, $registration, 'id');
    $second = $action->handle($user, $edition, $registration, 'ar');

    expect($first->paper_code)->toBe('ICHES27-P-0001')
        ->and($second->paper_code)->toBe('ICHES27-P-0002')
        ->and($registration->submissions()->count())->toBe(2)
        ->and(Submission::query()->where('edition_id', $edition->id)->count())->toBe(2);
});

test('failed draft insert rolls back the paper sequence allocation', function () {
    ['edition' => $edition, 'user' => $user, 'registration' => $registration] = makeDraftSubmissionContext();

    Submission::query()->create([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-P-0001',
        'primary_locale' => Locale::English->value,
        'academic_status' => 'DRAFT',
    ]);

    expect(fn () => app(CreateDraftSubmissionAction::class)->handle($user, $edition, $registration, 'id'))
        ->toThrow(QueryException::class);

    expect(NumberSequence::query()->where('sequence_type', 'PAPER')->exists())->toBeFalse()
        ->and(Submission::query()->count())->toBe(1);
});

test('moved shared numbering allocator preserves registration and paper sequence isolation', function () {
    ['edition' => $edition] = makeDraftSubmissionContext();

    $numbering = new NextEditionNumberAction;

    expect($numbering->handle($edition, 'REGISTRATION', $edition->edition_code.'-REG-'))->toBe('ICHES27-REG-0001')
        ->and($numbering->handle($edition, 'PAPER', $edition->edition_code.'-P-'))->toBe('ICHES27-P-0001')
        ->and($numbering->handle($edition, 'REGISTRATION', $edition->edition_code.'-REG-'))->toBe('ICHES27-REG-0002');

    $registrationSequence = NumberSequence::query()
        ->where('edition_id', $edition->id)
        ->where('sequence_type', 'REGISTRATION')
        ->first();
    $paperSequence = NumberSequence::query()
        ->where('edition_id', $edition->id)
        ->where('sequence_type', 'PAPER')
        ->first();

    expect($registrationSequence->last_value)->toBe(2)
        ->and($paperSequence->last_value)->toBe(1);
});

test('paper numbering is isolated per conference edition', function () {
    $firstEdition = makeDraftSubmissionEdition('ICHES27');
    $firstPackage = makeDraftSubmissionPackage($firstEdition);
    ['user' => $firstUser, 'registration' => $firstRegistration] = makeDraftSubmissionRegistration(
        $firstEdition,
        $firstPackage,
    );

    $secondEdition = makeDraftSubmissionEdition('OTHER27');
    $secondPackage = makeDraftSubmissionPackage($secondEdition);
    ['user' => $secondUser, 'registration' => $secondRegistration] = makeDraftSubmissionRegistration(
        $secondEdition,
        $secondPackage,
    );

    $action = app(CreateDraftSubmissionAction::class);

    $firstSubmission = $action->handle($firstUser, $firstEdition, $firstRegistration, 'id');
    $secondSubmission = $action->handle($secondUser, $secondEdition, $secondRegistration, 'id');

    $firstSequence = NumberSequence::query()
        ->where('edition_id', $firstEdition->id)
        ->where('sequence_type', 'PAPER')
        ->first();
    $secondSequence = NumberSequence::query()
        ->where('edition_id', $secondEdition->id)
        ->where('sequence_type', 'PAPER')
        ->first();

    expect($firstSubmission->paper_code)->toBe('ICHES27-P-0001')
        ->and($secondSubmission->paper_code)->toBe('OTHER27-P-0001')
        ->and($firstSequence)->not->toBeNull()
        ->and($firstSequence->last_value)->toBe(1)
        ->and($secondSequence)->not->toBeNull()
        ->and($secondSequence->last_value)->toBe(1)
        ->and(NumberSequence::query()->where('sequence_type', 'PAPER')->count())->toBe(2);
});

test('invalid package context is rejected without consuming the paper sequence', function () {
    $editionA = makeDraftSubmissionEdition('ICHES27');
    $editionB = makeDraftSubmissionEdition('OTHER27');
    $packageB = makeDraftSubmissionPackage($editionB);

    ['user' => $user, 'registration' => $registration] = makeDraftSubmissionRegistration($editionA, $packageB);

    expect(fn () => app(CreateDraftSubmissionAction::class)->handle($user, $editionA, $registration, 'id'))
        ->toThrow(DomainException::class);

    expect(Submission::query()->count())->toBe(0)
        ->and(NumberSequence::query()
            ->where('edition_id', $editionA->id)
            ->where('sequence_type', 'PAPER')
            ->exists())->toBeFalse();
});
