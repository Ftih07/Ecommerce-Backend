<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We'll create orders for the first 2 cart items
        // This leaves 3 carts without orders for testing cart functionality

        $orders = [
            [
                'cart_id' => 1,
                'payment_id' => 1,
                'final_price' => Cart::find(1)->total_price,
                'order_date' => now()->subDays(28),
                'created_at' => now()->subDays(28),
                'updated_at' => now()->subDays(28)
            ],
            [
                'cart_id' => 3,
                'payment_id' => 2,
                'final_price' => Cart::find(3)->total_price,
                'order_date' => now()->subDays(23),
                'created_at' => now()->subDays(23),
                'updated_at' => now()->subDays(23)
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create($orderData);
        }
    }
}
