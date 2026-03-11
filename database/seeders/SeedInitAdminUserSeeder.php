<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SeedInitAdminUserSeeder extends NonProductionSeeder
{
    private string $name = 'Test Admin';
    private string $email = 'admin@qq.com';
    private string $plainPassword = 'password';

    protected function seed(): void
    {
        User::query()->firstOrCreate(
            ['email' => $this->email],
            [
                'name' => $this->name,
                'password' => Hash::make($this->plainPassword),
                'email_verified_at' => now(),
            ]
        );
    }
}
