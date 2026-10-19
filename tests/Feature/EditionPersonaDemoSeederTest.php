<?php

use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\EditionPersonaDemoSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $series = ConferenceSeries::query()->create([
        'code' => 'ICHES', 'name' => 'ICHES Conference Series', 'status' => 'ACTIVE',
    ]);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id, 'edition_code' => 'ICHES-2027',
        'edition_number' => 6, 'year' => 2027, 'host_name' => 'UNISYA',
        'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta',
        'starts_at' => '2027-11-15 08:00:00',
        'ends_at' => '2027-11-16 18:00:00',
        'lifecycle_status' => 'DRAFT',
    ]);
});

test('demo fixtures are opt-in, never enabled by default', function () {
    config(['demo_roles.enabled' => false]);
    $this->seed(EditionPersonaDemoSeeder::class);
})->throws(RuntimeException::class, 'disabled');

test('demo fixture refuses nonlocal application environments', function () {
    app()->detectEnvironment(static fn (): string => 'production');
    config([
        'demo_roles.enabled' => true,
        'app.env' => 'production',
        'demo_roles.edition_code' => 'ICHES-2027',
        'demo_roles.password' => 'Demo#Conference2027!',
    ]);
    (new EditionPersonaDemoSeeder)->run();
})->throws(RuntimeException::class, 'prohibited');

test('creates edition-scoped roles and identities once without promoting superadmin or fabricating assignments', function () {
    config([
        'demo_roles.enabled' => true,
        'demo_roles.edition_code' => 'ICHES-2027',
        'demo_roles.password' => 'Demo#Conference2027!',
        'app.env' => 'testing',
    ]);
    $this->seed(EditionPersonaDemoSeeder::class);
    $this->seed(EditionPersonaDemoSeeder::class);

    $roles = array_keys(config('foundation.edition_role_blueprints'));
    expect(Role::query()->count())->toBe(count($roles))
        ->and(User::query()->count())->toBe(18)
        ->and(EditionMembership::query()->count())->toBe(6);

    $finance = User::query()
        ->where('email', 'demo+finance.iches-2027@example.test')->sole();
    expect($finance->isSuperAdmin())->toBeFalse()
        ->and(Hash::check('Demo#Conference2027!', $finance->password))->toBeTrue();
    setPermissionsTeamId($this->edition->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $finance->unsetRelation('roles')->unsetRelation('permissions');
    expect($finance->hasRole('finance'))->toBeTrue()
        ->and($finance->can('payment.verify'))->toBeTrue()
        ->and($finance->can('academic.decide'))->toBeFalse();

    $reviewer = User::query()
        ->where('email', 'demo+reviewer.iches-2027@example.test')->sole();
    expect($reviewer->getRoleNames())->toBeEmpty();
    setPermissionsTeamId(null);
    $finance->unsetRelation('roles')->unsetRelation('permissions');
    expect($finance->can('payment.verify'))->toBeFalse();
});

test('existing non-demo identity at reserved fixture address cannot be claimed', function () {
    User::factory()->create([
        'email' => 'demo+finance.iches-2027@example.test',
        'name' => 'Real Person',
    ]);
    config([
        'demo_roles.enabled' => true,
        'demo_roles.edition_code' => 'ICHES-2027',
        'demo_roles.password' => 'Demo#Conference2027!',
        'app.env' => 'testing',
    ]);
    $this->seed(EditionPersonaDemoSeeder::class);
})->throws(RuntimeException::class, 'collision');
