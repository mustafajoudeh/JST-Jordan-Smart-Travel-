<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Event;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        if (Event::query()->exists()) {
            return;
        }

        $governorate = Governorate::where('name', 'Amman')->first();

        if (! $governorate) {
            $this->command?->warn('EventSeeder skipped: run GovernorateSeeder first.');

            return;
        }

        // Venue-based published events.
        Event::factory()
            ->count(6)
            ->state(['governorate_id' => $governorate->id])
            ->create();

        // Events at a known destination.
        Destination::where('status', 'published')->inRandomOrder()->limit(2)->get()
            ->each(fn (Destination $destination) => Event::factory()->atDestination($destination)->create());
    }
}