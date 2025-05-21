<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payments = [
            [
                'payment_method' => 'Credit Card',
                'status' => 'paid',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30)
            ],
            [
                'payment_method' => 'PayPal',
                'status' => 'paid',
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25)
            ],
            [
                'payment_method' => 'Bank Transfer',
                'status' => 'paid',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20)
            ],
            [
                'payment_method' => 'Credit Card',
                'status' => 'pending',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2)
            ],
            [
                'payment_method' => 'PayPal',
                'status' => 'failed',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5)
            ],
        ];

        foreach ($payments as $paymentData) {
            Payment::create($paymentData);
        }
    }
}
