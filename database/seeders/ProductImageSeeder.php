<?php

namespace Database\Seeders;

use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productImages = [
            // Images for Smartphone X200 (Product ID 1)
            [
                'name' => 'smartphone_front.jpg',
                'product_id' => 1,
                'path' => 'https://picsum.photos/id/3/800'
            ],
            [
                'name' => 'smartphone_back.jpg',
                'product_id' => 1,
                'path' => 'https://picsum.photos/id/4/800'
            ],

            // Images for Laptop Pro (Product ID 2)
            [
                'name' => 'laptop_open.jpg',
                'product_id' => 2,
                'path' => 'https://picsum.photos/id/5/800'
            ],
            [
                'name' => 'laptop_side.jpg',
                'product_id' => 2,
                'path' => 'https://picsum.photos/id/6/800'
            ],

            // Images for Denim Jeans (Product ID 3)
            [
                'name' => 'jeans_front.jpg',
                'product_id' => 3,
                'path' => 'https://picsum.photos/id/23/800'
            ],
            [
                'name' => 'jeans_back.jpg',
                'product_id' => 3,
                'path' => 'https://picsum.photos/id/24/800'
            ],

            // Images for Coffee Maker (Product ID 5)
            [
                'name' => 'coffee_maker_side.jpg',
                'product_id' => 5,
                'path' => 'https://picsum.photos/id/34/800'
            ],
            [
                'name' => 'coffee_maker_top.jpg',
                'product_id' => 5,
                'path' => 'https://picsum.photos/id/35/800'
            ],
        ];

        foreach ($productImages as $imageData) {
            ProductImage::create($imageData);
        }
    }
}
