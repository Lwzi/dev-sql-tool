<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SeedInitTestUsersSeeder extends NonProductionSeeder
{
    private const USER_COUNT = 100;
    private const PASSWORD = 'password';

    protected function seed(): void
    {
        $password = Hash::make(self::PASSWORD);

        for ($index = 1; $index <= self::USER_COUNT; $index++) {
            User::query()->firstOrCreate(
                ['email' => $this->email($index)],
                [
                    'name' => $this->name($index),
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );
        }
    }

    private function name(int $index): string
    {
        return sprintf('Test User %03d', $index);
    }

    private function email(int $index): string
    {
        return sprintf('test-user-%03d@example.com', $index);
    }
}
