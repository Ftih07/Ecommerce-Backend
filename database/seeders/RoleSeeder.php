<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrator with full access',
            ],
            [
                'name' => 'customer',
                'description' => 'Regular customer',
            ],
            [
                'name' => 'seller',
                'description' => 'Store owner/seller',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
