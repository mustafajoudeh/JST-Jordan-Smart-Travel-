<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'default_commission_rate',
                'value' => '0.1500',
                'value_type' => 'decimal',
                'description' => 'Default platform commission rate (0 to 1).',
            ],
            [
                'key' => 'default_currency',
                'value' => 'JOD',
                'value_type' => 'string',
                'description' => 'Default currency for prices and bookings.',
            ],
            [
                'key' => 'free_cancellation_hours',
                'value' => '24',
                'value_type' => 'integer',
                'description' => 'Hours before the booking date during which cancellation is free.',
            ],
            [
                'key' => 'booking_requires_provider_approval',
                'value' => 'true',
                'value_type' => 'boolean',
                'description' => 'Whether the provider must confirm a booking (pending to confirmed).',
            ],
        ];

        
        DB::table('platform_settings')->insertOrIgnore($settings);
    }
}