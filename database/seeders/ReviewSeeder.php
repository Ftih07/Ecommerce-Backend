<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            // Reviews for Smartphone X200 (Product ID 1)
            [
                'user_id' => 2, // Test User
                'product_id' => 1,
                'rating' => 5,
                'review' => 'Amazing phone! The camera quality is exceptional and battery life is great.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10)
            ],
            [
                'user_id' => 4, // Jane Smith
                'product_id' => 1,
                'rating' => 4,
                'review' => 'Very good phone, but a bit expensive. Still worth the purchase.',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7)
            ],

            // Reviews for Laptop Pro (Product ID 2)
            [
                'user_id' => 2, // Test User
                'product_id' => 2,
                'rating' => 5,
                'review' => 'Perfect for work and entertainment. Fast performance and great display!',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15)
            ],

            // Reviews for Denim Jeans (Product ID 3)
            [
                'user_id' => 5, // Bob Johnson
                'product_id' => 3,
                'rating' => 3,
                'review' => 'Decent quality but sizing runs small. Consider ordering one size up.',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20)
            ],

            // Reviews for Coffee Maker (Product ID 5)
            [
                'user_id' => 4, // Jane Smith
                'product_id' => 5,
                'rating' => 5,
                'review' => 'Best coffee maker I\'ve owned! Easy to use and clean.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5)
            ],
        ];

        foreach ($reviews as $reviewData) {
            Review::create($reviewData);
        }
    }
}
