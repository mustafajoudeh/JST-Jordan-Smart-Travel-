<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PlatformSetting;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function configure(): static
    {
        return $this->afterMaking(fn (Booking $booking) => $booking->forceFill([
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'cancelled_by' => null,
            'cancelled_at' => null,
            'cancel_reason' => null,
        ]));
    }

    // Money is computed in fils (integers) so that
    // total_price = commission_amount + provider_net holds exactly.
    // If you override unit_price/participants/commission_rate, the dependent amounts follow.
    // If you override total_price directly, you must also override the split.
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'service_id' => Service::factory()->bookable(),
            'booking_date' => fake()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'start_time' => fake()->optional()->randomElement(['09:00:00', '10:00:00', '14:00:00']),
            'participants' => fn (array $a) => fake()->numberBetween(1, min(6, (int) (Service::find($a['service_id'])->max_participants ?? 6))),
            'unit_price' => fn (array $a) => (string) (Service::find($a['service_id'])->price ?? '10.000'),
            'total_price' => fn (array $a) => self::decimal(self::totalFils($a)),
            'currency' => fn (array $a) => Service::find($a['service_id'])->currency ?? 'JOD',
            'commission_rate' => fn (array $a) => self::defaultCommissionRate($a),
            'commission_amount' => fn (array $a) => self::decimal(self::commissionFils($a)),
            'provider_net' => fn (array $a) => self::decimal(self::totalFils($a) - self::commissionFils($a)),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'contact_phone' => fake()->optional()->numerify('+9627########'),
            'customer_note' => fake()->optional()->sentence(),
            'cancelled_by' => null,
            'cancelled_at' => null,
            'cancel_reason' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(['status' => 'confirmed'])
            ->afterMaking(fn (Booking $booking) => $booking->forceFill(['status' => 'confirmed']));
    }

    public function completed(): static
    {
        return $this->paid()
            ->state(['status' => 'completed'])
            ->afterMaking(fn (Booking $booking) => $booking->forceFill(['status' => 'completed']));
    }

    public function rejected(): static
    {
        return $this->state(['status' => 'rejected', 'payment_status' => 'unpaid'])
            ->afterMaking(fn (Booking $booking) => $booking->forceFill([
                'status' => 'rejected',
                'payment_status' => 'unpaid',
            ]))
            ->afterCreating(fn (Booking $booking) => self::clearPaymentForUnavailableBooking($booking));
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $a) => [
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
            'cancelled_by' => $a['user_id'],
            'cancelled_at' => now(),
            'cancel_reason' => 'Cancelled by traveler',
        ])->afterMaking(fn (Booking $booking) => $booking->forceFill([
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
            'cancelled_by' => $booking->user_id,
            'cancelled_at' => now(),
            'cancel_reason' => 'Cancelled by traveler',
        ]))->afterCreating(fn (Booking $booking) => self::clearPaymentForUnavailableBooking($booking));
    }

    public function paid(): static
    {
        return $this->state(['payment_status' => 'paid'])
            ->afterMaking(fn (Booking $booking) => $booking->forceFill(['payment_status' => 'paid']))
            ->afterCreating(function (Booking $booking): void {
                if (! in_array($booking->status, ['cancelled', 'rejected'], true) && $booking->payment_status === 'paid') {
                    Payment::factory()->succeeded()->state([
                        'booking_id' => $booking->id,
                        'amount' => (string) $booking->total_price,
                        'currency' => $booking->currency,
                    ])->create();
                }
            });
    }

    private static function totalFils(array $a): int
    {
        $unit = (int) round((float) $a['unit_price'] * 1000);
        $service = Service::find($a['service_id']);

        return $service && $service->price_unit === 'per_group'
            ? $unit
            : $unit * (int) $a['participants'];
    }

    private static function commissionFils(array $a): int
    {
        return (int) round(self::totalFils($a) * (float) $a['commission_rate']);
    }

    private static function defaultCommissionRate(array $attributes): string
    {
        $service = Service::with('provider')->find($attributes['service_id']);
        $rate = $service?->provider?->commission_rate;

        if ($rate !== null) {
            return (string) $rate;
        }

        return (string) (PlatformSetting::where('key', 'default_commission_rate')->value('value') ?? '0.1500');
    }

    private static function clearPaymentForUnavailableBooking(Booking $booking): void
    {
        if (in_array($booking->status, ['cancelled', 'rejected'], true) && $booking->payment_status !== 'unpaid') {
            $booking->forceFill(['payment_status' => 'unpaid'])->save();
        }
    }

    private static function decimal(int $fils): string
    {
        return number_format($fils / 1000, 3, '.', '');
    }
}
