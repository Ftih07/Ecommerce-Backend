<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'admin123',
        ]);

        // Assign admin role
        $admin->roles()->attach(1); // 1 = admin role

        // Create regular test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'user123',
        ]);

        // Assign customer role
        $user->roles()->attach(2); // 2 = customer role
    }
}
