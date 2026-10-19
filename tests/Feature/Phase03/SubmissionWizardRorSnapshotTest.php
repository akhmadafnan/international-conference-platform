<?php

use App\Actions\Submission\CreateDraftSubmissionAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\ContributorAffiliation;
use App\Models\EditionMembership;
use App\Models\Institution;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\User;

beforeEach(function () {
    $this->withoutVite();
    $series = ConferenceSeries::query()->create(['code' => 'ROR-TEST', 'name' => 'ROR Test', 'status' => 'ACTIVE']);
    $edition = ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => 'ROR-TEST-2027',
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'UNISYA',
        'mode' => 'HYBRID',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);
    $package = ParticipationPackage::query()->create([
        'edition_id' => $edition->id,
        'code' => 'PRESENTER',
        'name_i18n' => ['id' => 'Presenter'],
        'billing_mode' => 'PAID',
        'price' => '500000.00',
        'currency_code' => 'IDR',
        'active' => true,
        'display_order' => 1,
    ]);
    $this->actor = User::factory()->create();
    $membership = EditionMembership::query()->create([
        'edition_id' => $edition->id,
        'user_id' => $this->actor->id,
        'membership_status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    $registration = Registration::query()->create([
        'membership_id' => $membership->id,
        'registration_code' => 'ROR-REG-TEST',
        'package_id' => $package->id,
        'package_name_snapshot' => 'Presenter',
        'billing_mode' => 'PAID',
        'fee_amount' => '500000.00',
        'currency_code' => 'IDR',
        'status' => 'PENDING',
    ]);
    $this->draft = app(CreateDraftSubmissionAction::class)->handle($this->actor, $edition, $registration, 'id');
});

test('ROR selection saves canonical institution name and ROR URI into an author affiliation snapshot', function () {
    $institution = Institution::query()->create([
        'preferred_name' => 'State University of Malang',
        'ror_uri' => 'https://ror.org/00ypgyy34',
        'country_code' => 'ID',
        'source' => 'ROR',
    ]);
    $this->actingAs($this->actor)->put(route('submissions.contributors.update', $this->draft), [
        'contributors' => [[
            'id' => null,
            'display_name' => 'Sample Author',
            'given_name' => 'Sample',
            'family_name' => 'Author',
            'single_name' => false,
            'email' => 'sample.author@example.test',
            'country_code' => 'ID',
            'orcid' => null,
            'is_corresponding' => true,
            'affiliations' => [[
                'institution_id' => $institution->id,
                'institution_name' => null,
                'subdivision' => 'Faculty of Education',
                'city' => 'Malang',
                'country_code' => null,
            ]],
        ]],
    ])->assertSessionHasNoErrors();

    $aff = ContributorAffiliation::query()->sole();
    expect($aff->institution_id)->toBe($institution->id)
        ->and($aff->institution_name_snapshot)->toBe('State University of Malang')
        ->and($aff->ror_uri_snapshot)->toBe('https://ror.org/00ypgyy34')
        ->and($aff->subdivision_text)->toBe('Faculty of Education')
        ->and($aff->country_code)->toBe('ID');
});
