<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carts = [
            // Test User's cart items
            [
                'user_id' => 2, // Test User
                'product_id' => 1, // Smartphone X200
                'quantity' => 1
            ],
            [
                'user_id' => 2, // Test User
                'product_id' => 5, // Coffee Maker
                'quantity' => 2
            ],

            // Jane's cart items
            [
                'user_id' => 4, // Jane Smith
                'product_id' => 2, // Laptop Pro
                'quantity' => 1
            ],

            // Bob's cart items
            [
                'user_id' => 5, // Bob Johnson
                'product_id' => 7, // Yoga Mat
                'quantity' => 1
            ],
            [
                'user_id' => 5, // Bob Johnson
                'product_id' => 8, // Basketball
                'quantity' => 1
            ],
        ];

        foreach ($carts as $cartData) {
            // Calculate the total price based on product price and quantity
            $product = Product::find($cartData['product_id']);
            $totalPrice = $product->price * $cartData['quantity'];

            Cart::create([
                'user_id' => $cartData['user_id'],
                'product_id' => $cartData['product_id'],
                'quantity' => $cartData['quantity'],
                'total_price' => $totalPrice
            ]);
        }
    }
}
