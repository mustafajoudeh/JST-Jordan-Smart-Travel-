<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'type' => 'charge',
            'parent_payment_id' => null,
            'amount' => fn(array $a) => (string) Booking::find($a['booking_id'])->total_price,
            'currency' => 'JOD',
            'method' => fake()->randomElement(['card', 'cliq', 'wallet', 'cash']),
            'gateway' => 'demo_gateway',
            'gateway_reference' => fake()->unique()->bothify('TXN-########??'),
            'status' => 'pending',
            'failure_reason' => null,
            'initiated_by' => null,
            'paid_at' => null,
        ];
    }

    public function succeeded(): static
    {
        return $this->state([
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status' => 'failed',
            'failure_reason' => 'Payment declined by gateway',
        ]);
    }

    // A refund copies booking/method/gateway from its (succeeded) parent charge.
    public function refundOf(Payment $parent, ?string $amount = null): static
    {
        return $this->state([
            'type' => 'refund',
            'parent_payment_id' => $parent->id,
            'booking_id' => $parent->booking_id,
            'amount' => $amount ?? (string) $parent->amount,
            'currency' => $parent->currency,
            'method' => $parent->method,
            'gateway' => $parent->gateway,
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);
    }
}