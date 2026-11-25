<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        if (Payment::query()->exists()) {
            return;
        }

        $bookings = Booking::orderBy('id')->limit(3)->get();

        if ($bookings->isEmpty()) {
            $this->command?->warn('PaymentSeeder skipped: no bookings.');

            return;
        }

        // One succeeded charge per booking, and the booking summary status in the same transaction.
        foreach ($bookings as $booking) {
            DB::transaction(function () use ($booking) {
                Payment::factory()->succeeded()->state([
                    'booking_id' => $booking->id,
                    'amount' => (string) $booking->total_price,
                    'currency' => $booking->currency,
                ])->create();

                $booking->forceFill(['payment_status' => 'paid'])->save();
            });
        }
    }
}