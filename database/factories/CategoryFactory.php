<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'kind' => 'tourism',
            'name' => Str::title(fake()->unique()->words(2, true)),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function tourism(): static
    {
        return $this->state(['kind' => 'tourism']);
    }

    public function service(): static
    {
        return $this->state(['kind' => 'service']);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}