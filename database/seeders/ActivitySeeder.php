<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        if (Activity::query()->exists()) {
            return;
        }

        $categories = Category::where('kind', 'tourism')->pluck('id');
        $destinations = Destination::where('status', 'published')->get();

        if ($categories->isEmpty() || $destinations->isEmpty()) {
            $this->command?->warn('ActivitySeeder skipped: no tourism categories or published destinations.');

            return;
        }

        foreach ($destinations as $destination) {
            Activity::factory()
                ->count(2)
                ->state(fn () => [
                    'destination_id' => $destination->id,
                    'category_id' => $categories->random(),
                ])
                ->create();
        }
    }
}