<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\StoreSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ProductImageSeeder;
use Database\Seeders\ReviewSeeder;
use Database\Seeders\PaymentSeeder;
use Database\Seeders\CartSeeder;
use Database\Seeders\OrderSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run all seeders in the correct order to maintain relationships
        $this->call([
            // 1. Core data (no dependencies)
            RoleSeeder::class,
            StoreSeeder::class,
            CategorySeeder::class,
            
            // 2. Users and role assignments
            UserSeeder::class,
            
            // 3. Products (depends on categories and stores)
            ProductSeeder::class,
            ProductImageSeeder::class,
            
            // 4. User-generated content and transactions
            ReviewSeeder::class,
            PaymentSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
        ]);

        // Note: The UserSeeder now handles user creation and role assignment
    }
}
