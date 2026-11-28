<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        Governorate::firstOrCreate(
            ['name' => 'Amman'],
            [
                'description' => 'Capital governorate of Jordan and the pilot region of the platform.',
                'is_active' => true,
            ]
        );

        foreach (['Irbid', 'Ajloun', 'Jerash', 'Madaba', 'Karak', 'Aqaba', 'Mafraq', 'Balqa', 'Tafilah', 'Ma’an', 'Zarqa'] as $name) {
            Governorate::firstOrCreate(
                ['name' => $name],
                ['description' => "{$name} Governorate, Jordan.", 'is_active' => true]
            );
        }
    }
}
