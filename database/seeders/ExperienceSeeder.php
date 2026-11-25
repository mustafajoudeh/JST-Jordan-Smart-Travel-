<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        if (Experience::query()->exists()) {
            return;
        }

        $users = User::where('role', 'traveler')->pluck('id');
        $destinations = Destination::where('status', 'published')->pluck('id');

        if ($users->isEmpty() || $destinations->isEmpty()) {
            $this->command?->warn('ExperienceSeeder skipped: need travelers and published destinations.');

            return;
        }

        $owner = fn () => [
            'user_id' => $users->random(),
            'destination_id' => $destinations->random(),
        ];

        Experience::factory()->approved()->count(5)->state($owner)->create();
        Experience::factory()->count(3)->state($owner)->create(); // pending moderation
    }
}