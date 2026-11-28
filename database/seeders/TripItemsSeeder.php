<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class TripItemsSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            'demo.solo@jst.test' => [
                ['destination' => 'Amman Citadel', 'activity' => 'Explore the Umayyad Palace'],
                ['destination' => 'Roman Theatre', 'activity' => 'Visit the Roman Theatre'],
            ],
            'demo.couple@jst.test' => [
                ['destination' => 'The Jordan Museum', 'activity' => 'Explore Jordanian heritage galleries'],
                ['destination' => 'Rainbow Street', 'activity' => 'Walk Rainbow Street'],
                ['destination' => 'Darat al Funun', 'activity' => 'Visit the contemporary art spaces'],
            ],
            'demo.family@jst.test' => [
                ['destination' => 'The Royal Automobile Museum', 'activity' => 'Explore Jordan’s motoring history'],
                ['destination' => 'Amman Citadel', 'activity' => 'Walk the Temple of Hercules ridge'],
                ['destination' => 'Roman Theatre', 'activity' => 'Visit the Roman Theatre'],
                ['destination' => 'Rainbow Street', 'activity' => 'Walk Rainbow Street'],
            ],
            'demo.friends@jst.test' => [
                ['destination' => 'Rainbow Street', 'activity' => 'Walk Rainbow Street'],
                ['destination' => 'Amman Citadel', 'activity' => 'Explore the Umayyad Palace'],
                ['destination' => 'Darat al Funun', 'activity' => 'Visit the contemporary art spaces'],
                ['destination' => 'Jabal Al-Weibdeh', 'activity' => 'Walk the Jabal Al-Weibdeh lanes'],
                ['destination' => 'King Abdullah I Mosque', 'activity' => 'Admire the blue dome and architecture'],
            ],
            'demo.budget@jst.test' => [
                ['destination' => 'Amman Citadel', 'activity' => 'Walk the Temple of Hercules ridge'],
                ['destination' => 'Roman Theatre', 'activity' => 'Visit the Roman Theatre'],
            ],
        ];

        foreach (Trip::with('user')->get() as $trip) {
            $route = $routes[$trip->user->email] ?? [];
            $availableDays = $trip->start_date->diffInDays($trip->end_date) + 1;

            foreach (array_slice($route, 0, $availableDays) as $index => $stop) {
                $day = $index + 1;
                $destination = Destination::where('name', $stop['destination'])
                    ->whereHas('governorate', fn ($query) => $query->where('name', 'Amman'))
                    ->first();

                if (! $destination) {
                    $this->command?->warn("TripItemsSeeder skipped a stop for {$trip->title}: destination missing.");

                    continue;
                }

                $trip->items()->firstOrCreate(
                    ['day_number' => $day, 'position' => 1],
                    ['destination_id' => $destination->id, 'notes' => 'Suggested visit; confirm current access and opening hours.']
                );

                $activity = $destination->activities()->where('name', $stop['activity'])->first();

                if ($activity) {
                    $trip->items()->firstOrCreate(
                        ['day_number' => $day, 'position' => 2],
                        ['activity_id' => $activity->id, 'notes' => 'Suggested activity; check current visitor information.']
                    );
                }
            }
        }
    }
}
