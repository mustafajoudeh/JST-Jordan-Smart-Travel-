<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Governorate;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Provider> */
class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function configure(): static
    {
        return $this->afterMaking(fn (Provider $provider) => $provider->forceFill([
            'verification_status' => 'unverified',
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
            'commission_rate' => null,
            'can_receive_bookings' => false,
            'payout_method' => null,
            'payout_account' => null,
        ]));
    }

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->provider(),
            'provider_type' => 'company',
            'display_name' => fake()->randomElement([
                'Demo Amman Heritage Walks', 'Demo Jordan Culture Trails',
                'Demo Jabal Amman Guides', 'Demo Levant Food Experiences',
                'Demo Amman City Tours',
            ]).' '.fake()->unique()->numberBetween(1, 99999),
            'description' => 'Local development provider fixture. Replace with verified provider information before publication.',
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => null,
            'website_url' => null,
            'address' => null,
            'governorate_id' => Governorate::factory(),
            'category_id' => Category::factory()->service(),
            'languages' => ['Arabic', 'English'],
            'specializations' => fake()->randomElements(['history', 'culture', 'food', 'nature', 'adventure'], 2),
            'verification_status' => 'unverified',
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
            'commission_rate' => null,
            'can_receive_bookings' => false,
            'payout_method' => null,
            'payout_account' => null,
        ];
    }

    public function company(): static
    {
        return $this->state(['provider_type' => 'company']);
    }

    public function guide(): static
    {
        return $this->state(fn () => [
            'provider_type' => 'guide',
            'display_name' => fake()->unique()->randomElement([
                'Demo Amman Walking Guide', 'Demo Downtown Guide', 'Demo Jabal Amman Guide',
            ]),
            'address' => null,
            'languages' => fake()->randomElements(['Arabic', 'English', 'French', 'German', 'Spanish'], 2),
        ]);
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'reviewed_at' => now(),
        ])->afterMaking(fn (Provider $provider) => $provider->forceFill([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]));
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ])->afterMaking(fn (Provider $provider) => $provider->forceFill([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]));
    }

    public function bookable(): static
    {
        return $this->approved()->state(fn () => [
            'can_receive_bookings' => true,
            'payout_method' => 'cliq',
            'payout_account' => 'JST-TEST-PAYOUT-ACCOUNT',
            'commission_rate' => '0.1500',
        ])->afterMaking(fn (Provider $provider) => $provider->forceFill([
            'can_receive_bookings' => true,
            'payout_method' => 'cliq',
            'payout_account' => 'JST-TEST-PAYOUT-ACCOUNT',
            'commission_rate' => '0.1500',
        ]));
    }
}
