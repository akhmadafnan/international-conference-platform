<?php

use App\Actions\Payment\ConfigurePaymentDestinationAction;
use App\Actions\Payment\RequestPaymentCorrectionAction;
use App\Actions\Payment\SubmitPaymentProofAction;
use App\Actions\Payment\VerifyPaymentAction;
use App\Actions\Registration\ConfigureParticipationPackageAction;
use App\Actions\Registration\CreateRegistrationIntentAction;
use App\Actions\Registration\GrantRegistrationFeeExemptionAction;
use App\Actions\Registration\ResolveRegistrationFeeRequirementAction;
use App\Enums\BillingMode;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Enums\WorkflowWindowCode;
use App\Models\Activity;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionWorkflowWindow;
use App\Models\PackageActivityEntitlement;
use App\Models\ParticipationPackage;
use App\Models\NumberSequence;
use App\Models\PaymentDestination;
use App\Models\Registration;
use App\Models\RegistrationActivity;
use App\Models\Permission;
use App\Models\Role;
use App\Models\StoredFile;
use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Carbon\CarbonInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function makePhase02Edition(): ConferenceEdition
{
    $series = ConferenceSeries::query()->create([
        'code' => 'ICHES',
        'name' => 'International Conference on Humanity Education and Society',
        'status' => 'ACTIVE',
    ]);

    return ConferenceEdition::query()->create([
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
}

function makePhase02Package(
    ConferenceEdition $edition,
    BillingMode $billingMode,
    string $code = 'CONF',
    string $price = '0.00',
    ?PaymentDestination $destination = null,
): ParticipationPackage {
    return ParticipationPackage::query()->create([
        'edition_id' => $edition->id,
        'code' => $code,
        'name_i18n' => [
            'id' => 'Paket '.$code,
            'en' => $code.' Package',
        ],
        'billing_mode' => $billingMode,
        'price' => $price,
        'currency_code' => 'IDR',
        'payment_destination_id' => $destination?->id,
        'active' => true,
        'display_order' => 1,
    ]);
}

function makePhase02Destination(
    ConferenceEdition $edition,
    bool $default = true,
): PaymentDestination {
    return PaymentDestination::query()->create([
        'edition_id' => $edition->id,
        'code' => 'BSI',
        'label' => 'Rekening ICHES',
        'bank_name' => 'Bank Syariah Indonesia',
        'account_number' => '7123456789',
        'account_holder' => 'Panitia ICHES 2027',
        'is_default' => $default,
        'active' => true,
        'display_order' => 1,
    ]);
}

function makePhase02WorkflowWindow(
    ConferenceEdition $edition,
    WorkflowWindowCode $code = WorkflowWindowCode::REGISTRATION,
    ?CarbonInterface $opensAt = null,
    ?CarbonInterface $closesAt = null,
    bool $active = true,
): EditionWorkflowWindow {
    return EditionWorkflowWindow::query()->create([
        'edition_id' => $edition->id,
        'window_code' => $code->value,
        'opens_at' => $opensAt,
        'closes_at' => $closesAt,
        'active' => $active,
    ]);
}

afterEach(function (): void {
    Carbon::setTestNow();
});

test('phase 02 first class domain records use UUIDv7 identifiers', function () {
    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );

    expect(Str::isUuid($edition->id, 7))->toBeTrue()
        ->and(Str::isUuid($destination->id, 7))->toBeTrue()
        ->and(Str::isUuid($package->id, 7))->toBeTrue();
});

test('free registration resolves without synthetic payment and snapshots entitlements', function () {
    $edition = makePhase02Edition();
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    $activity = Activity::query()->create([
        'edition_id' => $edition->id,
        'code' => 'MAIN',
        'name_i18n' => ['id' => 'Konferensi Utama'],
        'activity_type' => 'CONFERENCE',
        'attendance_required' => true,
        'active' => true,
    ]);

    PackageActivityEntitlement::query()->create([
        'package_id' => $package->id,
        'activity_id' => $activity->id,
        'entitlement_type' => 'INCLUDED',
    ]);

    $user = User::factory()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $user,
        $edition,
        $package,
    );

    expect($registration->status)->toBe(RegistrationStatus::PENDING)
        ->and($registration->billing_mode)->toBe(BillingMode::FREE)
        ->and($registration->activities()->count())->toBe(1)
        ->and($registration->payments()->count())->toBe(0);

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $registration->refresh();

    expect($payment)->toBeNull()
        ->and($registration->status)->toBe(RegistrationStatus::CONFIRMED)
        ->and($registration->confirmed_at)->not->toBeNull()
        ->and($registration->payments()->count())->toBe(0);
});

