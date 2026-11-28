<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['provider' => 'JST Demo • Amman Heritage Walks', 'destination' => 'Amman Citadel', 'category' => 'Guided Tours', 'name' => 'Demo Amman Citadel history walk', 'duration' => 120, 'price' => '18.000'],
            ['provider' => 'JST Demo • Amman Heritage Walks', 'destination' => 'Roman Theatre', 'category' => 'Guided Tours', 'name' => 'Demo downtown heritage walk', 'duration' => 150, 'price' => '22.000'],
            ['provider' => 'JST Demo • Local City Guide', 'destination' => 'Rainbow Street', 'category' => 'City Tours', 'name' => 'Demo Jabal Amman neighborhood walk', 'duration' => 120, 'price' => '15.000'],
            ['provider' => 'JST Demo • Local City Guide', 'destination' => 'Jabal Al-Weibdeh', 'category' => 'City Tours', 'name' => 'Demo arts and architecture walk', 'duration' => 120, 'price' => '16.000'],
            ['provider' => 'JST Demo • Amman Food Experiences', 'destination' => 'Rainbow Street', 'category' => 'Food Experiences', 'name' => 'Demo Jordanian food tasting walk', 'duration' => 150, 'price' => '25.000'],
            ['provider' => 'JST Demo • Amman Food Experiences', 'destination' => 'The Jordan Museum', 'category' => 'Food Experiences', 'name' => 'Demo Jordanian home cooking session', 'duration' => 180, 'price' => '32.000'],
        ];

        foreach ($services as $data) {
            $provider = Provider::where('display_name', $data['provider'])->first();
            $destination = Destination::where('name', $data['destination'])
                ->whereHas('governorate', fn ($query) => $query->where('name', 'Amman'))
                ->first();
            $category = Category::where('kind', 'service')->where('name', $data['category'])->first();

            if (! $provider || ! $destination || ! $category) {
                $this->command?->warn("ServiceSeeder skipped {$data['name']}: a provider, destination, or service category is missing.");

                continue;
            }

            if (Service::where('provider_id', $provider->id)->where('name', $data['name'])->exists()) {
                continue;
            }

            $provider->services()->create([
                'destination_id' => $destination->id,
                'category_id' => $category->id,
                'name' => $data['name'],
                'description' => 'Local development service example only. Not approved, bookable, or offered by a real provider.',
                'duration_min' => $data['duration'],
                'price' => $data['price'],
                'price_unit' => 'per_person',
                'max_participants' => null,
                'daily_capacity' => null,
                'currency' => 'JOD',
            ]);
        }
    }
}
