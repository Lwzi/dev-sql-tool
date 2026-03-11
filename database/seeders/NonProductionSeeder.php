<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

abstract class NonProductionSeeder extends Seeder
{
    final public function run(): void
    {
        if ($this->shouldSkip()) {
            return;
        }

        $this->seed();
    }

    protected function shouldSkip(): bool
    {
        return app()->environment('production');
    }

    abstract protected function seed(): void;
}