test('paid registration creates immutable payment snapshots from configured destination', function () {
    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $user = User::factory()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $user,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe(PaymentStatus::PENDING)
        ->and($payment->expected_amount)->toBe('750000.00')
        ->and($payment->payment_destination_snapshot_json['account_number'])
        ->toBe('7123456789');

    $package->update([
        'price' => '900000.00',
        'name_i18n' => ['id' => 'Paket Diubah'],
    ]);

    $destination->update([
        'account_number' => '9999999999',
    ]);

    $payment->refresh();

    expect($payment->expected_amount)->toBe('750000.00')
        ->and($payment->package_name_snapshot)->toBe('Paket FULL')
        ->and($payment->payment_destination_snapshot_json['account_number'])
        ->toBe('7123456789');
});

test('paid package may use the active edition default destination', function () {
    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition, true);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'CONF',
        '500000.00',
        null,
    );
    $user = User::factory()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $user,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    expect($payment?->payment_destination_id)->toBe($destination->id);
});

test('complimentary exemption confirms paid registration without payment', function () {
    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'GUEST',
        '500000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $admin = User::factory()->superAdmin()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );

    $exemption = app(GrantRegistrationFeeExemptionAction::class)->handle(
        $registration,
        $admin,
        'Invited keynote speaker',
        'INVITED_GUEST',
    );

    expect($exemption->revoked_at)->toBeNull()
        ->and($registration->payments()->count())->toBe(0);

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $registration->refresh();

    expect($payment)->toBeNull()
        ->and($registration->status)->toBe(RegistrationStatus::CONFIRMED)
        ->and($registration->payments()->count())->toBe(0);
});

test('fee exemption refuses submitted financial evidence', function () {
    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $admin = User::factory()->superAdmin()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $payment?->forceFill([
        'status' => PaymentStatus::SUBMITTED,
        'submitted_at' => now(),
    ])->save();

    expect(fn () => app(GrantRegistrationFeeExemptionAction::class)->handle(
        $registration,
        $admin,
        'Late complimentary request',
    ))->toThrow(DomainException::class);
});

test('payment proof correction creates a new version without overwriting old evidence', function () {
    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $finance = User::factory()->superAdmin()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $file1 = StoredFile::query()->create([
        'disk' => 'private',
        'path' => 'payment-proofs/'.Str::uuid7().'.pdf',
        'original_name' => 'proof-1.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1234,
        'checksum_sha256' => str_repeat('a', 64),
        'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $participant->id,
    ]);

    $proof1 = app(SubmitPaymentProofAction::class)->handle(
        $payment,
        $file1,
        $participant,
        '750000.00',
        'Akhmad Afnan',
        now(),
    );

    app(RequestPaymentCorrectionAction::class)->handle(
        $payment,
        $finance,
        'Transfer receipt is not readable.',
    );

    $file2 = StoredFile::query()->create([
        'disk' => 'private',
        'path' => 'payment-proofs/'.Str::uuid7().'.pdf',
        'original_name' => 'proof-2.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 2345,
        'checksum_sha256' => str_repeat('b', 64),
        'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $participant->id,
    ]);

    $proof2 = app(SubmitPaymentProofAction::class)->handle(
        $payment,
        $file2,
        $participant,
        '750000.00',
        'Akhmad Afnan',
        now(),
    );

    $proof1->refresh();
    $payment->refresh();

    expect($proof1->version)->toBe(1)
        ->and($proof1->superseded_at)->not->toBeNull()
        ->and($proof2->version)->toBe(2)
        ->and($proof2->superseded_at)->toBeNull()
        ->and($payment->status)->toBe(PaymentStatus::SUBMITTED)
        ->and($payment->proofs()->count())->toBe(2);
});

