<?php

use App\Actions\Registration\CreateRegistrationIntentAction;
use App\Actions\Registration\GrantRegistrationFeeExemptionAction;
use App\Actions\Registration\ResolveRegistrationFeeRequirementAction;
use App\Enums\BillingMode;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Enums\WorkflowWindowCode;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionWorkflowWindow;
use App\Models\GeneratedDocument;
use App\Models\ParticipationPackage;
use App\Models\PaymentDestination;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
});

function makeParticipantBrowserEdition(): ConferenceEdition
{
    $series = ConferenceSeries::query()->create([
        'code' => 'ICHES-BROWSER',
        'name' => 'International Conference on Humanity Education and Society',
        'status' => 'ACTIVE',
    ]);

    return ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => 'ICHES27',
        'edition_number' => 6,
        'year' => 2027,
        'theme_i18n' => [
            'id' => 'Tema ICHES 2027',
            'en' => 'ICHES 2027 Theme',
            'ar' => 'موضوع ICHES 2027',
        ],
        'host_name' => 'Universitas Islam Syarifuddin Lumajang',
        'mode' => 'HYBRID',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);
}

function makeParticipantBrowserDestination(
    ConferenceEdition $edition,
): PaymentDestination {
    return PaymentDestination::query()->create([
        'edition_id' => $edition->id,
        'code' => 'BSI',
        'label' => 'Rekening ICHES',
        'bank_name' => 'Bank Syariah Indonesia',
        'account_number' => '7123456789',
        'account_holder' => 'Panitia ICHES 2027',
        'instructions_i18n' => [
            'id' => 'Transfer sesuai nominal lalu unggah bukti pembayaran.',
            'en' => 'Transfer the exact amount, then upload your payment proof.',
            'ar' => 'حوّل المبلغ المحدد ثم ارفع إثبات الدفع.',
        ],
        'is_default' => true,
        'active' => true,
        'display_order' => 1,
    ]);
}

function makeParticipantBrowserPackage(
    ConferenceEdition $edition,
    BillingMode $billingMode,
    string $code,
    string $price,
    ?PaymentDestination $destination = null,
    bool $active = true,
    int $displayOrder = 1,
): ParticipationPackage {
    return ParticipationPackage::query()->create([
        'edition_id' => $edition->id,
        'code' => $code,
        'name_i18n' => [
            'id' => 'Paket '.$code,
            'en' => $code.' Package',
            'ar' => 'باقة '.$code,
        ],
        'description_i18n' => [
            'id' => 'Paket partisipasi '.$code,
            'en' => $code.' participation package',
            'ar' => 'باقة المشاركة '.$code,
        ],
        'billing_mode' => $billingMode,
        'price' => $price,
        'currency_code' => 'IDR',
        'payment_destination_id' => $destination?->id,
        'active' => $active,
        'display_order' => $displayOrder,
    ]);
}

test('participant registration page exposes only active edition packages', function () {
    $edition = makeParticipantBrowserEdition();
    $participant = User::factory()->create();

    makeParticipantBrowserPackage(
        $edition,
        BillingMode::FREE,
        'FREE',
        '0.00',
        active: true,
        displayOrder: 1,
    );

    makeParticipantBrowserPackage(
        $edition,
        BillingMode::FREE,
        'HIDDEN',
        '0.00',
        active: false,
        displayOrder: 2,
    );

    $this->actingAs($participant)
        ->get(route('editions.registration.show', $edition))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('registration/Show')
                ->where('edition.code', 'ICHES27')
                ->has('packages', 1)
                ->where('packages.0.code', 'FREE')
                ->where('packages.0.billingMode', 'FREE')
                ->where('registration', null),
        );
});

test('free browser registration confirms immediately and exposes one Event Pass', function () {
    $edition = makeParticipantBrowserEdition();
    $package = makeParticipantBrowserPackage(
        $edition,
        BillingMode::FREE,
        'FREE',
        '0.00',
    );
    $participant = User::factory()->create([
        'name' => 'Akhmad Participant',
        'email' => 'participant-browser@example.test',
    ]);

    $this->actingAs($participant)
        ->post(route('editions.registration.store', $edition), [
            'package_id' => $package->id,
        ])
        ->assertStatus(303)
        ->assertRedirect(route('editions.registration.show', $edition));

    $registration = Registration::query()
        ->whereHas(
            'membership',
            fn ($query) => $query->where('user_id', $participant->id),
        )
        ->sole();

    $eventPass = $registration->generatedDocuments()
        ->where('document_type', GeneratedDocument::TYPE_EVENT_PASS)
        ->sole();

    $lookup = $eventPass->verificationTokens()->sole();

    expect($registration->status)->toBe(RegistrationStatus::CONFIRMED)
        ->and($registration->payments()->count())->toBe(0)
        ->and($eventPass->status)->toBe(GeneratedDocument::STATUS_ISSUED)
        ->and($lookup->public_code)->toStartWith('EP-')
        ->and($eventPass->snapshot_json)->not->toHaveKey('email');

    $this->actingAs($participant)
        ->get(route('editions.registration.show', $edition))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('registration/Show')
                ->where('registration.status', 'CONFIRMED')
                ->where('registration.billingMode', 'FREE')
                ->where('registration.complimentary', false)
                ->where('registration.nextAction', 'EVENT_PASS_AVAILABLE')
                ->where('registration.payment', null)
                ->where('registration.eventPass.publicCode', $lookup->public_code),
        );

    $this->actingAs($participant)
        ->get(route('documents.event-pass.show', $eventPass))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('documents/EventPass')
                ->where('eventPass.snapshot.registration_code', $registration->registration_code)
                ->where('eventPass.snapshot.participant_name', 'Akhmad Participant')
                ->where('eventPass.publicCode', $lookup->public_code),
        );

    $qrResponse = $this->actingAs($participant)
        ->get(route('documents.event-pass.qr', $eventPass));

    $qrResponse->assertOk()
        ->assertHeader('content-type', 'image/svg+xml; charset=UTF-8');

    expect($qrResponse->headers->get('cache-control'))
        ->toContain('private')
        ->toContain('no-store')
        ->toContain('max-age=0')
        ->and($qrResponse->getContent())
        ->not->toContain('participant-browser@example.test')
        ->not->toContain('Akhmad Participant');
});

