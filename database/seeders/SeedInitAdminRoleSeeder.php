<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SeedInitAdminRoleSeeder extends NonProductionSeeder
{
    private string $roleName = 'admin';
    private string $guardName = 'web';

    protected function seed(): void
    {
        Role::query()->firstOrCreate([
            'name' => $this->roleName,
            'guard_name' => $this->guardName,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
