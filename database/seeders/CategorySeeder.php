<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tourism = [
            'Historical' => 'Archaeological places and historic landmarks.',
            'Cultural' => 'Museums, arts, neighborhoods, and local heritage.',
            'Religious' => 'Religious landmarks and heritage sites.',
            'Nature' => 'Parks, landscapes, and outdoor places.',
            'Adventure' => 'Active outdoor experiences.',
            'Food' => 'Jordanian food, markets, and culinary culture.',
            'Relaxation' => 'Leisure and restorative experiences.',
            'Family Activities' => 'Places and activities suited to family visits.',
        ];

        $service = [
            'Guided Tours' => 'Guide-led visits and walking tours.',
            'Cultural Experiences' => 'Hosted arts and heritage experiences.',
            'Food Experiences' => 'Hosted food walks and cooking experiences.',
            'City Tours' => 'Local city tours and neighborhood walks.',
            'Activities' => 'Bookable activities and workshops.',
            'Transportation' => 'Local travel and transfer services.',
        ];

        foreach ($tourism as $name => $description) {
            Category::firstOrCreate(['kind' => 'tourism', 'name' => $name], ['description' => $description, 'is_active' => true]);
        }

        foreach ($service as $name => $description) {
            Category::firstOrCreate(['kind' => 'service', 'name' => $name], ['description' => $description, 'is_active' => true]);
        }
    }
}
