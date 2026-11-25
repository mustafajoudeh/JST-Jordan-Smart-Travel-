<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        if (Favorite::query()->exists()) {
            return;
        }

        $destinations = Destination::where('status', 'published')->pluck('id');
        $users = User::where('role', 'traveler')->get();

        if ($destinations->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('FavoriteSeeder skipped: need travelers and published destinations.');

            return;
        }

        foreach ($users as $user) {
            foreach ($destinations->shuffle()->take(2) as $destinationId) {
                Favorite::factory()
                    ->state(['user_id' => $user->id, 'destination_id' => $destinationId])
                    ->create();
            }
        }
    }
}