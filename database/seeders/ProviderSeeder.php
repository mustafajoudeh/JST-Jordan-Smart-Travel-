<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Governorate;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $amman = Governorate::where('name', 'Amman')->first();

        $providers = [
            [
                'email' => 'demo.provider.heritage@jst.test',
                'type' => 'company',
                'name' => 'JST Demo • Amman Heritage Walks',
                'category' => 'Guided Tours',
                'specializations' => ['history', 'architecture'],
            ],
            [
                'email' => 'demo.provider.guide@jst.test',
                'type' => 'guide',
                'name' => 'JST Demo • Local City Guide',
                'category' => 'City Tours',
                'specializations' => ['walking', 'culture'],
            ],
            [
                'email' => 'demo.provider.food@jst.test',
                'type' => 'company',
                'name' => 'JST Demo • Amman Food Experiences',
                'category' => 'Food Experiences',
                'specializations' => ['food', 'culture'],
            ],
        ];

        foreach ($providers as $data) {
            $user = User::where('email', $data['email'])->where('role', 'provider')->first();
            $category = Category::where('kind', 'service')->where('name', $data['category'])->first();

            if (! $user || ! $category) {
                $this->command?->warn("ProviderSeeder skipped {$data['name']}: provider account or service category not found.");

                continue;
            }

            if (Provider::where('user_id', $user->id)->exists()) {
                continue;
            }

            Provider::factory()->state([
                'user_id' => $user->id,
                'provider_type' => $data['type'],
                'display_name' => $data['name'],
                'description' => 'Local development profile only. This provider has not been verified, approved, or enabled to receive bookings.',
                'contact_email' => $user->email,
                'contact_phone' => null,
                'website_url' => null,
                'address' => null,
                'governorate_id' => $amman?->id,
                'category_id' => $category->id,
                'languages' => ['Arabic', 'English'],
                'specializations' => $data['specializations'],
            ])->create();
        }
    }
}
