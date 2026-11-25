<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RefreshTokenSeeder extends Seeder
{
    /**
     * Intentionally empty: refresh tokens are issued at login and are runtime data.
     * Seeding fake token hashes would only create unusable, misleading rows.
     * Use RefreshToken::factory() in tests when a token row is needed.
     */
    public function run(): void
    {
        //
    }
}