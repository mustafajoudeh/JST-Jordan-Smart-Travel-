<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Governorate;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class DashboardDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('Dashboard demo data is restricted to local and testing environments.');

            return;
        }

        $this->call([
            GovernorateSeeder::class,
            CategorySeeder::class,
            PlatformSettingSeeder::class,
            UserSeeder::class,
            DestinationSeeder::class,
        ]);

        DB::transaction(function (): void {
            $reviewerId = User::query()->where('role', 'system_admin')->value('id');
            $travelerIds = User::query()
                ->where('role', 'traveler')
                ->where('email', 'like', 'demo.%@jst.test')
                ->orderBy('id')
                ->pluck('id')
                ->all();
            $governorateId = Governorate::query()->where('name', 'Amman')->value('id');
            $categoryId = Category::query()
                ->where('kind', 'service')
                ->where('name', 'Guided Tours')
                ->value('id');
            $destinationIds = Destination::query()
                ->whereIn('name', ['Amman Citadel', 'Rainbow Street'])
                ->pluck('id', 'name');

            if ($travelerIds === [] || ! $governorateId || ! $categoryId || $destinationIds->count() !== 2) {
                throw new RuntimeException('Seed the JST local catalog before creating dashboard demo bookings.');
            }

            $providerAccount = User::withTrashed()->firstOrCreate(
                ['email' => 'dashboard.demo.provider@jst.test'],
                [
                    'first_name' => 'Dashboard Demo',
                    'last_name' => 'Provider',
                    'role' => 'provider',
                    'password_hash' => Hash::make(Str::random(64)),
                    'country_code' => 'JO',
                    'is_active' => true,
                ]
            );

            if ($providerAccount->getRawOriginal('role') !== 'provider') {
                throw new RuntimeException('The reserved dashboard demo provider account has an unexpected role.');
            }

            $provider = Provider::query()->firstOrNew(['user_id' => $providerAccount->id]);

            if (! $provider->exists) {
                $provider->forceFill([
                    'provider_type' => 'company',
                    'display_name' => 'JST Dashboard Demo · Amman Experiences',
                    'description' => 'Local development fixture for dashboard reporting.',
                    'contact_email' => $providerAccount->email,
                    'governorate_id' => $governorateId,
                    'category_id' => $categoryId,
                    'languages' => ['Arabic', 'English'],
                    'specializations' => ['history', 'culture'],
                    'verification_status' => 'verified',
                    'status' => 'approved',
                    'reviewed_by' => $reviewerId,
                    'reviewed_at' => now(),
                    'commission_rate' => '0.1500',
                    'can_receive_bookings' => true,
                    'payout_method' => 'cliq',
                    'payout_account' => 'DEMO',
                ])->save();
            }

            $serviceDefinitions = [
                [
                    'name' => 'JST dashboard demo: Amman heritage walk',
                    'destination_id' => $destinationIds->get('Amman Citadel'),
                    'duration_min' => 120,
                    'price' => '18.000',
                ],
                [
                    'name' => 'JST dashboard demo: downtown food walk',
                    'destination_id' => $destinationIds->get('Rainbow Street'),
                    'duration_min' => 150,
                    'price' => '25.000',
                ],
            ];
            $services = [];

            foreach ($serviceDefinitions as $definition) {
                $service = Service::query()->firstOrNew([
                    'provider_id' => $provider->id,
                    'name' => $definition['name'],
                ]);

                if (! $service->exists) {
                    $service->forceFill([
                        'destination_id' => $definition['destination_id'],
                        'category_id' => $categoryId,
                        'description' => 'Local dashboard test fixture. Not a real commercial offer.',
                        'duration_min' => $definition['duration_min'],
                        'price' => $definition['price'],
                        'price_unit' => 'per_person',
                        'booking_enabled' => true,
                        'max_participants' => 8,
                        'daily_capacity' => 24,
                        'commission_rate' => '0.1500',
                        'currency' => 'JOD',
                        'status' => 'approved',
                        'reviewed_by' => $reviewerId,
                        'reviewed_at' => now(),
                    ])->save();
                }

                $services[] = $service;
            }

            $fixtures = [
                [0, 'completed', 'succeeded'], [0, 'pending', null], [1, 'confirmed', 'succeeded'],
                [2, 'completed', 'succeeded'], [3, 'cancelled', null], [4, 'completed', 'succeeded'],
                [6, 'pending', 'failed'], [7, 'completed', 'succeeded'], [9, 'confirmed', null],
                [11, 'completed', 'succeeded'], [13, 'pending', null], [15, 'rejected', null],
                [17, 'completed', 'succeeded'], [20, 'confirmed', 'succeeded'], [22, 'completed', 'succeeded'],
                [25, 'pending', null], [27, 'completed', 'succeeded'], [28, 'cancelled', null],
                [29, 'confirmed', null], [30, 'pending', null], [35, 'completed', 'succeeded'],
                [40, 'confirmed', null], [45, 'cancelled', null], [50, 'completed', 'succeeded'],
                [56, 'pending', 'failed'], [62, 'rejected', null], [68, 'completed', 'succeeded'],
                [75, 'confirmed', 'succeeded'], [82, 'completed', 'succeeded'], [89, 'rejected', null],
            ];
            $now = CarbonImmutable::now();

            foreach ($fixtures as $index => [$age, $status, $paymentStatus]) {
                $createdAt = $age === 0
                    ? $now->subMinutes(15 + $index)
                    : $now->subDays($age)->setTime(9 + ($index % 6), 0);
                $travelerId = $travelerIds[$index % count($travelerIds)];
                $service = $services[$index % count($services)];
                $participants = ($index % 6) + 1;
                $unitPriceFils = (int) round((float) $service->price * 1000);
                $totalFils = $unitPriceFils * $participants;
                $commissionFils = (int) round($totalFils * 0.15);
                $totalPrice = number_format($totalFils / 1000, 3, '.', '');
                $commission = number_format($commissionFils / 1000, 3, '.', '');
                $providerNet = number_format(($totalFils - $commissionFils) / 1000, 3, '.', '');
                $note = sprintf('JST dashboard demo fixture #%02d', $index + 1);
                $bookingDate = $status === 'completed'
                    ? $createdAt->toDateString()
                    : $createdAt->addDays(90)->toDateString();
                $booking = Booking::query()->firstOrNew(['customer_note' => $note]);

                if (! $booking->exists) {
                    $booking->forceFill([
                        'user_id' => $travelerId,
                        'service_id' => $service->id,
                        'booking_date' => $bookingDate,
                        'start_time' => $status === 'completed' ? '08:00:00' : '10:00:00',
                        'participants' => $participants,
                        'unit_price' => (string) $service->price,
                        'total_price' => $totalPrice,
                        'currency' => $service->currency,
                        'commission_rate' => '0.1500',
                        'commission_amount' => $commission,
                        'provider_net' => $providerNet,
                        'status' => $status,
                        'payment_status' => $paymentStatus === 'succeeded' ? 'paid' : ($paymentStatus ?? 'unpaid'),
                        'customer_note' => $note,
                        'cancelled_by' => $status === 'cancelled' ? $travelerId : null,
                        'cancelled_at' => $status === 'cancelled' ? $createdAt->addHours(4) : null,
                        'cancel_reason' => $status === 'cancelled' ? 'Local dashboard demo fixture.' : null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ])->save();
                }

                if ($paymentStatus !== null) {
                    Payment::query()->firstOrCreate(
                        ['booking_id' => $booking->id, 'type' => 'charge'],
                        [
                            'parent_payment_id' => null,
                            'amount' => $totalPrice,
                            'currency' => $service->currency,
                            'method' => ['card', 'cliq', 'cash'][$index % 3],
                            'gateway' => null,
                            'gateway_reference' => null,
                            'status' => $paymentStatus,
                            'failure_reason' => $paymentStatus === 'failed' ? 'Local dashboard payment fixture.' : null,
                            'initiated_by' => $travelerId,
                            'paid_at' => $paymentStatus === 'succeeded' ? $createdAt : null,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]
                    );
                }
            }

            $demoBookings = Booking::query()
                ->where('customer_note', 'like', 'JST dashboard demo fixture #%')
                ->count();
            $demoPayments = Payment::query()
                ->whereHas('booking', fn ($query) => $query->where('customer_note', 'like', 'JST dashboard demo fixture #%'))
                ->count();

            $this->command?->info("Dashboard fixtures ready: {$demoBookings} bookings and {$demoPayments} payments.");
        });
    }
}
