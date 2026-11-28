<?php

namespace Database\Factories;

use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Governorate> */
class GovernorateFactory extends Factory
{
    protected $model = Governorate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Amman', 'Irbid', 'Ajloun', 'Jerash', 'Madaba', 'Karak',
                'Aqaba', 'Mafraq', 'Balqa', 'Tafilah', 'Ma’an', 'Zarqa',
            ]),
            'description' => 'A governorate in Jordan.',
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
