<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserInterestSeeder extends Seeder
{
    public function run(): void
    {
        $interests = [
            'demo.solo@jst.test' => ['Historical', 'Cultural', 'Food'],
            'demo.couple@jst.test' => ['Cultural', 'Food', 'Relaxation'],
            'demo.family@jst.test' => ['Historical', 'Family Activities', 'Cultural'],
            'demo.friends@jst.test' => ['Food', 'Adventure', 'Cultural'],
            'demo.budget@jst.test' => ['Historical', 'Food', 'Cultural'],
        ];

        foreach ($interests as $email => $names) {
            $user = User::where('email', $email)->where('role', 'traveler')->first();

            if (! $user) {
                continue;
            }

            $categoryIds = Category::where('kind', 'tourism')
                ->whereIn('name', $names)
                ->pluck('id')
                ->all();

            $user->interests()->syncWithoutDetaching($categoryIds);
        }
    }
}
