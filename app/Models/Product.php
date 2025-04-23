<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Data produk yang tersedia",
 *     required={"product_id", "name", "price", "category_id"},
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Smartphone"),
 *     @OA\Property(property="thumbnail_image", type="string", example="path/to/thumbnail.jpg"),
 *     @OA\Property(property="stock", type="integer", example=100),
 *     @OA\Property(property="status", type="string", example="available"),
 *     @OA\Property(property="description", type="string", example="A high-quality smartphone"),
 *     @OA\Property(property="price", type="number", format="float", example=199.99),
 *     @OA\Property(property="store_id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-23T12:30:00Z")
 * )
 */

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $fillable = [
        'name',
        'thumbnail_image',
        'stock',
        'status',
        'description',
        'price',
        'store_id',
        'category_id' 
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }
}
