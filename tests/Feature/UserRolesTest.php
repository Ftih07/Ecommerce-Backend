<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserRolesTest extends TestCase
{
    use RefreshDatabase;    /**
     * Test that admins can update user roles.
     */
    public function test_admin_can_update_user_roles(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $sellerRole = Role::create(['name' => 'seller']);
        $customerRole = Role::create(['name' => 'customer']);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $admin->roles()->attach($adminRole->role_id);

        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $user->roles()->attach($customerRole->role_id);

        // Login as admin
        $token = $admin->createToken('test-token')->plainTextToken;

        // Update user roles
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->put('/api/users/' . $user->user_id . '/roles', [
            'roles' => ['seller', 'customer'] // Change to seller and customer
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User roles updated successfully',
            ]);

        // Check database records
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->user_id,
            'role_id' => $sellerRole->role_id,
        ]);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->user_id,
            'role_id' => $customerRole->role_id,
        ]);
        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->user_id,
            'role_id' => $adminRole->role_id,
        ]);
    }    /**
     * Test that non-admins cannot update user roles.
     */
    public function test_non_admin_cannot_update_user_roles(): void
    {
        // Create roles
        $sellerRole = Role::create(['name' => 'seller']);
        $customerRole = Role::create(['name' => 'customer']);

        // Create customer user
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);
        $customer->roles()->attach($customerRole->role_id);

        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $user->roles()->attach($customerRole->role_id);

        // Login as customer
        $token = $customer->createToken('test-token')->plainTextToken;

        // Attempt to update user roles
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->put('/api/users/' . $user->user_id . '/roles', [
            'roles' => ['seller'] // Try to change to seller
        ]);

        // Should be forbidden
        $response->assertStatus(403);
    }
}
