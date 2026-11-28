<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Activity> */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'destination_id' => Destination::factory(),
            'category_id' => Category::factory()->tourism(),
            'name' => fake()->randomElement([
                'Explore the Umayyad Palace', 'Walk the Temple of Hercules ridge',
                'Visit the Roman Theatre', 'Walk Rainbow Street',
                'Explore Jordanian heritage galleries', 'Visit the contemporary art spaces',
                'Explore Jordan’s motoring history', 'Walk the Jabal Al-Weibdeh lanes',
                'Discover downtown Amman architecture', 'Take a historic city walk',
            ]).' demo activity '.fake()->unique()->numberBetween(1, 99999),
            'description' => 'A Jordan tourism activity awaiting editorial review.',
            'duration_min' => fake()->randomElement([30, 45, 60, 90, 120]),
            'estimated_cost' => fake()->randomFloat(2, 0, 30),
            'available_times' => null,
            'source_type' => 'curated',
            'source_url' => null,
            'last_verified_at' => null,
            'status' => 'draft',
            'created_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    public function published(): static
    {
        return $this->state(['status' => 'published']);
    }

    public function officialFromVerifiedSource(string $sourceUrl): static
    {
        if (filter_var($sourceUrl, FILTER_VALIDATE_URL) === false || parse_url($sourceUrl, PHP_URL_SCHEME) !== 'https') {
            throw new \InvalidArgumentException('An HTTPS source URL must be supplied and verified by the caller.');
        }

        return $this->state([
            'source_type' => 'official',
            'source_url' => $sourceUrl,
            'last_verified_at' => now()->toDateString(),
        ]);
    }
}
