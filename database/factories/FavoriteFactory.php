<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Experience;
use App\Models\Favorite;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Favorite> */
class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    // Exactly one target is set (favorites_exactly_one_target_check).
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'service_id' => null,
            'experience_id' => null,
        ];
    }

    public function forDestination(): static
    {
        return $this->state([
            'destination_id' => Destination::factory(),
            'service_id' => null,
            'experience_id' => null,
        ]);
    }

    public function forService(): static
    {
        return $this->state([
            'destination_id' => null,
            'service_id' => Service::factory(),
            'experience_id' => null,
        ]);
    }

    public function forExperience(): static
    {
        return $this->state([
            'destination_id' => null,
            'service_id' => null,
            'experience_id' => Experience::factory(),
        ]);
    }
}