test('finance verification confirms registration while preserving payment evidence', function () {
    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $finance = User::factory()->superAdmin()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $file = StoredFile::query()->create([
        'disk' => 'private',
        'path' => 'payment-proofs/'.Str::uuid7().'.pdf',
        'original_name' => 'proof.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1234,
        'checksum_sha256' => str_repeat('c', 64),
        'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $participant->id,
    ]);

    app(SubmitPaymentProofAction::class)->handle(
        $payment,
        $file,
        $participant,
        '750000.00',
        'Akhmad Afnan',
        now(),
    );

    $verified = app(VerifyPaymentAction::class)->handle(
        $payment,
        $finance,
    );

    $registration->refresh();

    expect($verified->status)->toBe(PaymentStatus::VERIFIED)
        ->and($verified->verified_by_user_id)->toBe($finance->id)
        ->and($verified->proofs()->count())->toBe(1)
        ->and($registration->status)->toBe(RegistrationStatus::CONFIRMED)
        ->and($registration->confirmed_at)->not->toBeNull();
});

test('package configuration makes free and paid behavior explicit', function () {
    $edition = makePhase02Edition();
    $admin = User::factory()->superAdmin()->create();

    $destination = app(ConfigurePaymentDestinationAction::class)->handle(
        $edition,
        $admin,
        [
            'code' => 'BSI',
            'label' => 'Rekening ICHES',
            'bank_name' => 'Bank Syariah Indonesia',
            'account_number' => '7123456789',
            'account_holder' => 'Panitia ICHES 2027',
            'is_default' => true,
            'active' => true,
        ],
    );

    $freePackage = app(ConfigureParticipationPackageAction::class)->handle(
        $edition,
        $admin,
        [
            'code' => 'FREE',
            'name_i18n' => ['id' => 'Partisipan Gratis'],
            'billing_mode' => BillingMode::FREE,
            'price' => '250000.00',
            'payment_destination_id' => $destination->id,
            'active' => true,
        ],
    );

    $paidPackage = app(ConfigureParticipationPackageAction::class)->handle(
        $edition,
        $admin,
        [
            'code' => 'PAID',
            'name_i18n' => ['id' => 'Partisipan Berbayar'],
            'billing_mode' => BillingMode::PAID,
            'price' => '500000.00',
            'active' => true,
        ],
    );

    expect($freePackage->price)->toBe('0.00')
        ->and($freePackage->payment_destination_id)->toBeNull()
        ->and($paidPackage->price)->toBe('500000.00')
        ->and($paidPackage->payment_destination_id)->toBeNull();
});

test('registration intent rejects package from another edition', function () {
    $editionA = makePhase02Edition();

    $seriesB = ConferenceSeries::query()->create([
        'code' => 'OTHER',
        'name' => 'Other Conference',
        'status' => 'ACTIVE',
    ]);

    $editionB = ConferenceEdition::query()->create([
        'series_id' => $seriesB->id,
        'edition_code' => 'OTHER27',
        'year' => 2027,
        'host_name' => 'Other Host',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);

    $package = makePhase02Package(
        $editionB,
        BillingMode::FREE,
        'FREE',
        '0.00',
    );

    expect(fn () => app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $editionA,
        $package,
    ))->toThrow(DomainException::class);
});

test('operator actions enforce server side capabilities', function () {
    $edition = makePhase02Edition();
    $normalUser = User::factory()->create();

    expect(fn () => app(ConfigurePaymentDestinationAction::class)->handle(
        $edition,
        $normalUser,
        [
            'code' => 'BSI',
            'label' => 'Rekening ICHES',
            'bank_name' => 'Bank Syariah Indonesia',
            'account_number' => '7123456789',
            'account_holder' => 'Panitia ICHES 2027',
            'is_default' => true,
        ],
    ))->toThrow(AuthorizationException::class);
});