test('paid browser flow supports proof correction finance verification and Event Pass', function () {
    Storage::fake('private');

    $edition = makeParticipantBrowserEdition();
    $destination = makeParticipantBrowserDestination($edition);
    $package = makeParticipantBrowserPackage(
        $edition,
        BillingMode::PAID,
        'FULL',
        '750000.00',
        $destination,
    );
    $participant = User::factory()->create();
    $finance = User::factory()->superAdmin()->create();

    $this->actingAs($participant)
        ->post(route('editions.registration.store', $edition), [
            'package_id' => $package->id,
        ])
        ->assertStatus(303);

    $registration = Registration::query()
        ->whereHas(
            'membership',
            fn ($query) => $query->where('user_id', $participant->id),
        )
        ->sole();
    $payment = $registration->payments()->sole();

    expect($registration->status)->toBe(RegistrationStatus::PAYMENT_PENDING)
        ->and($payment->status)->toBe(PaymentStatus::PENDING)
        ->and($payment->payment_destination_snapshot_json['account_number'])
        ->toBe('7123456789');

    $this->actingAs($participant)
        ->get(route('editions.registration.show', $edition))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('registration.nextAction', 'SUBMIT_PAYMENT_PROOF')
                ->where('registration.payment.status', 'PENDING')
                ->where(
                    'registration.payment.destination.instructions_i18n.en',
                    'Transfer the exact amount, then upload your payment proof.',
                ),
        );

    $this->actingAs($participant)
        ->post(route('payments.proofs.store', $payment), [
            'proof' => UploadedFile::fake()->create(
                'receipt.pdf',
                128,
                'application/pdf',
            ),
            'submitted_amount' => '750000.00',
            'sender_name' => 'Akhmad Afnan',
            'transfer_date' => now()->toDateString(),
        ])
        ->assertStatus(303);

    $payment->refresh();

    expect($payment->status)->toBe(PaymentStatus::SUBMITTED);

    $this->actingAs($participant)
        ->get(route('editions.registration.show', $edition))
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('registration.nextAction', 'WAIT_FINANCE_VERIFICATION')
                ->where('registration.payment.status', 'SUBMITTED'),
        );

    $this->actingAs($finance)
        ->post(
            route('payments.correction.store', $payment),
            ['reason' => 'Receipt image is not readable.'],
        )
        ->assertStatus(303);

    $payment->refresh();

    expect($payment->status)->toBe(PaymentStatus::CORRECTION_REQUIRED);

    $this->actingAs($participant)
        ->get(route('editions.registration.show', $edition))
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('registration.nextAction', 'REPLACE_PAYMENT_PROOF')
                ->where('registration.payment.status', 'CORRECTION_REQUIRED')
                ->where(
                    'registration.payment.correctionReason',
                    'Receipt image is not readable.',
                ),
        );

    $this->actingAs($participant)
        ->post(route('payments.proofs.store', $payment), [
            'proof' => UploadedFile::fake()->create(
                'replacement.pdf',
                128,
                'application/pdf',
            ),
            'submitted_amount' => '750000.00',
        ])
        ->assertStatus(303);

    $payment->refresh();

    expect($payment->status)->toBe(PaymentStatus::SUBMITTED)
        ->and($payment->proofs()->count())->toBe(2);

    $this->actingAs($finance)
        ->post(route('payments.verify', $payment))
        ->assertStatus(303);

    $registration->refresh();
    $payment->refresh();

    $eventPass = $registration->generatedDocuments()
        ->where('document_type', GeneratedDocument::TYPE_EVENT_PASS)
        ->sole();

    expect($payment->status)->toBe(PaymentStatus::VERIFIED)
        ->and($registration->status)->toBe(RegistrationStatus::CONFIRMED)
        ->and($eventPass->status)->toBe(GeneratedDocument::STATUS_ISSUED);

    $this->actingAs($participant)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('phase02.registration.status', 'CONFIRMED')
                ->where(
                    'phase02.registration.nextAction',
                    'EVENT_PASS_AVAILABLE',
                )
                ->has('phase02.registration.eventPass'),
        );
});

