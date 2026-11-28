<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $amman = Governorate::where('name', 'Amman')->first();

        if (! $amman) {
            $this->command?->warn('DestinationSeeder skipped: the Amman governorate is missing.');

            return;
        }

        foreach (JstDemoCatalog::destinations() as $data) {
            $category = Category::where('kind', 'tourism')->where('name', $data['category'])->first();

            if (! $category) {
                $this->command?->warn("DestinationSeeder skipped {$data['name']}: tourism category not found.");

                continue;
            }

            $amman->destinations()->firstOrCreate(
                ['name' => $data['name']],
                [
                    'category_id' => $category->id,
                    'description' => $data['description'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'address' => $data['address'],
                    'suggested_duration_min' => $data['duration'],
                    'estimated_cost' => $data['cost'],
                    'opening_hours' => null,
                    'tags' => $data['tags'],
                    'source_type' => 'curated',
                    'source_url' => null,
                    'last_verified_at' => null,
                    'status' => 'published',
                ]
            );
        }
    }
}
