<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Experience> */
class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function configure(): static
    {
        return $this->afterMaking(fn (Experience $experience) => $experience->forceFill([
            'status' => 'pending',
            'moderated_by' => null,
            'moderated_at' => null,
        ]));
    }

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'title' => fake()->randomElement([
                'A morning above downtown Amman', 'Discovering old Amman on foot',
                'A quiet afternoon in Jabal Al-Weibdeh', 'Family day at the Citadel',
                'Exploring Amman’s museums', 'Street food and city views',
            ]).' — demo '.fake()->unique()->numberBetween(1, 99999),
            'body' => 'A local development example awaiting traveler submission and moderation. Replace with a genuine traveler account before publication.',
            'rating' => fake()->numberBetween(1, 5),
            'social_url' => null,
            'tags' => fake()->randomElements(['family', 'food', 'history', 'views', 'budget', 'sunset'], 2),
            'status' => 'pending',
            'moderated_by' => null,
            'moderated_at' => null,
        ];
    }

    public function approvedBy(User $moderator): static
    {
        self::assertModerator($moderator);

        return $this->state([
            'status' => 'approved',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
        ])->afterMaking(fn (Experience $experience) => $experience->forceFill([
            'status' => 'approved', 'moderated_by' => $moderator->id, 'moderated_at' => now(),
        ]));
    }

    public function rejectedBy(User $moderator): static
    {
        self::assertModerator($moderator);

        return $this->state([
            'status' => 'rejected',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
        ])->afterMaking(fn (Experience $experience) => $experience->forceFill([
            'status' => 'rejected', 'moderated_by' => $moderator->id, 'moderated_at' => now(),
        ]));
    }

    public function hiddenBy(User $moderator): static
    {
        self::assertModerator($moderator);

        return $this->state([
            'status' => 'hidden',
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
        ])->afterMaking(fn (Experience $experience) => $experience->forceFill([
            'status' => 'hidden', 'moderated_by' => $moderator->id, 'moderated_at' => now(),
        ]));
    }

    private static function assertModerator(User $moderator): void
    {
        $role = $moderator->role instanceof \BackedEnum ? $moderator->role->value : $moderator->role;

        if (! in_array($role, ['content_admin', 'system_admin'], true)) {
            throw new \InvalidArgumentException('Experience moderation requires a content_admin or system_admin user.');
        }
    }
}
