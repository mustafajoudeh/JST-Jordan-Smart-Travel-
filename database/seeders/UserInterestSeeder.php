<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserInterestSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::where('kind', 'tourism')->pluck('id');
        $users = User::where('role', 'traveler')->pluck('id');

        if ($categoryIds->count() < 3 || $users->isEmpty()) {
            $this->command?->warn('UserInterestSeeder skipped: need travelers and tourism categories.');

            return;
        }

        $rows = [];

        foreach ($users as $userId) {
            foreach ($categoryIds->random(3) as $categoryId) {
                $rows[] = ['user_id' => $userId, 'category_id' => $categoryId];
            }
        }

        // insertOrIgnore on the composite PK makes this safe to repeat.
        DB::table('user_interests')->insertOrIgnore($rows);
    }
}