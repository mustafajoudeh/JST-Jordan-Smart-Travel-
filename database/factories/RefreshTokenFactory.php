<?php

namespace Database\Factories;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<RefreshToken> */
class RefreshTokenFactory extends Factory
{
    protected $model = RefreshToken::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token_hash' => hash('sha256', Str::random(64)),
            'expires_at' => now()->addDays(30),
            'created_by_ip' => fake()->ipv4(),
            'revoked_at' => null,
            'revoked_by_ip' => null,
            'replaced_by_token_id' => null,
        ];
    }

    public function revoked(): static
    {
        return $this->state([
            'revoked_at' => now(),
            'revoked_by_ip' => fake()->ipv4(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }
}