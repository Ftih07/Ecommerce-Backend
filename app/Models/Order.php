<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     description="Data pesanan pengguna",
 *     required={"order_id", "final_price", "cart_id", "payment_id", "order_date"},
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="final_price", type="number", format="float", example=20000),
 *     @OA\Property(property="cart_id", type="integer", example=5),
 *     @OA\Property(property="payment_id", type="integer", example=3),
 *     @OA\Property(property="order_date", type="string", format="date", example="2025-04-23"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-23T12:30:00Z")
 * )
 */

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    protected $fillable = [
        'final_price',
        'cart_id',
        'payment_id',
        'order_date'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }
}