test('edition scoped operator cannot mutate another edition with permission from active edition', function () {
    $editionA = makePhase02Edition();

    $seriesB = ConferenceSeries::query()->create([
        'code' => 'ICHES-B',
        'name' => 'ICHES Secondary Edition Series',
        'status' => 'ACTIVE',
    ]);

    $editionB = ConferenceEdition::query()->create([
        'series_id' => $seriesB->id,
        'edition_code' => 'ICHES28',
        'year' => 2028,
        'host_name' => 'Universitas Islam Syarifuddin Lumajang',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addYear(),
        'ends_at' => now()->addYear()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);

    $operator = User::factory()->create();

    Permission::query()->create([
        'name' => 'payment.configure',
        'guard_name' => 'web',
    ]);

    setPermissionsTeamId($editionA->id);

    $role = Role::query()->create([
        'name' => 'conference_admin',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo('payment.configure');
    $operator->assignRole($role);
    $operator->unsetRelation('roles')->unsetRelation('permissions');

    app(ActiveConferenceEditionContext::class)->activate($editionA->id);

    try {
        expect(fn () => app(ConfigurePaymentDestinationAction::class)->handle(
            $editionB,
            $operator,
            [
                'code' => 'BSI',
                'label' => 'Rekening ICHES',
                'bank_name' => 'Bank Syariah Indonesia',
                'account_number' => '7123456789',
                'account_holder' => 'Panitia ICHES 2028',
                'is_default' => true,
            ],
        ))->toThrow(AuthorizationException::class);
    } finally {
        app(ActiveConferenceEditionContext::class)->clear();
        setPermissionsTeamId(null);
    }
});

test('registered participant can upload payment proof into protected storage through HTTP', function () {
    Storage::fake('private');

    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $response = $this->actingAs($participant)->post(
        route('payments.proofs.store', $payment),
        [
            'proof' => UploadedFile::fake()->create(
                'receipt.pdf',
                128,
                'application/pdf',
            ),
            'submitted_amount' => '750000.00',
            'sender_name' => 'Akhmad Afnan',
            'transfer_date' => now()->toDateString(),
        ],
    );

    $response->assertStatus(303);

    $payment?->refresh();
    $proof = $payment?->proofs()->sole();
    $storedFile = $proof?->storedFile;

    expect($payment?->status)->toBe(PaymentStatus::SUBMITTED)
        ->and($storedFile)->not->toBeNull()
        ->and($storedFile?->disk)->toBe('private')
        ->and($storedFile?->visibility_class)->toBe('PRIVATE')
        ->and($storedFile?->original_name)->toBe('receipt.pdf')
        ->and($storedFile?->checksum_sha256)->toHaveLength(64)
        ->and($storedFile?->path)->not->toContain('receipt');

    Storage::disk('private')->assertExists($storedFile?->path);
});

test('another user cannot upload payment proof for a registration they do not own', function () {
    Storage::fake('private');

    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $otherUser = User::factory()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $this->actingAs($otherUser)->post(
        route('payments.proofs.store', $payment),
        [
            'proof' => UploadedFile::fake()->create(
                'receipt.pdf',
                128,
                'application/pdf',
            ),
            'submitted_amount' => '750000.00',
        ],
    )->assertForbidden();

    expect($payment?->proofs()->count())->toBe(0)
        ->and(StoredFile::query()->count())->toBe(0);
});

test('participant can download own protected payment proof but another participant cannot', function () {
    Storage::fake('private');

    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $otherUser = User::factory()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $this->actingAs($participant)->post(
        route('payments.proofs.store', $payment),
        [
            'proof' => UploadedFile::fake()->create(
                'receipt.pdf',
                128,
                'application/pdf',
            ),
            'submitted_amount' => '750000.00',
        ],
    )->assertStatus(303);

    $proof = $payment?->proofs()->sole();

    $this->actingAs($participant)->get(
        route('payments.proofs.download', [
            'payment' => $payment,
            'proof' => $proof,
        ]),
    )->assertOk()
        ->assertHeader('content-disposition');

    $this->actingAs($otherUser)->get(
        route('payments.proofs.download', [
            'payment' => $payment,
            'proof' => $proof,
        ]),
    )->assertForbidden();
});

test('participant cannot download a payment proof through a different payment they also own', function () {
    Storage::fake('private');

    $participant = User::factory()->create();

    $editionA = makePhase02Edition();
    $destinationA = makePhase02Destination($editionA);
    $packageA = makePhase02Package(
        $editionA,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destinationA,
    );

    $registrationA = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $editionA,
        $packageA,
    );

    $paymentA = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registrationA);

    $seriesB = ConferenceSeries::query()->create([
        'code' => 'ICHES-B',
        'name' => 'ICHES Secondary Edition Series',
        'status' => 'ACTIVE',
    ]);

    $editionB = ConferenceEdition::query()->create([
        'series_id' => $seriesB->id,
        'edition_code' => 'ICHES28',
        'year' => 2028,
        'host_name' => 'Other Host',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addYear(),
        'ends_at' => now()->addYear()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);

    $destinationB = makePhase02Destination($editionB);
    $packageB = makePhase02Package(
        $editionB,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destinationB,
    );

    $registrationB = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $editionB,
        $packageB,
    );

    $paymentB = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registrationB);

    $this->actingAs($participant)->post(
        route('payments.proofs.store', $paymentA),
        [
            'proof' => UploadedFile::fake()->create(
                'receipt-a.pdf',
                128,
                'application/pdf',
            ),
            'submitted_amount' => '750000.00',
        ],
    )->assertStatus(303);

    $proofA = $paymentA?->proofs()->sole();
    $storedFile = $proofA?->storedFile;

    expect($paymentA?->id)->not->toBe($paymentB?->id)
        ->and($proofA?->payment_id)->toBe($paymentA?->id)
        ->and($storedFile)->not->toBeNull();

    $this->actingAs($participant)->get(
        route('payments.proofs.download', [
            'payment' => $paymentB,
            'proof' => $proofA,
        ]),
    )->assertForbidden();

    Storage::disk('private')->assertExists($storedFile?->path);
});

