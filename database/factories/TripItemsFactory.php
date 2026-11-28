<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Service;
use App\Models\Trip;
use App\Models\TripItems;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TripItems> */
class TripItemsFactory extends Factory
{
    protected $model = TripItems::class;

    // Exactly one item kind is set (trip_items_exactly_one_item_check).
    // Pass explicit trip_id/day_number/position when creating several items for one trip.
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'day_number' => 1,
            'position' => fake()->unique()->numberBetween(1, 30000),
            'start_time' => null,
            'end_time' => null,
            'destination_id' => Destination::factory(),
            'activity_id' => null,
            'event_id' => null,
            'service_id' => null,
            'booking_id' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forDestination(): static
    {
        return $this->state([
            'destination_id' => Destination::factory(),
            'activity_id' => null,
            'event_id' => null,
            'service_id' => null,
        ]);
    }

    public function forActivity(): static
    {
        return $this->state([
            'destination_id' => null,
            'activity_id' => Activity::factory(),
            'event_id' => null,
            'service_id' => null,
        ]);
    }

    public function forEvent(): static
    {
        return $this->state([
            'destination_id' => null,
            'activity_id' => null,
            'event_id' => Event::factory(),
            'service_id' => null,
        ]);
    }

    public function forService(): static
    {
        return $this->state([
            'destination_id' => null,
            'activity_id' => null,
            'event_id' => null,
            'service_id' => Service::factory()->bookable(),
        ]);
    }

    // booking_id requires service_id, and it must be the booked service.
    public function forBooking(Booking $booking): static
    {
        return $this->state([
            'destination_id' => null,
            'activity_id' => null,
            'event_id' => null,
            'service_id' => $booking->service_id,
            'booking_id' => $booking->id,
        ]);
    }
}
