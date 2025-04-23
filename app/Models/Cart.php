<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Cart",
 *     type="object",
 *     title="Cart",
 *     description="Data keranjang belanja",
 *     required={"quantity", "total_price", "product_id", "user_id"},
 *     @OA\Property(property="cart_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="total_price", type="number", format="float", example=150000.00),
 *     @OA\Property(property="product_id", type="integer", example=3),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-23T12:30:00Z"),
 *
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="product_id", type="integer", example=3),
 *         @OA\Property(property="name", type="string", example="Buku Tulis"),
 *         @OA\Property(property="price", type="number", format="float", example=75000.00)
 *     ),
 *
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="user_id", type="integer", example=5),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="john@example.com")
 *     )
 * )
 */

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'cart_id';
    protected $fillable = [
        'quantity',
        'total_price',
        'product_id', 
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'cart_id', 'cart_id');
    }
}
