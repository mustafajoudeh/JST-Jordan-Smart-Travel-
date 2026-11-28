<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $submissions = [
            ['email' => 'demo.solo@jst.test', 'destination' => 'Amman Citadel', 'title' => 'Demo submission: a morning at the Citadel'],
            ['email' => 'demo.couple@jst.test', 'destination' => 'Rainbow Street', 'title' => 'Demo submission: an evening in Jabal Amman'],
            ['email' => 'demo.family@jst.test', 'destination' => 'The Royal Automobile Museum', 'title' => 'Demo submission: museum visit with family'],
            ['email' => 'demo.friends@jst.test', 'destination' => 'Darat al Funun', 'title' => 'Demo submission: art and gardens in Weibdeh'],
            ['email' => 'demo.budget@jst.test', 'destination' => 'Roman Theatre', 'title' => 'Demo submission: exploring downtown Amman'],
        ];

        foreach ($submissions as $submission) {
            $user = User::where('email', $submission['email'])->where('role', 'traveler')->first();
            $destination = Destination::where('name', $submission['destination'])
                ->whereHas('governorate', fn ($query) => $query->where('name', 'Amman'))
                ->first();

            if (! $user || ! $destination) {
                $this->command?->warn("ExperienceSeeder skipped {$submission['title']}: traveler or destination not found.");

                continue;
            }

            $user->experiences()->firstOrCreate(
                ['destination_id' => $destination->id, 'title' => $submission['title']],
                [
                    'body' => 'Local development placeholder awaiting a genuine traveler submission and moderation. Do not publish this text as a real review.',
                    'rating' => 4,
                    'social_url' => null,
                    'tags' => ['demo', 'awaiting moderation'],
                ]
            );
        }
    }
}
