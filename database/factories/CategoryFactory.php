<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'kind' => 'tourism',
            'name' => fake()->randomElement([
                'Historical', 'Cultural', 'Religious', 'Nature', 'Adventure',
                'Food', 'Relaxation', 'Family Activities',
            ]).' Demo '.fake()->unique()->numberBetween(1, 99999),
            'description' => null,
            'is_active' => true,
        ];
    }

    public function tourism(): static
    {
        return $this->state(fn () => [
            'kind' => 'tourism',
            'name' => fake()->randomElement([
                'Historical', 'Cultural', 'Religious', 'Nature', 'Adventure',
                'Food', 'Relaxation', 'Family Activities',
            ]).' Demo '.fake()->unique()->numberBetween(1, 99999),
        ]);
    }

    public function service(): static
    {
        return $this->state(fn () => [
            'kind' => 'service',
            'name' => fake()->randomElement([
                'Guided Tours', 'Cultural Experiences', 'Food Experiences',
                'City Tours', 'Activities', 'Transportation',
            ]).' Demo '.fake()->unique()->numberBetween(1, 99999),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
