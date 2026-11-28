<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $passwordHash = null;

    public function definition(): array
    {
        return [
            'role' => 'traveler',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => static::$passwordHash ??= Hash::make('password'),
            'country_code' => fake()->randomElement(['JO', 'SA', 'AE', 'EG', 'US', 'GB', 'DE', 'FR']),
            'photo_url' => null,
            'phone' => fake()->optional(0.5)->numerify('+9627########'),
            'budget_level' => fake()->randomElement(['low', 'medium', 'high']),
            'trip_pace' => fake()->randomElement(['relaxed', 'balanced', 'packed']),
            'group_type' => fake()->randomElement(['solo', 'couple', 'family', 'friends']),
            'activity_level' => fake()->randomElement(['low', 'moderate', 'high']),
            'is_active' => true,
            'failed_login_attempts' => 0,
            'last_login_at' => null,
            'deleted_at' => null,
        ];
    }

    public function provider(): static
    {
        return $this->state(['role' => 'provider']);
    }

    public function contentAdmin(): static
    {
        return $this->state(['role' => 'content_admin']);
    }

    public function systemAdmin(): static
    {
        return $this->state(['role' => 'system_admin']);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function trashed(): static
    {
        return $this->state(['deleted_at' => now()])
            ->afterMaking(fn (User $user) => $user->forceFill(['deleted_at' => now()]));
    }

    public function withoutPreferences(): static
    {
        return $this->state([
            'budget_level' => null,
            'trip_pace' => null,
            'group_type' => null,
            'activity_level' => null,
        ]);
    }
}
