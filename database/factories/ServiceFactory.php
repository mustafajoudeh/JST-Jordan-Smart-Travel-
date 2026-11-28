<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function configure(): static
    {
        return $this->afterMaking(fn (Service $service) => $service->forceFill([
            'booking_enabled' => false,
            'commission_rate' => null,
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]));
    }

    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'destination_id' => Destination::factory(),
            'category_id' => Category::factory()->service(),
            'name' => fake()->randomElement([
                'Amman heritage walking tour', 'Jabal Amman food walk',
                'Downtown architecture walk', 'Jordanian home cooking session',
                'Amman museum and Citadel tour', 'Old Amman neighborhood walk',
            ]).' '.fake()->unique()->numberBetween(1, 99999),
            'description' => 'Local development service fixture. Confirm the provider, route, and terms before accepting bookings.',
            'duration_min' => fake()->randomElement([60, 90, 120, 180, 240]),
            'price' => number_format(fake()->randomElement([12, 18, 22, 28, 35, 45]), 3, '.', ''),
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
        ])->afterMaking(fn (Service $service) => $service->forceFill([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]));
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ])->afterMaking(fn (Service $service) => $service->forceFill([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]));
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive'])
            ->afterMaking(fn (Service $service) => $service->forceFill(['status' => 'inactive']));
    }

    public function bookable(): static
    {
        return $this->approved()->state(fn () => [
            'provider_id' => Provider::factory()->bookable(),
            'booking_enabled' => true,
            'price_unit' => 'per_person',
            'max_participants' => 8,
            'daily_capacity' => 24,
        ])->afterMaking(fn (Service $service) => $service->forceFill(['booking_enabled' => true]));
    }

    public function perGroup(): static
    {
        return $this->state(['price_unit' => 'per_group']);
    }
}
