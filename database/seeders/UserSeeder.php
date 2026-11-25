<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // No admin and no known password is ever created here: you create real accounts yourself.
        // Demo travelers exist only in local/testing, and each gets a random, unknown password,
        // so no account seeded by this class can be logged into.
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        if (User::where('role', 'traveler')->exists()) {
            return;
        }

        User::factory()
            ->count(10)
            ->state(fn () => ['password_hash' => Hash::make(Str::random(40))])
            ->create();
    }
}