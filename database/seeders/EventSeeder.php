<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $amman = Governorate::where('name', 'Amman')->first();

        if (! $amman) {
            $this->command?->warn('EventSeeder skipped: the Amman governorate is missing.');

            return;
        }

        $drafts = [
            ['name' => 'Draft demo: Amman arts evening', 'event_type' => 'cultural', 'date' => '2027-04-15'],
            ['name' => 'Draft demo: Jabal Amman food walk', 'event_type' => 'food_art', 'date' => '2027-05-20'],
            ['name' => 'Draft demo: Jordanian heritage workshop', 'event_type' => 'exhibition', 'date' => '2027-06-12'],
        ];

        foreach ($drafts as $draft) {
            $amman->events()->firstOrCreate(
                ['name' => $draft['name'], 'start_date' => $draft['date']],
                [
                    'description' => 'Local development event concept. Confirm the real organizer, venue, dates, and a verifiable source before publishing.',
                    'event_type' => $draft['event_type'],
                    'end_date' => $draft['date'],
                    'start_time' => null,
                    'end_time' => null,
                    'venue_name' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'source_type' => 'curated',
                    'source_url' => null,
                    'last_verified_at' => null,
                    'status' => 'draft',
                ]
            );
        }
    }
}
