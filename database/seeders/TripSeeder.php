<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            'demo.solo@jst.test' => ['title' => 'A solo weekend in Amman', 'days' => 2, 'travelers' => 1, 'budget' => 'low', 'group' => 'solo', 'pace' => 'relaxed'],
            'demo.couple@jst.test' => ['title' => 'Culture and food in the capital', 'days' => 3, 'travelers' => 2, 'budget' => 'medium', 'group' => 'couple', 'pace' => 'balanced'],
            'demo.family@jst.test' => ['title' => 'A family visit to Amman', 'days' => 4, 'travelers' => 4, 'budget' => 'high', 'group' => 'family', 'pace' => 'relaxed'],
            'demo.friends@jst.test' => ['title' => 'Friends explore old Amman', 'days' => 5, 'travelers' => 4, 'budget' => 'medium', 'group' => 'friends', 'pace' => 'packed'],
            'demo.budget@jst.test' => ['title' => 'A low-cost Amman city break', 'days' => 2, 'travelers' => 1, 'budget' => 'low', 'group' => 'solo', 'pace' => 'balanced'],
        ];

        foreach ($plans as $email => $plan) {
            $user = User::where('email', $email)->where('role', 'traveler')->first();

            if (! $user) {
                $this->command?->warn("TripSeeder skipped {$email}: traveler account not found.");

                continue;
            }

            $user->trips()->firstOrCreate(
                ['title' => $plan['title']],
                [
                    'start_date' => now()->addDays(45)->toDateString(),
                    'end_date' => now()->addDays(45 + $plan['days'] - 1)->toDateString(),
                    'travelers_count' => $plan['travelers'],
                    'budget_level' => $plan['budget'],
                    'group_type' => $plan['group'],
                    'starting_point' => 'Amman, Jordan',
                    'is_ai_generated' => false,
                    'status' => 'draft',
                    'notes' => "Local demo itinerary ({$plan['pace']} pace). Update dates and details for a real trip.",
                ]
            );
        }
    }
}
