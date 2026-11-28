<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = Destination::whereHas('governorate', fn ($query) => $query->where('name', 'Amman'))
            ->orderBy('name')
            ->get();

        $favorites = [
            'demo.solo@jst.test' => ['Amman Citadel', 'Roman Theatre'],
            'demo.couple@jst.test' => ['The Jordan Museum', 'Rainbow Street'],
            'demo.family@jst.test' => ['The Royal Automobile Museum', 'Amman Citadel'],
            'demo.friends@jst.test' => ['Rainbow Street', 'Darat al Funun'],
            'demo.budget@jst.test' => ['Roman Theatre', 'Jabal Al-Weibdeh'],
        ];

        foreach ($favorites as $email => $names) {
            $user = User::where('email', $email)->where('role', 'traveler')->first();

            if (! $user) {
                continue;
            }

            foreach ($names as $name) {
                $destination = $destinations->firstWhere('name', $name);

                if ($destination) {
                    $user->favorites()->firstOrCreate([
                        'destination_id' => $destination->id,
                        'service_id' => null,
                        'experience_id' => null,
                    ]);
                }
            }
        }
    }
}
