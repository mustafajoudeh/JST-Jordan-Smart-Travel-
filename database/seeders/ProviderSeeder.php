<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Governorate;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        if (Provider::query()->exists()) {
            return;
        }

        $governorate = Governorate::where('name', 'Amman')->first();
        $categories = Category::where('kind', 'service')->pluck('id');

        if (! $governorate || $categories->isEmpty()) {
            $this->command?->warn('ProviderSeeder skipped: run GovernorateSeeder and CategorySeeder first.');

            return;
        }

        $location = fn () => [
            'governorate_id' => $governorate->id,
            'category_id' => $categories->random(),
        ];

        Provider::factory()->bookable()->count(2)->state($location)->create();   // approved companies that accept bookings
        Provider::factory()->guide()->approved()->state($location)->create();    // approved guide, no bookings
        Provider::factory()->state($location)->create();                         // pending company
    }
}