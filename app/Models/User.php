<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Budi"),
 *     @OA\Property(property="email", type="string", format="email", example="budi@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="secret123"),
 *     @OA\Property(property="profile_image", type="string", nullable=true, example="http://example.com/image.jpg"),
 *     @OA\Property(property="address", type="string", nullable=true, example="Jl. Mawar No. 5"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-30T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-30T12:30:00Z")
 * )
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'user_id';
    protected $fillable = [
        'password',
        'email',
        'profile_image',
        'address',
        'name',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcrypt($value),
        );
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'user_id');
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Check if user has a specific role
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        return (bool) $this->roles->whereIn('name', $roles)->count();
    }

    /**
     * Check if user has any role
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        return (bool) $this->roles->whereIn('name', $roles)->count();
    }
}
