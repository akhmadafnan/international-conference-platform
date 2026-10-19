<?php

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use App\Support\Registration\ParticipantRegistrationView;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
    $series = ConferenceSeries::query()->create(['code' => 'ACTION', 'name' => 'Conference', 'status' => 'ACTIVE']);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => 'ICHES-UAT-2027', 'year' => 2027,
        'host_name' => 'Host', 'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(), 'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
        'settings_json' => ['abstract_submission' => ['min_keywords' => 2, 'max_keywords' => 7, 'track_required' => false, 'abstract_file_required' => false]],
    ]);
    $this->actor = User::factory()->create();
    $membership = EditionMembership::query()->create([
        'edition_id' => $this->edition->id, 'user_id' => $this->actor->id,
        'membership_status' => 'ACTIVE', 'joined_at' => now(),
    ]);
    $this->package = ParticipationPackage::query()->create([
        'edition_id' => $this->edition->id, 'code' => 'PRESENTER-UAT',
        'name_i18n' => ['en' => 'Presenter'], 'billing_mode' => 'PAID', 'price' => '750000.00', 'currency_code' => 'IDR', 'active' => true,
    ]);
    $this->registration = Registration::query()->create([
        'membership_id' => $membership->id, 'registration_code' => 'REG-ACTION',
        'package_id' => $this->package->id, 'package_name_snapshot' => 'Presenter',
        'billing_mode' => 'PAID', 'fee_amount' => '750000.00', 'currency_code' => 'IDR', 'status' => 'PENDING',
    ]);
});

test('paid pending registration without payment is not free and offers abstract creation', function () {
    $this->actingAs($this->actor)->get(route('dashboard'))->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('phase02.registration.billingMode', 'PAID')
            ->where('phase02.registration.billingState', 'AFTER_ACCEPT')
            ->where('phase02.registration.feeAmount', '750000.00')
            ->where('phase02.registration.complimentary', false)
            ->where('phase02.registration.payment', null)
            ->where('phase02.registration.nextAction', 'SUBMIT_ABSTRACT')
            ->where('phase02.registration.nextActionUrl', route('submissions.index'))
            ->where('phase02.registration.eventPass', null));
    $this->get(route('editions.registration.show', $this->edition))->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('registration.billingState', 'AFTER_ACCEPT')
            ->where('registration.payment', null));
    $this->assertDatabaseCount('payments', 0);
    $this->assertDatabaseHas('registrations', ['id' => $this->registration->id, 'status' => 'PENDING']);
});

test('academic next action reflects draft submitted and accepted states without creating payment', function (string $status, string $expected) {
    $submission = Submission::query()->create([
        'edition_id' => $this->edition->id, 'registration_id' => $this->registration->id,
        'paper_code' => 'P-1', 'primary_locale' => 'id', 'academic_status' => $status,
        'accepted_at' => $status === 'ACCEPTED' ? now() : null,
    ]);
    $view = app(ParticipantRegistrationView::class)->build($this->registration);
    expect($view['nextAction'])->toBe($expected)
        ->and($view['academicStatus'])->toBe($status)
        ->and($view['payment'])->toBeNull();
    if (in_array($status, ['DRAFT', 'SUBMITTED'], true)) {
        expect($view['nextActionUrl'])->toBe(route('submissions.details', $submission))
            ->and($view['billingState'])->toBe('AFTER_ACCEPT');
    } else {
        expect($view['nextActionUrl'])->toBeNull()->and($view['billingState'])->toBe('AWAITING_PAYMENT_SETUP');
    }
    $this->assertDatabaseCount('payments', 0);
})->with([
    ['DRAFT', 'CONTINUE_ABSTRACT'], ['SUBMITTED', 'WAIT_ACADEMIC_REVIEW'], ['ACCEPTED', 'WAIT_PAYMENT_SETUP'],
]);

test('an accepted author sees only the existing real payment obligation', function () {
    Submission::query()->create([
        'edition_id' => $this->edition->id, 'registration_id' => $this->registration->id,
        'paper_code' => 'P-1', 'primary_locale' => 'id', 'academic_status' => 'ACCEPTED', 'accepted_at' => now(),
    ]);
    Payment::query()->create([
        'registration_id' => $this->registration->id, 'package_id_snapshot' => $this->package->id,
        'package_name_snapshot' => 'Presenter', 'expected_amount' => '750000.00', 'currency_code' => 'IDR',
        'payment_destination_snapshot_json' => [], 'status' => 'PENDING',
    ]);
    $view = app(ParticipantRegistrationView::class)->build($this->registration);
    expect($view['nextAction'])->toBe('SUBMIT_PAYMENT_PROOF')
        ->and($view['nextActionUrl'])->toBe(route('editions.registration.show', $this->edition))
        ->and($view['billingState'])->toBe('PAYMENT_RECORDED');
    $this->assertDatabaseCount('payments', 1);
});

test('free and waived registrations have distinct truthful billing states', function (bool $waived) {
    if ($waived) {
        $this->registration->feeExemptions()->create([
            'reason_code' => 'INVITED', 'reason_text' => 'Invited author', 'granted_at' => now(), 'granted_by_user_id' => $this->actor->id,
        ]);
    } else {
        $this->registration->update(['billing_mode' => 'FREE', 'fee_amount' => '0.00']);
    }
    $view = app(ParticipantRegistrationView::class)->build($this->registration->fresh());
    expect($view['billingState'])->toBe($waived ? 'COMPLIMENTARY' : 'FREE')
        ->and($view['complimentary'])->toBe($waived);
    $this->assertDatabaseCount('payments', 0);
})->with([true, false]);

test('cancelled registration never offers an academic or payment action', function () {
    $this->registration->update(['status' => 'CANCELLED']);
    $view = app(ParticipantRegistrationView::class)->build($this->registration);
    expect($view['nextAction'])->toBe('REGISTRATION_CANCELLED')->and($view['nextActionUrl'])->toBeNull();
});
