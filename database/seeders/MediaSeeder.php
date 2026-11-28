<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('MediaSeeder skipped: no local image files are available to link to media records.');
    }
}
