<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Electronics - Store 1
            [
                'name' => 'Smartphone X200',
                'thumbnail_image' => 'https://picsum.photos/id/1/300',
                'stock' => 50,
                'status' => 'active',
                'description' => 'Latest flagship smartphone with 5G capabilities and 48MP camera',
                'price' => 899.99,
                'store_id' => 1,
                'category_id' => 1
            ],
            [
                'name' => 'Laptop Pro',
                'thumbnail_image' => 'https://picsum.photos/id/2/300',
                'stock' => 25,
                'status' => 'active',
                'description' => '15-inch laptop with 16GB RAM and 512GB SSD',
                'price' => 1299.99,
                'store_id' => 1,
                'category_id' => 1
            ],

            // Clothing - Store 2
            [
                'name' => 'Denim Jeans',
                'thumbnail_image' => 'https://picsum.photos/id/21/300',
                'stock' => 100,
                'status' => 'active',
                'description' => 'Classic blue denim jeans, straight fit',
                'price' => 49.99,
                'store_id' => 2,
                'category_id' => 2
            ],
            [
                'name' => 'Cotton T-Shirt',
                'thumbnail_image' => 'https://picsum.photos/id/22/300',
                'stock' => 200,
                'status' => 'active',
                'description' => 'Comfortable 100% cotton t-shirt in various colors',
                'price' => 19.99,
                'store_id' => 2,
                'category_id' => 2
            ],

            // Home & Kitchen - Store 3
            [
                'name' => 'Coffee Maker',
                'thumbnail_image' => 'https://picsum.photos/id/30/300',
                'stock' => 30,
                'status' => 'active',
                'description' => 'Programmable coffee maker with built-in grinder',
                'price' => 129.99,
                'store_id' => 3,
                'category_id' => 3
            ],
            [
                'name' => 'Cookware Set',
                'thumbnail_image' => 'https://picsum.photos/id/31/300',
                'stock' => 20,
                'status' => 'active',
                'description' => '10-piece non-stick cookware set with glass lids',
                'price' => 199.99,
                'store_id' => 3,
                'category_id' => 3
            ],

            // Sports - Store 4
            [
                'name' => 'Yoga Mat',
                'thumbnail_image' => 'https://picsum.photos/id/60/300',
                'stock' => 75,
                'status' => 'active',
                'description' => 'Non-slip exercise yoga mat with carrying strap',
                'price' => 29.99,
                'store_id' => 4,
                'category_id' => 5
            ],
            [
                'name' => 'Basketball',
                'thumbnail_image' => 'https://picsum.photos/id/61/300',
                'stock' => 40,
                'status' => 'active',
                'description' => 'Official size basketball for indoor/outdoor use',
                'price' => 24.99,
                'store_id' => 4,
                'category_id' => 5
            ],

            // Kitchen items - Store 5
            [
                'name' => 'Knife Set',
                'thumbnail_image' => 'https://picsum.photos/id/32/300',
                'stock' => 15,
                'status' => 'active',
                'description' => '6-piece professional chef knife set with block',
                'price' => 89.99,
                'store_id' => 5,
                'category_id' => 3
            ],
            [
                'name' => 'Blender',
                'thumbnail_image' => 'https://picsum.photos/id/33/300',
                'stock' => 25,
                'status' => 'active',
                'description' => 'High-speed blender for smoothies and food processing',
                'price' => 69.99,
                'store_id' => 5,
                'category_id' => 3
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
