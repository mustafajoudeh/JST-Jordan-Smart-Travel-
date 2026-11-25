<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Destination> */
class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        return [
            'governorate_id' => Governorate::factory(),
            'category_id' => Category::factory()->tourism(),
            'name' => Str::title(fake()->unique()->words(3, true)),
            'description' => fake()->paragraph(),
            'latitude' => fake()->randomFloat(6, 31.85, 32.05),
            'longitude' => fake()->randomFloat(6, 35.80, 36.05),
            'address' => fake()->optional()->streetAddress(),
            'suggested_duration_min' => fake()->randomElement([30, 60, 90, 120, 180]),
            'estimated_cost' => fake()->randomFloat(2, 0, 25),
            'opening_hours' => [
                'sun' => ['09:00', '17:00'],
                'mon' => ['09:00', '17:00'],
                'tue' => ['09:00', '17:00'],
                'wed' => ['09:00', '17:00'],
                'thu' => ['09:00', '17:00'],
                'fri' => null,
                'sat' => ['09:00', '17:00'],
            ],
            'tags' => fake()->randomElements(['family', 'history', 'views', 'walking', 'food', 'culture', 'photography'], 3),
            'source_type' => 'curated',
            'source_url' => null,
            'last_verified_at' => now()->toDateString(),
            'status' => 'published',
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

    public function official(): static
    {
        return $this->state([
            'source_type' => 'official',
            'source_url' => fake()->url(),
        ]);
    }
}