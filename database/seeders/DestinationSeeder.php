<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        if (Destination::query()->exists()) {
            return;
        }

        $governorate = Governorate::where('name', 'Amman')->first();
        $categories = Category::where('kind', 'tourism')->pluck('id');

        if (! $governorate || $categories->isEmpty()) {
            $this->command?->warn('DestinationSeeder skipped: run GovernorateSeeder and CategorySeeder first.');

            return;
        }

        // Demo data only (lorem text). Real curated destinations come from the team.
        Destination::factory()
            ->count(12)
            ->state(fn () => [
                'governorate_id' => $governorate->id,
                'category_id' => $categories->random(),
            ])
            ->create();
    }
}