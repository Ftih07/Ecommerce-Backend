<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Store",
 *     type="object",
 *     title="Store",
 *     description="Data toko tempat produk dijual",
 *     required={"store_id", "name", "city"},
 *     @OA\Property(property="store_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="TechStore"),
 *     @OA\Property(property="city", type="string", example="New York"),
 *     @OA\Property(property="profile_image", type="string", example="path/to/profile_image.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-23T12:30:00Z")
 * )
 */

class Store extends Model
{
    use HasFactory;

    protected $primaryKey = 'store_id';
    protected $fillable = [
        'name',
        'city',
        'profile_image'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'store_id', 'store_id');
    }
}
