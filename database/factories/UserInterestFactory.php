<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Models\UserInterest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserInterest> */
class UserInterestFactory extends Factory
{
    protected $model = UserInterest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory()->tourism(),
        ];
    }
}