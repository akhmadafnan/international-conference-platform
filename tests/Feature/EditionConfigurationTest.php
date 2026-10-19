<?php

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->withoutVite();
    $series = ConferenceSeries::query()->create([
        'code' => 'ICHES', 'name' => 'ICHES', 'status' => 'ACTIVE',
    ]);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => 'ICHES-2027',
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'UNISYA',
        'mode' => 'HYBRID',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => '2027-11-15 08:00:00',
        'ends_at' => '2027-11-16 18:00:00',
        'lifecycle_status' => 'DRAFT',
    ]);
    $this->destination = [
        'code' => 'BANK_MAIN',
        'label' => 'ICHES official transfer',
        'bank_name' => 'Demo Bank',
        'account_number' => '99900001234',
        'account_holder' => 'ICHES',
        'is_default' => true,
        'active' => true,
        'display_order' => 0,
    ];
    $this->package = [
        'code' => 'PARTICIPANT',
        'name_i18n' => ['id' => 'Peserta', 'en' => 'Participant', 'ar' => 'مشارك'],
        'billing_mode' => 'FREE',
        'price' => '0',
        'currency_code' => 'IDR',
        'payment_destination_id' => null,
        'active' => true,
        'display_order' => 0,
    ];
});

test('configuration routes require verified superadmin and do not mutate guest data', function (string $actor) {
    if ($actor === 'ordinary') {
        $this->actingAs(User::factory()->create());
    } elseif ($actor === 'unverified') {
        $this->actingAs(User::factory()->superAdmin()->unverified()->create());
    }

    $responses = [
        $this->get(route('admin.editions.configuration', $this->edition)),
        $this->post(route('admin.editions.destinations.store', $this->edition), $this->destination),
        $this->post(route('admin.editions.packages.store', $this->edition), $this->package),
    ];
    foreach ($responses as $response) {
        if ($actor === 'ordinary') {
            $response->assertForbidden();
        } else {
            $response->assertRedirect(route($actor === 'guest' ? 'login' : 'verification.notice'));
        }
    }
    $this->assertDatabaseCount('payment_destinations', 0);
    $this->assertDatabaseCount('participation_packages', 0);
})->with(['guest', 'unverified', 'ordinary']);

test('superadmin gets real scoped edition configuration with account number masked', function () {
    $actor = User::factory()->superAdmin()->create();
    $destination = $this->edition->paymentDestinations()->create($this->destination);
    $this->actingAs($actor)->get(route('admin.editions.configuration', $this->edition))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/editions/Configuration')
            ->where('edition.code', 'ICHES-2027')
            ->has('destinations', 1)
            ->where('destinations.0.accountLast4', '1234')
            ->missing('destinations.0.account_number')
            ->has('packages', 0));
    expect($destination->edition_id)->toBe($this->edition->id);
});

test('can configure a default destination and free multilingual package without generating a payment', function () {
    $actor = User::factory()->superAdmin()->create();
    $this->actingAs($actor)->post(route('admin.editions.destinations.store', $this->edition), $this->destination)
        ->assertSessionHasNoErrors()->assertRedirect(route('admin.editions.configuration', $this->edition));
    $this->actingAs($actor)->post(route('admin.editions.packages.store', $this->edition), $this->package)
        ->assertSessionHasNoErrors()->assertRedirect(route('admin.editions.configuration', $this->edition));
    $this->assertDatabaseHas('payment_destinations', [
        'edition_id' => $this->edition->id, 'code' => 'BANK_MAIN', 'is_default' => true,
    ]);
    $this->assertDatabaseHas('participation_packages', [
        'edition_id' => $this->edition->id, 'code' => 'PARTICIPANT', 'price' => 0,
        'payment_destination_id' => null,
    ]);
    expect(Activity::where('event', 'payment_destination_created')->count())->toBe(1);
    expect(Activity::where('event', 'participation_package_created')->count())->toBe(1);
    expect(Activity::where('event', 'participation_package_created')->first()->properties->toArray())
        ->not->toHaveKey('account_number');
});

test('active paid package requires active edition destination and preserves rollback on invalid configuration', function () {
    $actor = User::factory()->superAdmin()->create();
    $paid = array_replace($this->package, [
        'code' => 'PAID', 'billing_mode' => 'PAID', 'price' => '250000.00',
    ]);

    $this->actingAs($actor)->post(route('admin.editions.packages.store', $this->edition), $paid)
        ->assertSessionHasErrors('configuration');
    $this->assertDatabaseCount('participation_packages', 0);

    $this->edition->paymentDestinations()->create($this->destination);
    $this->actingAs($actor)->post(route('admin.editions.packages.store', $this->edition), $paid)
        ->assertSessionHasNoErrors();
    $this->assertDatabaseHas('participation_packages', [
        'edition_id' => $this->edition->id, 'code' => 'PAID',
        'price' => '250000', 'currency_code' => 'IDR',
    ]);
});

test('invalid price and duplicate package codes are rejected without additional records', function () {
    $actor = User::factory()->superAdmin()->create();
    $this->actingAs($actor)->post(route('admin.editions.packages.store', $this->edition), $this->package)
        ->assertSessionHasNoErrors();
    $this->actingAs($actor)->post(route('admin.editions.packages.store', $this->edition), $this->package)
        ->assertSessionHasErrors('code');
    $this->actingAs($actor)->post(route('admin.editions.packages.store', $this->edition), array_replace(
        $this->package, ['code' => 'BROKEN', 'price' => '-10', 'billing_mode' => 'PAID'],
    ))->assertSessionHasErrors('price');
    $this->assertDatabaseCount('participation_packages', 1);
});

test('foreign edition payment destination cannot be injected into inactive packages', function () {
    $actor = User::factory()->superAdmin()->create();
    $other = ConferenceEdition::query()->create(array_replace(
        $this->edition->only(['series_id', 'edition_code', 'edition_number', 'year', 'host_name', 'mode', 'timezone', 'starts_at', 'ends_at', 'lifecycle_status']),
        ['edition_code' => 'ICHES-2028', 'year' => 2028],
    ));
    $foreign = $other->paymentDestinations()->create($this->destination);
    $invalid = array_replace($this->package, [
        'code' => 'FOREIGN', 'billing_mode' => 'PAID', 'price' => '100',
        'active' => false, 'payment_destination_id' => $foreign->id,
    ]);
    $this->actingAs($actor)->post(route('admin.editions.packages.store', $this->edition), $invalid)
        ->assertSessionHasErrors('payment_destination_id');
    $this->assertDatabaseCount('participation_packages', 0);
});
