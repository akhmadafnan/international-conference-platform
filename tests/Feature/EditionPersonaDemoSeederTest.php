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
        'demo_roles.password' => null,
    ]);
    (new EditionPersonaDemoSeeder)->run();
})->throws(RuntimeException::class, 'prohibited');

test('creates edition-scoped roles and identities once without promoting superadmin or fabricating assignments', function () {
    config([
        'demo_roles.enabled' => true,
        'demo_roles.edition_code' => 'ICHES-2027',
        'demo_roles.password' => null,
        'app.env' => 'testing',
    ]);
    $this->seed(EditionPersonaDemoSeeder::class);
    $this->seed(EditionPersonaDemoSeeder::class);

    $roles = array_keys(config('foundation.edition_role_blueprints'));
    expect(Role::query()->count())->toBe(count($roles))
        ->and(User::query()->count())->toBe(18)
        ->and(EditionMembership::query()->count())->toBe(6);

    foreach (User::query()->get() as $user) {
        expect(str_ends_with($user->email, '.iches-2027@iches.test'))->toBeTrue()
            ->and(Hash::check('password', $user->password))->toBeTrue();
    }

    $finance = User::query()
        ->where('email', 'demo+finance.iches-2027@iches.test')->sole();
    expect($finance->isSuperAdmin())->toBeFalse()
        ->and(Hash::check('password', $finance->password))->toBeTrue();
    setPermissionsTeamId($this->edition->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $finance->unsetRelation('roles')->unsetRelation('permissions');
    expect($finance->hasRole('finance'))->toBeTrue()
        ->and($finance->can('payment.verify'))->toBeTrue()
        ->and($finance->can('academic.decide'))->toBeFalse();

    $reviewer = User::query()
        ->where('email', 'demo+reviewer.iches-2027@iches.test')->sole();
    expect($reviewer->getRoleNames())->toBeEmpty();
    setPermissionsTeamId(null);
    $finance->unsetRelation('roles')->unsetRelation('permissions');
    expect($finance->can('payment.verify'))->toBeFalse();
});

test('existing non-demo identity at reserved fixture address cannot be claimed', function () {
    User::factory()->create([
        'email' => 'demo+finance.iches-2027@iches.test',
        'name' => 'Real Person',
    ]);
    config([
        'demo_roles.enabled' => true,
        'demo_roles.edition_code' => 'ICHES-2027',
        'demo_roles.password' => null,
        'app.env' => 'testing',
    ]);
    $this->seed(EditionPersonaDemoSeeder::class);
})->throws(RuntimeException::class, 'collision');

test('another Edition has separate demo identities and cannot inherit finance permissions', function () {
    config(['demo_roles.enabled' => true, 'demo_roles.edition_code' => 'ICHES-2027']);
    $this->seed(EditionPersonaDemoSeeder::class);
    $finance = User::query()->where('email', 'demo+finance.iches-2027@iches.test')->sole();
    $other = $this->edition->replicate();
    $other->edition_code = 'ICHES-2029';
    $other->year = 2029;
    $other->save();
    config(['demo_roles.edition_code' => 'ICHES-2029']);
    $this->seed(EditionPersonaDemoSeeder::class);

    expect(User::query()->count())->toBe(36)
        ->and(User::query()->where('email', 'demo+finance.iches-2029@iches.test')->sole()->id)->not->toBe($finance->id);
    setPermissionsTeamId($other->id);
    $finance->unsetRelation('roles')->unsetRelation('permissions');
    expect($finance->hasRole('finance'))->toBeFalse()
        ->and($finance->can('payment.verify'))->toBeFalse();
    setPermissionsTeamId(null);
});

test('a superadmin at a demo target address is never modified even with a matching demo marker', function () {
    config(['demo_roles.enabled' => true, 'demo_roles.edition_code' => 'ICHES-2027']);
    $superadmin = User::factory()->create([
        'name' => 'ICHES Demo · finance', 'email' => 'demo+finance.iches-2027@iches.test',
        'is_super_admin' => true,
    ]);
    $before = $superadmin->fresh()->getRawOriginal();

    expect(fn () => $this->seed(EditionPersonaDemoSeeder::class))->toThrow(RuntimeException::class, 'collision');
    expect($superadmin->fresh()->getRawOriginal())->toBe($before)
        ->and(User::query()->count())->toBe(1)
        ->and(Role::query()->count())->toBe(0);
});
