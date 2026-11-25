<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reference data and optional admin: every environment.
        $this->call([
            GovernorateSeeder::class,
            CategorySeeder::class,
            PlatformSettingSeeder::class,
            UserSeeder::class,
        ]);

        // Demo data: local/testing only. Order follows foreign-key dependencies.
        if (app()->environment(['local', 'testing'])) {
            $this->call([
                DestinationSeeder::class,
                ActivitySeeder::class,
                EventSeeder::class,
                UserInterestSeeder::class,
                ProviderSeeder::class,
                ServiceSeeder::class,
                ExperienceSeeder::class,
                FavoriteSeeder::class,
                MediaSeeder::class,
                TripSeeder::class,
                BookingSeeder::class,
                TripItemsSeeder::class,   // after bookings (trip_items.booking_id)
                PaymentSeeder::class,    // after bookings
                RefreshTokenSeeder::class,
            ]);
        }
    }
}