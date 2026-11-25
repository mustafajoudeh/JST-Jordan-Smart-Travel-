<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\TripItems;
use Illuminate\Database\Seeder;

class TripItemsSeeder extends Seeder
{
    public function run(): void
    {
        if (TripItems::query()->exists()) {
            return;
        }

        $destinations = Destination::where('status', 'published')->pluck('id');
        $trips = Trip::all();

        if ($destinations->isEmpty() || $trips->isEmpty()) {
            $this->command?->warn('TripItemSeeder skipped: need trips and published destinations.');

            return;
        }

        // Day 1: up to three destinations per trip, ordered by position.
        foreach ($trips as $trip) {
            foreach ($destinations->shuffle()->take(3)->values() as $index => $destinationId) {
                TripItems::factory()->state([
                    'trip_id' => $trip->id,
                    'day_number' => 1,
                    'position' => $index + 1,
                    'destination_id' => $destinationId,
                ])->create();
            }
        }

        // Day 2: attach each booking to its traveler's trip (trips are at least 3 days long).
        foreach (Booking::all() as $booking) {
            $trip = Trip::where('user_id', $booking->user_id)->first();

            if (! $trip) {
                continue;
            }

            $position = (int) TripItems::where('trip_id', $trip->id)->where('day_number', 2)->max('position') + 1;

            TripItems::factory()
                ->forBooking($booking)
                ->state(['trip_id' => $trip->id, 'day_number' => 2, 'position' => $position])
                ->create();
        }
    }
}