<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        $demoPassword = Hash::make('JstLocal!2026');

        $accounts = [
            ['first_name' => 'Samira', 'last_name' => 'Haddad', 'email' => 'demo.solo@jst.test', 'role' => 'traveler', 'group_type' => 'solo', 'budget_level' => 'low'],
            ['first_name' => 'Omar', 'last_name' => 'Nasser', 'email' => 'demo.couple@jst.test', 'role' => 'traveler', 'group_type' => 'couple', 'budget_level' => 'medium'],
            ['first_name' => 'Lina', 'last_name' => 'Khalil', 'email' => 'demo.family@jst.test', 'role' => 'traveler', 'group_type' => 'family', 'budget_level' => 'high'],
            ['first_name' => 'Tareq', 'last_name' => 'Mansour', 'email' => 'demo.friends@jst.test', 'role' => 'traveler', 'group_type' => 'friends', 'budget_level' => 'medium'],
            ['first_name' => 'Maya', 'last_name' => 'Saleh', 'email' => 'demo.budget@jst.test', 'role' => 'traveler', 'group_type' => 'solo', 'budget_level' => 'low'],
            ['first_name' => 'Demo', 'last_name' => 'Heritage Walks', 'email' => 'demo.provider.heritage@jst.test', 'role' => 'provider'],
            ['first_name' => 'Demo', 'last_name' => 'City Guide', 'email' => 'demo.provider.guide@jst.test', 'role' => 'provider'],
            ['first_name' => 'Demo', 'last_name' => 'Food Experiences', 'email' => 'demo.provider.food@jst.test', 'role' => 'provider'],
        ];

        foreach ($accounts as $account) {
            User::withTrashed()->firstOrCreate(
                ['email' => $account['email']],
                [
                    'first_name' => $account['first_name'],
                    'last_name' => $account['last_name'],
                    'role' => $account['role'],
                    'password_hash' => $demoPassword,
                    'country_code' => 'JO',
                    'is_active' => true,
                    'budget_level' => $account['budget_level'] ?? 'medium',
                    'trip_pace' => 'balanced',
                    'group_type' => $account['group_type'] ?? null,
                    'activity_level' => 'moderate',
                ]
            );
        }
    }
}
