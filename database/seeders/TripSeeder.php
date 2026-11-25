<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        if (Trip::query()->exists()) {
            return;
        }

        User::where('role', 'traveler')->get()->each(
            fn (User $user) => Trip::factory()->state(['user_id' => $user->id])->create()
        );
    }
}