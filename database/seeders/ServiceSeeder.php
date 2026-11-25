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
        if (Service::query()->exists()) {
            return;
        }

        $destinations = Destination::where('status', 'published')->pluck('id');
        $categories = Category::where('kind', 'service')->pluck('id');
        $providers = Provider::where('status', 'approved')->get();

        if ($destinations->isEmpty() || $categories->isEmpty() || $providers->isEmpty()) {
            $this->command?->warn('ServiceSeeder skipped: need published destinations, service categories and approved providers.');

            return;
        }

        foreach ($providers as $provider) {
            $factory = Service::factory()
                ->approved()
                ->count(2)
                ->state(fn () => [
                    'provider_id' => $provider->id,
                    'destination_id' => $destinations->random(),
                    'category_id' => $categories->random(),
                ]);

            if ($provider->can_receive_bookings) {
                $factory = $factory->bookable();
            }

            $factory->create();
        }
    }
}