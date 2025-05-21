<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Tech Emporium',
                'city' => 'San Francisco',
                'profile_image' => 'https://picsum.photos/id/1/200'
            ],
            [
                'name' => 'Fashion Boutique',
                'city' => 'New York',
                'profile_image' => 'https://picsum.photos/id/20/200'
            ],
            [
                'name' => 'Home Essentials',
                'city' => 'Chicago',
                'profile_image' => 'https://picsum.photos/id/42/200'
            ],
            [
                'name' => 'Sports World',
                'city' => 'Boston',
                'profile_image' => 'https://picsum.photos/id/60/200'
            ],
            [
                'name' => 'Kitchen Paradise',
                'city' => 'Seattle',
                'profile_image' => 'https://picsum.photos/id/30/200'
            ],
        ];

        foreach ($stores as $storeData) {
            Store::create($storeData);
        }
    }
}
