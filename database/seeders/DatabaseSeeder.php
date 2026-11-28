<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('DatabaseSeeder only creates demo data in local or testing environments.');

            return;
        }

        DB::transaction(function (): void {
            $this->call([
                GovernorateSeeder::class,
                CategorySeeder::class,
                PlatformSettingSeeder::class,
                UserSeeder::class,
                DestinationSeeder::class,
                ActivitySeeder::class,
                EventSeeder::class,
                ProviderSeeder::class,
                ServiceSeeder::class,
                TripSeeder::class,
                TripItemsSeeder::class,
                ExperienceSeeder::class,
                FavoriteSeeder::class,
                UserInterestSeeder::class,
            ]);
        });
    }
}
