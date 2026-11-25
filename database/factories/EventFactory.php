<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Event;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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

        // Default is a published event with venue + source (satisfies the publish CHECK).
        return [
            'governorate_id' => Governorate::factory(),
            'destination_id' => null,
            'name' => Str::title(fake()->unique()->words(3, true)),
            'description' => fake()->paragraph(),
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
            'venue_name' => fake()->company(),
            'latitude' => fake()->randomFloat(6, 31.85, 32.05),
            'longitude' => fake()->randomFloat(6, 35.80, 36.05),
            'source_type' => 'curated',
            'source_url' => fake()->url(),
            'last_verified_at' => now()->toDateString(),
            'status' => 'published',
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