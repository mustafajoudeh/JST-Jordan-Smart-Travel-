<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (JstDemoCatalog::activities() as $destinationName => $activities) {
            $destination = Destination::where('name', $destinationName)
                ->whereHas('governorate', fn ($query) => $query->where('name', 'Amman'))
                ->first();

            if (! $destination) {
                $this->command?->warn("ActivitySeeder skipped {$destinationName}: destination not found.");

                continue;
            }

            $category = Category::where('kind', 'tourism')->whereKey($destination->category_id)->first();

            if (! $category) {
                $this->command?->warn("ActivitySeeder skipped {$destinationName}: its tourism category is missing.");

                continue;
            }

            foreach ($activities as $activity) {
                $destination->activities()->firstOrCreate(
                    ['name' => $activity['name']],
                    [
                        'category_id' => $category->id,
                        'description' => $activity['description'],
                        'duration_min' => $activity['duration'],
                        'estimated_cost' => $activity['cost'],
                        'available_times' => null,
                        'source_type' => 'curated',
                        'source_url' => null,
                        'last_verified_at' => null,
                        'status' => 'published',
                    ]
                );
            }
        }
    }
}