test('complimentary paid registration is visibly distinct and requires no payment', function () {
    $edition = makeParticipantBrowserEdition();
    $destination = makeParticipantBrowserDestination($edition);
    $package = makeParticipantBrowserPackage(
        $edition,
        BillingMode::PAID,
        'INVITED',
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

    app(GrantRegistrationFeeExemptionAction::class)->handle(
        $registration,
        $finance,
        'Invited participant approved by committee.',
        'INVITED',
    );

    app(ResolveRegistrationFeeRequirementAction::class)->handle($registration);

    $registration->refresh();

    expect($registration->status)->toBe(RegistrationStatus::CONFIRMED)
        ->and($registration->payments()->count())->toBe(0)
        ->and($registration->generatedDocuments()->count())->toBe(1);

    $this->actingAs($participant)
        ->get(route('editions.registration.show', $edition))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('registration.billingMode', 'PAID')
                ->where('registration.complimentary', true)
                ->where('registration.payment', null)
                ->where('registration.status', 'CONFIRMED')
                ->where('registration.nextAction', 'EVENT_PASS_AVAILABLE'),
        );
});

test('Event Pass page and QR are private to their recipient', function () {
    $edition = makeParticipantBrowserEdition();
    $package = makeParticipantBrowserPackage(
        $edition,
        BillingMode::FREE,
        'FREE',
        '0.00',
    );
    $participant = User::factory()->create();
    $otherParticipant = User::factory()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );
    app(ResolveRegistrationFeeRequirementAction::class)->handle($registration);

    $eventPass = $registration->generatedDocuments()->sole();

    $this->actingAs($otherParticipant)
        ->get(route('documents.event-pass.show', $eventPass))
        ->assertForbidden();

    $this->actingAs($otherParticipant)
        ->get(route('documents.event-pass.qr', $eventPass))
        ->assertForbidden();
});

test('Arabic participant flow keeps the server document in RTL mode', function () {
    $edition = makeParticipantBrowserEdition();
    $package = makeParticipantBrowserPackage(
        $edition,
        BillingMode::FREE,
        'FREE',
        '0.00',
    );
    $participant = User::factory()->create();

    $registration = app(CreateRegistrationIntentAction::class)->handle(
        $participant,
        $edition,
        $package,
    );
    app(ResolveRegistrationFeeRequirementAction::class)->handle($registration);
    $eventPass = $registration->generatedDocuments()->sole();

    $this->actingAs($participant)
        ->withCookie('locale', 'ar')
        ->get(route('editions.registration.show', $edition))
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('localization.locale', 'ar')
                ->where('localization.direction', 'rtl'),
        );

    $this->actingAs($participant)
        ->withCookie('locale', 'ar')
        ->get(route('documents.event-pass.show', $eventPass))
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('documents/EventPass')
                ->where('localization.locale', 'ar')
                ->where('localization.direction', 'rtl'),
        );
});

test('closed registration window returns a participant error without partial records', function () {
    $edition = makeParticipantBrowserEdition();
    $package = makeParticipantBrowserPackage(
        $edition,
        BillingMode::FREE,
        'FREE',
        '0.00',
    );
    $participant = User::factory()->create();

    EditionWorkflowWindow::query()->create([
        'edition_id' => $edition->id,
        'window_code' => WorkflowWindowCode::REGISTRATION->value,
        'opens_at' => now()->subDay(),
        'closes_at' => now()->subHour(),
        'active' => true,
    ]);

    $this->actingAs($participant)
        ->from(route('editions.registration.show', $edition))
        ->post(route('editions.registration.store', $edition), [
            'package_id' => $package->id,
        ])
        ->assertRedirect(route('editions.registration.show', $edition))
        ->assertSessionHasErrors([
            'package_id' => 'REGISTRATION_UNAVAILABLE',
        ]);

    expect(
        $edition->memberships()
            ->where('user_id', $participant->id)
            ->count(),
    )->toBe(0)
        ->and(Registration::query()->count())->toBe(0);
});

test('paid registration configuration failure rolls back participant membership and registration', function () {
    $edition = makeParticipantBrowserEdition();
    $package = makeParticipantBrowserPackage(
        $edition,
        BillingMode::PAID,
        'PAID-NO-DESTINATION',
        '500000.00',
    );
    $participant = User::factory()->create();

    $this->actingAs($participant)
        ->from(route('editions.registration.show', $edition))
        ->post(route('editions.registration.store', $edition), [
            'package_id' => $package->id,
        ])
        ->assertRedirect(route('editions.registration.show', $edition))
        ->assertSessionHasErrors([
            'package_id' => 'REGISTRATION_UNAVAILABLE',
        ]);

    expect(
        $edition->memberships()
            ->where('user_id', $participant->id)
            ->count(),
    )->toBe(0)
        ->and(Registration::query()->count())->toBe(0);
});