test('finance can verify submitted payment through active edition HTTP context', function () {
    Storage::fake('private');

    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $finance = User::factory()->create();

    Permission::query()->create([
        'name' => 'payment.verify',
        'guard_name' => 'web',
    ]);

    setPermissionsTeamId($edition->id);

    $role = Role::query()->create([
        'name' => 'finance',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo('payment.verify');
    $finance->assignRole($role);

    setPermissionsTeamId(null);
    $finance->unsetRelation('roles')->unsetRelation('permissions');

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $this->actingAs($participant)->post(
        route('payments.proofs.store', $payment),
        [
            'proof' => UploadedFile::fake()->create(
                'receipt.pdf',
                128,
                'application/pdf',
            ),
            'submitted_amount' => '750000.00',
        ],
    )->assertStatus(303);

    $this->actingAs($finance)
        ->withSession([
            ActiveConferenceEditionContext::SESSION_KEY => $edition->id,
        ])
        ->post(route('payments.verify', $payment))
        ->assertStatus(303);

    $payment?->refresh();
    $registration->refresh();

    expect($payment?->status)->toBe(PaymentStatus::VERIFIED)
        ->and($payment?->verified_by_user_id)->toBe($finance->id)
        ->and($registration->status)->toBe(RegistrationStatus::CONFIRMED);
});

test('finance correction endpoint preserves submitted proof and requests correction', function () {
    Storage::fake('private');

    $edition = makePhase02Edition();
    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $finance = User::factory()->create();

    Permission::query()->create([
        'name' => 'payment.verify',
        'guard_name' => 'web',
    ]);

    setPermissionsTeamId($edition->id);

    $role = Role::query()->create([
        'name' => 'finance',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo('payment.verify');
    $finance->assignRole($role);

    setPermissionsTeamId(null);
    $finance->unsetRelation('roles')->unsetRelation('permissions');

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );
    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    $this->actingAs($participant)->post(
        route('payments.proofs.store', $payment),
        [
            'proof' => UploadedFile::fake()->create(
                'receipt.pdf',
                128,
                'application/pdf',
            ),
            'submitted_amount' => '750000.00',
        ],
    )->assertStatus(303);

    $this->actingAs($finance)
        ->withSession([
            ActiveConferenceEditionContext::SESSION_KEY => $edition->id,
        ])
        ->post(
            route('payments.correction.store', $payment),
            ['reason' => 'Receipt image is not readable.'],
        )->assertStatus(303);

    $payment?->refresh();

    expect($payment?->status)->toBe(PaymentStatus::CORRECTION_REQUIRED)
        ->and($payment?->correction_reason)
        ->toBe('Receipt image is not readable.')
        ->and($payment?->proofs()->count())->toBe(1);
});

test('registration workflow is unrestricted when no registration window row exists', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    );

    expect($registration)->toBeInstanceOf(Registration::class)
        ->and($registration->status)->toBe(RegistrationStatus::PENDING);
});

test('inactive registration workflow window does not enforce even with an invalid range', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::REGISTRATION,
        now()->addHour(),
        now()->subHour(),
        false,
    );
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    );

    expect($registration)->toBeInstanceOf(Registration::class);
});

test('registration workflow with null opens at has no lower bound', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::REGISTRATION,
        null,
        now()->addMinute(),
    );
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    expect(fn () => app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    ))->not->toThrow(DomainException::class);
});

