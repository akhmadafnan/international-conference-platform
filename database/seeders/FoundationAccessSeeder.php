<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use RuntimeException;
use Spatie\Permission\PermissionRegistrar;

class FoundationAccessSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = config('foundation.permissions', []);

        if (! is_array($permissions)) {
            throw new RuntimeException(
                'Foundation permission configuration must be an array.',
            );
        }

        $permissionRegistrar = app(PermissionRegistrar::class);

        $permissionRegistrar->forgetCachedPermissions();

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $permissionRegistrar->forgetCachedPermissions();
    }
}
