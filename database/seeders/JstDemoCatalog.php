<?php

namespace Database\Seeders;

final class JstDemoCatalog
{
    public static function destinations(): array
    {
        return [
            [
                'name' => 'Amman Citadel',
                'category' => 'Historical',
                'description' => 'A hilltop archaeological site overlooking downtown Amman, with remains from several periods of the city’s history.',
                'latitude' => 31.954017,
                'longitude' => 35.934868,
                'address' => 'Jabal Al-Qalaa, Amman',
                'duration' => 120,
                'cost' => 3.00,
                'tags' => ['history', 'archaeology', 'views'],
            ],
            [
                'name' => 'Roman Theatre',
                'category' => 'Historical',
                'description' => 'A restored Roman-era theatre set into the hillside in the heart of downtown Amman.',
                'latitude' => 31.951658,
                'longitude' => 35.939393,
                'address' => 'Al-Hashemi Street, Downtown Amman',
                'duration' => 90,
                'cost' => 2.00,
                'tags' => ['history', 'architecture', 'downtown'],
            ],
            [
                'name' => 'Rainbow Street',
                'category' => 'Cultural',
                'description' => 'A walkable Jabal Amman street known for cafés, small shops, and views across the older parts of the city.',
                'latitude' => 31.9493,
                'longitude' => 35.9257,
                'address' => 'Rainbow Street, Jabal Amman',
                'duration' => 120,
                'cost' => 8.00,
                'tags' => ['food', 'walking', 'city life'],
            ],
            [
                'name' => 'King Abdullah I Mosque',
                'category' => 'Religious',
                'description' => 'A landmark mosque in central Amman, recognized by its blue tiled dome and distinctive modern architecture.',
                'latitude' => 31.9616,
                'longitude' => 35.9131,
                'address' => 'Sulayman Al-Nabulsi Street, Amman',
                'duration' => 60,
                'cost' => 0.00,
                'tags' => ['architecture', 'religious heritage'],
            ],
            [
                'name' => 'The Jordan Museum',
                'category' => 'Cultural',
                'description' => 'A national museum in Ras Al-Ain presenting Jordan’s archaeology, history, and cultural heritage.',
                'latitude' => 31.945645,
                'longitude' => 35.926515,
                'address' => 'Ras Al-Ain, Ali Bin Abi Taleb Street, Amman',
                'duration' => 150,
                'cost' => 5.00,
                'tags' => ['museum', 'history', 'culture'],
            ],
            [
                'name' => 'Darat al Funun',
                'category' => 'Cultural',
                'description' => 'An arts center in Jabal Al-Weibdeh set among restored buildings and gardens, with rotating exhibitions and cultural programs.',
                'latitude' => 31.953831,
                'longitude' => 35.930721,
                'address' => '13 Nadeem al-Mallah Street, Jabal Al-Weibdeh, Amman',
                'duration' => 90,
                'cost' => 0.00,
                'tags' => ['art', 'culture', 'gardens'],
            ],
            [
                'name' => 'The Royal Automobile Museum',
                'category' => 'Cultural',
                'description' => 'A museum in King Hussein Park displaying vehicles connected with Jordan’s modern history.',
                'latitude' => 31.9842,
                'longitude' => 35.8257,
                'address' => 'King Hussein Park, Amman',
                'duration' => 120,
                'cost' => 5.00,
                'tags' => ['museum', 'history', 'family'],
            ],
            [
                'name' => 'Jabal Al-Weibdeh',
                'category' => 'Cultural',
                'description' => 'A central Amman neighborhood of hillside lanes, historic homes, independent cafés, and local arts spaces.',
                'latitude' => 31.9580,
                'longitude' => 35.9228,
                'address' => 'Jabal Al-Weibdeh, Amman',
                'duration' => 120,
                'cost' => 6.00,
                'tags' => ['walking', 'architecture', 'cafes'],
            ],
        ];
    }

    public static function activities(): array
    {
        return [
            'Amman Citadel' => [
                ['name' => 'Explore the Umayyad Palace', 'description' => 'Follow the remains of the Umayyad Palace complex and take in the views over downtown Amman.', 'duration' => 60, 'cost' => 0.00],
                ['name' => 'Walk the Temple of Hercules ridge', 'description' => 'See the monumental columns and archaeological remains along the Citadel ridge.', 'duration' => 45, 'cost' => 0.00],
            ],
            'Roman Theatre' => [
                ['name' => 'Visit the Roman Theatre', 'description' => 'Explore the seating tiers and stage of the restored Roman theatre in downtown Amman.', 'duration' => 60, 'cost' => 0.00],
            ],
            'Rainbow Street' => [
                ['name' => 'Walk Rainbow Street', 'description' => 'Explore the cafés, shops, and stairways around Rainbow Street at an easy pace.', 'duration' => 90, 'cost' => 0.00],
            ],
            'King Abdullah I Mosque' => [
                ['name' => 'Admire the blue dome and architecture', 'description' => 'Take time to view the mosque’s exterior architecture and central blue dome. Check visitor access locally before entering.', 'duration' => 45, 'cost' => 0.00],
            ],
            'The Jordan Museum' => [
                ['name' => 'Explore Jordanian heritage galleries', 'description' => 'Spend time with the museum’s archaeological and cultural collections.', 'duration' => 120, 'cost' => 0.00],
            ],
            'Darat al Funun' => [
                ['name' => 'Visit the contemporary art spaces', 'description' => 'Explore the exhibition spaces and gardens; check the current program and opening schedule before visiting.', 'duration' => 75, 'cost' => 0.00],
            ],
            'The Royal Automobile Museum' => [
                ['name' => 'Explore Jordan’s motoring history', 'description' => 'View the collection of vehicles and related displays in King Hussein Park.', 'duration' => 90, 'cost' => 0.00],
            ],
            'Jabal Al-Weibdeh' => [
                ['name' => 'Walk the Jabal Al-Weibdeh lanes', 'description' => 'Take a self-guided walk through the neighborhood’s hillside streets and local art spaces.', 'duration' => 90, 'cost' => 0.00],
            ],
        ];
    }
}
