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
        $groupType = fake()->randomElement(['solo', 'couple', 'family', 'friends']);
        $travelers = match ($groupType) {
            'solo' => 1,
            'couple' => 2,
            'family' => fake()->numberBetween(3, 5),
            default => fake()->numberBetween(3, 6),
        };

        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'A long weekend in Amman', 'Culture and food in the capital',
                'A family visit to Amman', 'A relaxed city break',
                'Friends explore old Amman',
            ]),
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays(fake()->numberBetween(1, 5))->toDateString(),
            'travelers_count' => $travelers,
            'budget_level' => fake()->randomElement(['low', 'medium', 'high']),
            'group_type' => $groupType,
            'starting_point' => 'Amman, Jordan',
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

    public function solo(): static
    {
        return $this->state(['group_type' => 'solo', 'travelers_count' => 1]);
    }

    public function couple(): static
    {
        return $this->state(['group_type' => 'couple', 'travelers_count' => 2]);
    }

    public function family(): static
    {
        return $this->state(['group_type' => 'family', 'travelers_count' => 4]);
    }

    public function friends(): static
    {
        return $this->state(['group_type' => 'friends', 'travelers_count' => 4]);
    }
}
