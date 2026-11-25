<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<Trip> */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        $start = Carbon::instance(fake()->dateTimeBetween('+1 week', '+2 months'));

        // Trip length is 3-7 days so seeded items on day 1-2 always fit.
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays(fake()->numberBetween(2, 6))->toDateString(),
            'travelers_count' => fake()->numberBetween(1, 6),
            'budget_level' => fake()->randomElement(['low', 'medium', 'high']),
            'group_type' => fake()->randomElement(['solo', 'couple', 'family', 'friends']),
            'starting_point' => fake()->optional()->city(),
            'is_ai_generated' => false,
            'status' => 'draft',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function saved(): static
    {
        return $this->state(['status' => 'saved']);
    }

    public function aiGenerated(): static
    {
        return $this->state(['is_ai_generated' => true]);
    }
}