<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();
        $sellerRole = Role::where('name', 'seller')->first();

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'admin123',
            'profile_image' => 'https://randomuser.me/api/portraits/men/1.jpg',
            'address' => '123 Admin Street, Tech City'
        ]);
        $admin->roles()->attach($adminRole->role_id);

        // Create regular customer
        $customer = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'user123',
            'profile_image' => 'https://randomuser.me/api/portraits/women/1.jpg',
            'address' => '456 Customer Ave, Shopping Town'
        ]);
        $customer->roles()->attach($customerRole->role_id);

        // Create seller
        $seller = User::create([
            'name' => 'Test Seller',
            'email' => 'seller@example.com',
            'password' => 'sell123',
            'profile_image' => 'https://randomuser.me/api/portraits/men/2.jpg',
            'address' => '789 Merchant Blvd, Business District'
        ]);
        $seller->roles()->attach($sellerRole->role_id);

        // Create a few more regular customers
        $customers = [
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => 'password123',
                'profile_image' => 'https://randomuser.me/api/portraits/women/2.jpg',
                'address' => '101 Shopper Lane, Consumer City'
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'password' => 'password123',
                'profile_image' => 'https://randomuser.me/api/portraits/men/3.jpg',
                'address' => '202 Buyer Street, Market Town'
            ],
        ];

        foreach ($customers as $userData) {
            $user = User::create($userData);
            $user->roles()->attach($customerRole->role_id);
        }

        // Create another seller
        $anotherSeller = User::create([
            'name' => 'Sarah Vendor',
            'email' => 'sarah@example.com',
            'password' => 'password123',
            'profile_image' => 'https://randomuser.me/api/portraits/women/3.jpg',
            'address' => '303 Retail Road, Vendor Village'
        ]);
        $anotherSeller->roles()->attach($sellerRole->role_id);
    }
}
