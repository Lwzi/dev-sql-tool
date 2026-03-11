<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SeedBindAdminRoleToUserSeeder extends NonProductionSeeder
{
    private const ROLE_NAME = 'admin';
    private const GUARD_NAME = 'web';
    private const USER_EMAIL = 'admin@qq.com';

    protected function seed(): void
    {
        $user = $this->resolveUser();
        $role = $this->resolveRole();

        if (! $user || ! $role) {
            return;
        }

        if (! $user->hasRole($role->name)) {
            $user->assignRole($role);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function resolveUser()
    {
        return User::query()->where('email', self::USER_EMAIL)->first();
    }

    private function resolveRole()
    {
        return Role::query()
            ->where('name', self::ROLE_NAME)
            ->where('guard_name', self::GUARD_NAME)
            ->first();
    }
}
