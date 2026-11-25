<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Experience> */
class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraphs(2, true),
            'rating' => fake()->numberBetween(1, 5),
            'social_url' => fake()->optional(0.3)->passthrough('https://www.instagram.com/p/' . fake()->bothify('???########')),
            'tags' => fake()->randomElements(['family', 'food', 'history', 'views', 'budget', 'sunset'], 2),
            'status' => 'pending',
            'moderated_by' => null,
            'moderated_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'moderated_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'moderated_at' => now(),
        ]);
    }

    public function hidden(): static
    {
        return $this->state([
            'status' => 'hidden',
            'moderated_at' => now(),
        ]);
    }
}