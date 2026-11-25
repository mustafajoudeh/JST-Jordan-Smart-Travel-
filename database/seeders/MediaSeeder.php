<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        if (Media::query()->exists()) {
            return;
        }

        // One primary image row per published destination.
        // file_path values are placeholders: no physical files are created.
        Destination::where('status', 'published')->get()->each(
            fn (Destination $destination) => Media::factory()
                ->primary()
                ->state(['destination_id' => $destination->id])
                ->create()
        );
    }
}