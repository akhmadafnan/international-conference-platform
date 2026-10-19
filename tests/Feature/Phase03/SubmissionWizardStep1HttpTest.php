<?php

declare(strict_types=1);

use App\Actions\Submission\CreateDraftSubmissionAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function (): void {
    $this->withoutVite();
});

function p03iStepOneContext(): array
{
    $series = ConferenceSeries::query()->firstOrCreate(
        ['code' => 'ICHES-STEP1'],
        ['name' => 'ICHES', 'status' => 'ACTIVE'],
    );
    $edition = ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => 'STEP1-27',
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'UNISYA',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
        'settings_json' => ['abstract_submission' => [
            'min_keywords' => 3,
            'max_keywords' => 5,
            'track_required' => false,
            'abstract_file_required' => false,
        ]],
    ]);
    $package = ParticipationPackage::query()->create([
        'edition_id' => $edition->id,
        'code' => 'PRESENTER',
        'name_i18n' => ['id' => 'Pemakalah', 'en' => 'Presenter'],
        'billing_mode' => 'PAID',
        'price' => '750000.00',
        'currency_code' => 'IDR',
        'active' => true,
        'display_order' => 1,
    ]);
    $user = User::factory()->create(['email_verified_at' => now()]);
    $membership = EditionMembership::query()->create([
        'edition_id' => $edition->id,
        'user_id' => $user->id,
        'membership_status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    $registration = Registration::query()->create([
        'membership_id' => $membership->id,
        'registration_code' => 'STEP1-REG-'.Str::random(8),
        'package_id' => $package->id,
        'package_name_snapshot' => 'Presenter',
        'billing_mode' => 'PAID',
        'fee_amount' => '750000.00',
        'currency_code' => 'IDR',
        'status' => 'PAYMENT_PENDING',
    ]);

    return compact('edition', 'package', 'user', 'membership', 'registration');
}

test('submission UI requires authentication', function () {
    $this->get(route('submissions.index'))->assertRedirect();
    $this->post(route('submissions.store'))->assertRedirect();
});

test('author can see own eligible registration and create an unpaid draft', function () {
    ['user' => $user, 'registration' => $registration] = p03iStepOneContext();

    $this->actingAs($user)->get(route('submissions.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('submissions/Index')
            ->has('registrations', 1)->has('submissions', 0));

    $response = $this->actingAs($user)->post(route('submissions.store'), [
        'registration_id' => $registration->id,
        'primary_locale' => 'ar',
    ]);

    $draft = Submission::query()->sole();
    $response->assertRedirect(route('submissions.details', $draft));
    expect($draft->academic_status)->toBe('DRAFT')
        ->and($draft->getRawOriginal('primary_locale'))->toBe('ar')
        ->and($draft->registration_id)->toBe($registration->id);
});

test('foreign registration cannot create a draft', function () {
    ['registration' => $registration] = p03iStepOneContext();
    $stranger = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($stranger)->post(route('submissions.store'), [
        'registration_id' => $registration->id,
        'primary_locale' => 'id',
    ])->assertNotFound();

    expect(Submission::query()->count())->toBe(0);
});

test('invalid primary locale does not create a draft', function () {
    ['user' => $user, 'registration' => $registration] = p03iStepOneContext();

    $this->actingAs($user)->post(route('submissions.store'), [
        'registration_id' => $registration->id,
        'primary_locale' => 'fr',
    ])->assertSessionHasErrors('primary_locale');

    expect(Submission::query()->count())->toBe(0);
});

test('author can save Step 1 without paying and read it back', function () {
    ['user' => $user, 'edition' => $edition, 'registration' => $registration] = p03iStepOneContext();
    $draft = app(CreateDraftSubmissionAction::class)
        ->handle($user, $edition, $registration, 'id');

    $this->actingAs($user)->put(route('submissions.details.update', $draft), [
        'track_id' => null,
        'title' => 'Pembelajaran Berbasis Aset',
        'subtitle' => 'Konteks Konferensi',
        'abstract' => 'Penelitian ini menganalisis potensi aset komunitas.',
        'keywords_text' => "pendidikan\nmasyarakat\nABCD",
    ])->assertRedirect(route('submissions.details', $draft));

    expect($draft->fresh()->translations()->sole()->title)->toBe('Pembelajaran Berbasis Aset')
        ->and($draft->keywords()->orderBy('sequence')->pluck('value')->all())
        ->toBe(['pendidikan', 'masyarakat', 'ABCD']);

    $this->actingAs($user)->get(route('submissions.details', $draft))
        ->assertOk()->assertInertia(fn ($page) => $page
        ->component('submissions/Details')
        ->where('submission.title', 'Pembelajaran Berbasis Aset')
        ->where('submission.keywordsText', "pendidikan\nmasyarakat\nABCD"));
});

test('foreign user cannot inspect or mutate author Step 1', function () {
    ['user' => $user, 'edition' => $edition, 'registration' => $registration] = p03iStepOneContext();
    $draft = app(CreateDraftSubmissionAction::class)
        ->handle($user, $edition, $registration, 'id');
    $stranger = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($stranger)->get(route('submissions.details', $draft))->assertNotFound();
    $this->actingAs($stranger)->put(route('submissions.details.update', $draft), [
        'title' => 'Hacked', 'abstract' => 'Hacked', 'keywords_text' => 'foo',
    ])->assertNotFound();

    expect($draft->fresh()->translations()->count())->toBe(0);
});

test('submitted abstract is read-only in Step 1', function () {
    ['user' => $user, 'edition' => $edition, 'registration' => $registration] = p03iStepOneContext();
    $draft = app(CreateDraftSubmissionAction::class)
        ->handle($user, $edition, $registration, 'en');
    $draft->update(['academic_status' => 'SUBMITTED', 'submitted_at' => now()]);

    $this->actingAs($user)->get(route('submissions.details', $draft))
        ->assertOk()->assertInertia(fn ($page) => $page->where('submission.status', 'SUBMITTED'));

    $this->actingAs($user)->put(route('submissions.details.update', $draft), [
        'title' => 'New', 'abstract' => 'New', 'keywords_text' => 'x',
    ])->assertStatus(409);
});
