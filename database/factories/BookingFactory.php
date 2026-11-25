<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

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
            'participants' => fake()->numberBetween(1, 6),
            'unit_price' => fn(array $a) => (string) (Service::find($a['service_id'])->price ?? '10.000'),
            'total_price' => fn(array $a) => self::decimal(self::totalFils($a)),
            'currency' => 'JOD',
            'commission_rate' => '0.1500',
            'commission_amount' => fn(array $a) => self::decimal(self::commissionFils($a)),
            'provider_net' => fn(array $a) => self::decimal(self::totalFils($a) - self::commissionFils($a)),
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
        return $this->state(['status' => 'confirmed']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }

    public function rejected(): static
    {
        return $this->state(['status' => 'rejected']);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $a) => [
            'status' => 'cancelled',
            'cancelled_by' => $a['user_id'],
            'cancelled_at' => now(),
            'cancel_reason' => 'Cancelled by traveler',
        ]);
    }

    public function paid(): static
    {
        return $this->state(['payment_status' => 'paid']);
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

    private static function decimal(int $fils): string
    {
        return number_format($fils / 1000, 3, '.', '');
    }
}