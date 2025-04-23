<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     title="Payment",
 *     description="Informasi pembayaran",
 *     required={"payment_id", "payment_method", "status"},
 *     @OA\Property(property="payment_id", type="integer", example=1),
 *     @OA\Property(property="payment_method", type="string", example="Credit Card"),
 *     @OA\Property(property="status", type="string", enum={"pending", "paid", "failed"}, example="paid"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-23T12:30:00Z")
 * )
 */

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    protected $fillable = [
        'payment_method',
        'status'
    ];

    public function order()
    {
        return $this->hasOne(Order::class, 'payment_id', 'payment_id');
    }
}
