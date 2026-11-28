<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('PaymentSeeder skipped: the project has no payment gateway integration to create genuine payment records.');
    }
}
