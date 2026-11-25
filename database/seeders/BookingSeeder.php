<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        if (Booking::query()->exists()) {
            return;
        }

        $services = Service::where('booking_enabled', true)->where('status', 'approved')->pluck('id');
        $users = User::where('role', 'traveler')->pluck('id');

        if ($services->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('BookingSeeder skipped: need bookable services and travelers.');

            return;
        }

        // Price, total, commission and provider net are snapshotted by the factory.
        Booking::factory()
            ->count(4)
            ->state(fn () => [
                'user_id' => $users->random(),
                'service_id' => $services->random(),
            ])
            ->create();
    }
}