<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ProductImage",
 *     type="object",
 *     title="Product Image",
 *     description="Gambar tambahan untuk produk",
 *     required={"product_images_id", "name", "product_id", "path"},
 *     @OA\Property(property="product_images_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="product_image_1.jpg"),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="path", type="string", example="path/to/image.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-23T12:30:00Z")
 * )
 */

class ProductImage extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_images_id';
    protected $fillable = [
        'name',
        'product_id',
        'path'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
