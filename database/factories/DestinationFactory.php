<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Destination> */
class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        return [
            'governorate_id' => Governorate::factory()->state(['name' => 'Amman']),
            'category_id' => Category::factory()->tourism(),
            'name' => fake()->randomElement([
                'Amman Citadel', 'Roman Theatre', 'Rainbow Street', 'Jordan Museum',
                'Darat al Funun', 'Jabal Al-Weibdeh', 'King Abdullah I Mosque',
                'Royal Automobile Museum',
            ]).' demo visit '.fake()->unique()->numberBetween(1, 99999),
            'description' => 'A Jordan tourism destination awaiting editorial review.',
            'latitude' => fake()->randomFloat(6, 31.85, 32.05),
            'longitude' => fake()->randomFloat(6, 35.80, 36.05),
            'address' => null,
            'suggested_duration_min' => fake()->randomElement([30, 60, 90, 120, 180]),
            'estimated_cost' => fake()->randomFloat(2, 0, 25),
            'opening_hours' => null,
            'tags' => fake()->randomElements(['family', 'history', 'views', 'walking', 'food', 'culture', 'photography'], 3),
            'source_type' => 'curated',
            'source_url' => null,
            'last_verified_at' => null,
            'status' => 'draft',
            'created_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    public function published(): static
    {
        return $this->state(['status' => 'published']);
    }

    public function officialFromVerifiedSource(string $sourceUrl): static
    {
        if (filter_var($sourceUrl, FILTER_VALIDATE_URL) === false || parse_url($sourceUrl, PHP_URL_SCHEME) !== 'https') {
            throw new \InvalidArgumentException('An HTTPS source URL must be supplied and verified by the caller.');
        }

        return $this->state([
            'source_type' => 'official',
            'source_url' => $sourceUrl,
            'last_verified_at' => now()->toDateString(),
        ]);
    }
}
