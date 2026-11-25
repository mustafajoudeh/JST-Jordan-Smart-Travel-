<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Activity> */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'destination_id' => Destination::factory(),
            'category_id' => Category::factory()->tourism(),
            'name' => Str::title(fake()->unique()->words(3, true)),
            'description' => fake()->paragraph(),
            'duration_min' => fake()->randomElement([30, 45, 60, 90, 120]),
            'estimated_cost' => fake()->randomFloat(2, 0, 30),
            'available_times' => [
                'sun' => ['10:00', '16:00'],
                'mon' => ['10:00', '16:00'],
                'tue' => ['10:00', '16:00'],
                'wed' => ['10:00', '16:00'],
                'thu' => ['10:00', '16:00'],
                'fri' => null,
                'sat' => ['10:00', '16:00'],
            ],
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