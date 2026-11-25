<?php

namespace Database\Factories;

use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PlatformSetting> */
class PlatformSettingFactory extends Factory
{
    protected $model = PlatformSetting::class;

    public function definition(): array
    {
        return [
            'key' => str_replace('-', '_', fake()->unique()->slug(3)),
            'value' => (string) fake()->numberBetween(1, 100),
            'value_type' => 'integer',
            'description' => fake()->optional()->sentence(),
            'updated_by' => null,
        ];
    }

    public function string(): static
    {
        return $this->state([
            'value' => fake()->word(),
            'value_type' => 'string',
        ]);
    }

    public function decimal(): static
    {
        return $this->state([
            'value' => '0.1000',
            'value_type' => 'decimal',
        ]);
    }

    public function boolean(): static
    {
        return $this->state([
            'value' => fake()->boolean() ? 'true' : 'false',
            'value_type' => 'boolean',
        ]);
    }

    public function updatedBy(User $user): static
    {
        return $this->state(['updated_by' => $user->id]);
    }
}