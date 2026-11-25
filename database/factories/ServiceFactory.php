<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'destination_id' => Destination::factory(),
            'category_id' => Category::factory()->service(),
            'name' => Str::title(fake()->words(3, true)),
            'description' => fake()->paragraph(),
            'duration_min' => fake()->randomElement([60, 90, 120, 180, 240]),
            'price' => number_format(fake()->randomFloat(3, 5, 80), 3, '.', ''),
            'price_unit' => 'per_person',
            'booking_enabled' => false,
            'max_participants' => null,
            'daily_capacity' => null,
            'commission_rate' => null,
            'currency' => 'JOD',
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    public function bookable(): static
    {
        return $this->approved()->state(fn() => [
            'booking_enabled' => true,
            'price_unit' => fake()->randomElement(['per_person', 'per_group']),
            'max_participants' => 10,
            'daily_capacity' => 30,
        ]);
    }

    public function perGroup(): static
    {
        return $this->state(['price_unit' => 'per_group']);
    }
}