test('registration workflow with null closes at has no upper bound', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::REGISTRATION,
        now()->subMinute(),
        null,
    );
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    expect(fn () => app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    ))->not->toThrow(DomainException::class);
});

test('registration workflow rejects registration before opens at without business side effects', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::REGISTRATION,
        now()->addMinute(),
        now()->addHour(),
    );
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    $activity = Activity::query()->create([
        'edition_id' => $edition->id,
        'code' => 'MAIN',
        'name_i18n' => ['id' => 'Konferensi Utama'],
        'activity_type' => 'CONFERENCE',
        'attendance_required' => true,
        'active' => true,
    ]);

    PackageActivityEntitlement::query()->create([
        'package_id' => $package->id,
        'activity_id' => $activity->id,
        'entitlement_type' => 'INCLUDED',
    ]);

    expect(fn () => app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    ))->toThrow(
        DomainException::class,
        'The REGISTRATION workflow window is not open yet.',
    );

    expect($edition->memberships()->count())->toBe(0)
        ->and(Registration::query()->count())->toBe(0)
        ->and(NumberSequence::query()->count())->toBe(0)
        ->and(RegistrationActivity::query()->count())->toBe(0)
        ->and(DB::table('activity_log')->where('log_name', 'registration')->count())->toBe(0);
});

test('registration workflow allows the exact opens at boundary', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::REGISTRATION,
        now(),
        now()->addHour(),
    );
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    expect(fn () => app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    ))->not->toThrow(DomainException::class);
});

test('registration workflow allows the exact closes at boundary', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::REGISTRATION,
        now()->subHour(),
        now(),
    );
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    expect(fn () => app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    ))->not->toThrow(DomainException::class);
});

test('registration workflow rejects registration after closes at', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::REGISTRATION,
        now()->subHour(),
        now()->subMinute(),
    );
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    expect(fn () => app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    ))->toThrow(
        DomainException::class,
        'The REGISTRATION workflow window is closed.',
    );
});

test('active registration workflow rejects invalid opens at and closes at configuration', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::REGISTRATION,
        now()->addHour(),
        now(),
    );
    $package = makePhase02Package($edition, BillingMode::FREE, 'FREE', '0.00');

    expect(fn () => app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    ))->toThrow(
        DomainException::class,
        'Active REGISTRATION workflow window configuration is invalid: opens_at must be earlier than or equal to closes_at.',
    );

    expect($edition->memberships()->count())->toBe(0)
        ->and(Registration::query()->count())->toBe(0)
        ->and(NumberSequence::query()->count())->toBe(0);
});

test('registration workflow window remains scoped to its conference edition', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $editionA = makePhase02Edition();
    makePhase02WorkflowWindow(
        $editionA,
        WorkflowWindowCode::REGISTRATION,
        now()->addHour(),
        now()->addHours(2),
    );

    $seriesB = ConferenceSeries::query()->create([
        'code' => 'OTHER-WINDOW',
        'name' => 'Other Window Conference',
        'status' => 'ACTIVE',
    ]);

    $editionB = ConferenceEdition::query()->create([
        'series_id' => $seriesB->id,
        'edition_code' => 'OTHER-WINDOW-27',
        'year' => 2027,
        'host_name' => 'Other Host',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);

    $packageB = makePhase02Package($editionB, BillingMode::FREE, 'FREE-B', '0.00');

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $editionB,
        $packageB,
    );

    expect($registration->membership->edition_id)->toBe($editionB->id);
});

test('participant only paid path does not consume accepted author payment window', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-04 10:00:00'));

    $edition = makePhase02Edition();
    makePhase02WorkflowWindow(
        $edition,
        WorkflowWindowCode::ACCEPTED_AUTHOR_PAYMENT,
        now()->addDay(),
        now()->addDays(2),
    );

    $destination = makePhase02Destination($edition);
    $package = makePhase02Package(
        $edition,
        BillingMode::PAID,
        'PARTICIPANT',
        '500000.00',
        $destination,
    );

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        User::factory()->create(),
        $edition,
        $package,
    );

    $payment = app(ResolveRegistrationFeeRequirementAction::class)
        ->handle($registration);

    expect($payment)->not->toBeNull()
        ->and($payment?->status)->toBe(PaymentStatus::PENDING)
        ->and($registration->refresh()->status)->toBe(RegistrationStatus::PAYMENT_PENDING);
});

