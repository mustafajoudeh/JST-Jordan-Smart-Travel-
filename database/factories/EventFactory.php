<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Event;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<Event> */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $start = Carbon::instance(fake()->dateTimeBetween('+1 week', '+3 months'));
        [$startTime, $endTime] = fake()->randomElement([
            ['09:00:00', '13:00:00'],
            ['17:00:00', '21:00:00'],
            ['19:00:00', '23:00:00'],
        ]);

        return [
            'governorate_id' => Governorate::factory(),
            'destination_id' => null,
            'name' => fake()->randomElement([
                'Draft demo: Amman arts evening', 'Draft demo: Jabal Amman food walk',
                'Draft demo: Jordanian heritage workshop', 'Draft demo: downtown music gathering',
                'Draft demo: Amman crafts exhibition',
            ]).' #'.fake()->unique()->numberBetween(1, 99999),
            'description' => 'Local demonstration event concept. Confirm the organizer, venue, dates, and source before publishing.',
            'event_type' => fake()->randomElement([
                'cultural',
                'festival',
                'exhibition',
                'food_art',
                'sports_entertainment',
                'national',
            ]),
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays(fake()->numberBetween(0, 3))->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'venue_name' => null,
            'latitude' => null,
            'longitude' => null,
            'source_type' => 'curated',
            'source_url' => null,
            'last_verified_at' => null,
            'status' => 'draft',
            'created_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state([
            'status' => 'draft',
            'source_url' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    public function publishedAtVenueWithVerifiedSource(string $sourceUrl, string $venueName): static
    {
        if (filter_var($sourceUrl, FILTER_VALIDATE_URL) === false || parse_url($sourceUrl, PHP_URL_SCHEME) !== 'https' || trim($venueName) === '') {
            throw new \InvalidArgumentException('Supply an HTTPS source verified by the caller and a real venue name.');
        }

        return $this->state([
            'status' => 'published',
            'source_url' => $sourceUrl,
            'venue_name' => $venueName,
            'last_verified_at' => now()->toDateString(),
        ]);
    }

    public function atDestination(Destination $destination): static
    {
        return $this->state([
            'governorate_id' => $destination->governorate_id,
            'destination_id' => $destination->id,
            'venue_name' => null,
            'latitude' => $destination->latitude,
            'longitude' => $destination->longitude,
        ]);
    }
}
