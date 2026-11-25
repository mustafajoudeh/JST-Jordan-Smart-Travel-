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

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->provider(),
            'provider_type' => 'company',
            'display_name' => fake()->unique()->company(),
            'description' => fake()->paragraph(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => fake()->numerify('+9626#######'),
            'website_url' => fake()->optional()->url(),
            'address' => fake()->streetAddress(),
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
        return $this->state(fn() => [
            'provider_type' => 'guide',
            'display_name' => fake()->name(),
            'address' => null,
            'languages' => fake()->randomElements(['Arabic', 'English', 'French', 'German', 'Spanish'], 2),
        ]);
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]);
    }

    public function verified(): static
    {
        return $this->state(['verification_status' => 'verified']);
    }

    // Satisfies providers_booking_requirements_check (approved + payout_account).
    // Short CliQ alias on purpose: the encrypted value must fit varchar(255).
    public function bookable(): static
    {
        return $this->approved()->state(fn() => [
            'can_receive_bookings' => true,
            'payout_method' => 'cliq',
            'payout_account' => fake()->numerify('9627########'),
        ]);
    }
}