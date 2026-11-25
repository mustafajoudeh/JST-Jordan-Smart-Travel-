<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tourism = [
            'Historical',
            'Cultural',
            'Religious',
            'Nature',
            'Adventure',
            'Food',
            'Relaxation',
            'Family Activities',
        ];

        $service = [
            'Guided Tours',
            'Cultural Experiences',
            'Food Experiences',
            'City Tours',
            'Activities',
            'Transportation',
        ];

        foreach ($tourism as $name) {
            Category::firstOrCreate(['kind' => 'tourism', 'name' => $name], ['is_active' => true]);
        }

        foreach ($service as $name) {
            Category::firstOrCreate(['kind' => 'service', 'name' => $name], ['is_active' => true]);
        }
    }
}