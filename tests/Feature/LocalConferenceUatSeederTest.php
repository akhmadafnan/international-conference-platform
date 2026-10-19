<?php

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\User;
use Database\Seeders\LocalConferenceUatSeeder;

test('isolated UAT environment opt-in and idempotent actual registration', function () {
    config([
        'demo_roles.enabled' => true,
        'demo_roles.password' => 'UatFixture!987654321',
        'app.env' => 'testing',
    ]);

    $this->seed(LocalConferenceUatSeeder::class);
    $this->seed(LocalConferenceUatSeeder::class);

    $edition = ConferenceEdition::query()
        ->where('edition_code', LocalConferenceUatSeeder::EDITION_CODE)->sole();

    expect($edition->settings_json['uat_only'])->toBeTrue()
        ->and(ConferenceSeries::query()->where('code', 'ICHES-UAT')->count())->toBe(1)
        ->and(ParticipationPackage::query()->where('edition_id', $edition->id)->count())->toBe(2)
        ->and($edition->workflowWindows()->count())->toBe(2)
        ->and(User::query()->count())->toBe(18)
        ->and(Registration::query()->count())->toBe(1);

    $registration = Registration::query()->sole();
    expect($registration->package->code)->toBe('PRESENTER-UAT')
        ->and($registration->membership->user->email)->toBe('demo+author.iches-uat-2027@example.test')
        ->and($registration->status->value)->toBe('PENDING');
});

test('UAT seeder cannot be run without explicit local opt-in', function () {
    config(['demo_roles.enabled' => false]);
    $this->seed(LocalConferenceUatSeeder::class);
})->throws(RuntimeException::class, 'opt-in');
