<?php

use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->withoutVite();
});

function rorFixture(): array
{
    return [
        'id' => 'https://ror.org/00ypgyy34',
        'status' => 'active',
        'names' => [
            ['value' => 'State University of Malang', 'types' => ['ror_display'], 'lang' => 'en'],
            ['value' => 'Universitas Negeri Malang', 'types' => ['label'], 'lang' => 'id'],
        ],
        'locations' => [
            ['geonames_details' => [
                'country_code' => 'ID', 'country_name' => 'Indonesia', 'name' => 'Malang',
            ]],
        ],
    ];
}

test('ROR search is authenticated and returns selectable canonical organizations', function () {
    $this->getJson(route('institutions.search', ['q' => 'Malang']))->assertUnauthorized();
    Http::fake([
        'api.ror.org/v2/organizations*' => Http::response(['items' => [rorFixture()]], 200),
    ]);
    $this->actingAs(User::factory()->create())
        ->getJson(route('institutions.search', ['q' => 'Universitas Malang']))
        ->assertOk()
        ->assertJsonPath('ror_available', true)
        ->assertJsonPath('manual_allowed', true)
        ->assertJsonPath('items.0.name', 'State University of Malang')
        ->assertJsonPath('items.0.ror_uri', 'https://ror.org/00ypgyy34');
    $this->assertDatabaseCount('institutions', 0);
});

test('ROR resolution verifies an official record server side, stores once, and ignores client names', function () {
    Http::fake([
        'api.ror.org/v2/organizations/00ypgyy34' => Http::response(rorFixture(), 200),
    ]);
    $actor = User::factory()->create();
    $this->actingAs($actor)->postJson(route('institutions.resolve'), [
        'ror_uri' => 'https://ror.org/00ypgyy34',
        'name' => 'Forged Client Name',
    ])->assertOk()->assertJsonPath('name', 'State University of Malang');

    $this->actingAs($actor)->postJson(route('institutions.resolve'), [
        'ror_uri' => 'https://ror.org/00ypgyy34',
    ])->assertOk();
    $this->assertDatabaseCount('institutions', 1);
    $this->assertDatabaseHas('institutions', [
        'preferred_name' => 'State University of Malang',
        'ror_uri' => 'https://ror.org/00ypgyy34',
        'country_code' => 'ID',
        'source' => 'ROR',
    ]);
});

test('invalid ROR IDs are rejected and user-provided external URLs are never fetched', function () {
    Http::fake();
    $this->actingAs(User::factory()->create())->postJson(route('institutions.resolve'), [
        'ror_uri' => 'http://localhost/admin?fetch=secrets',
    ])->assertUnprocessable()->assertJsonValidationErrors('ror_uri');
    Http::assertNothingSent();
});

test('ROR outage does not prevent a truthful manual institution label', function () {
    Http::fake(['api.ror.org/*' => Http::response([], 503)]);
    $this->actingAs(User::factory()->create())
        ->getJson(route('institutions.search', ['q' => 'Universitas Lumajang']))
        ->assertOk()
        ->assertJsonPath('manual_allowed', true)
        ->assertJsonPath('ror_available', false);
    $this->assertDatabaseCount('institutions', 0);
});

test('local ROR institution is available even when the public API fails', function () {
    Institution::query()->create([
        'preferred_name' => 'Sample Research University',
        'country_code' => 'ID',
        'ror_uri' => 'https://ror.org/00ypgyy34',
        'source' => 'ROR',
    ]);
    Http::fake(['api.ror.org/*' => Http::response([], 503)]);
    $this->actingAs(User::factory()->create())
        ->getJson(route('institutions.search', ['q' => 'Research University']))
        ->assertOk()
        ->assertJsonPath('items.0.name', 'Sample Research University')
        ->assertJsonPath('items.0.source', 'local');
});
