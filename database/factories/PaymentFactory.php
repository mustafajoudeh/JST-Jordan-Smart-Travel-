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
            'amount' => fn (array $a) => (string) Booking::find($a['booking_id'])->total_price,
            'currency' => 'JOD',
            'method' => 'cash',
            'gateway' => null,
            'gateway_reference' => null,
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
        ])->afterCreating(function (Payment $payment): void {
            $booking = $payment->booking;

            if (in_array($booking->status, ['cancelled', 'rejected'], true)) {
                $payment->delete();

                throw new \InvalidArgumentException('A cancelled or rejected booking cannot receive a succeeded payment fixture.');
            }

            $booking->forceFill(['payment_status' => 'paid'])->save();
        });
    }

    public function failed(): static
    {
        return $this->state([
            'status' => 'failed',
            'failure_reason' => 'Local test fixture: payment marked failed; no gateway request was made.',
        ])->afterCreating(function (Payment $payment): void {
            $payment->booking->forceFill(['payment_status' => 'failed'])->save();
        });
    }

    // A refund copies booking/method/gateway from its (succeeded) parent charge.
    public function refundOf(Payment $parent, ?string $amount = null): static
    {
        if (! $parent->exists || $parent->type !== 'charge' || $parent->status !== 'succeeded') {
            throw new \InvalidArgumentException('A refund fixture must reference a succeeded charge.');
        }

        $refundAmount = $amount ?? (string) $parent->amount;

        if ((float) $refundAmount <= 0 || (float) $refundAmount > (float) $parent->amount) {
            throw new \InvalidArgumentException('Refund amount must be positive and no greater than the original charge.');
        }

        return $this->state([
            'type' => 'refund',
            'parent_payment_id' => $parent->id,
            'booking_id' => $parent->booking_id,
            'amount' => $refundAmount,
            'currency' => $parent->currency,
            'method' => $parent->method,
            'gateway' => $parent->gateway,
            'status' => 'succeeded',
            'paid_at' => now(),
        ])->afterCreating(function (Payment $refund) use ($parent): void {
            $succeededRefunds = Payment::where('parent_payment_id', $parent->id)
                ->where('status', 'succeeded')
                ->sum('amount');
            $status = (float) $succeededRefunds >= (float) $parent->amount ? 'refunded' : 'partially_refunded';

            $refund->booking->forceFill(['payment_status' => $status])->save();
        });
    }
}
