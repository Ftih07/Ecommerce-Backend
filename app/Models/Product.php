<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Product data schema",
 *     required={"product_id", "name", "price", "stock", "status", "store_id", "category_id"},
 * 
 *     @OA\Property(property="product_id", type="integer", example=1, description="Product primary key"),
 *     @OA\Property(property="name", type="string", example="Smartphone X200", description="Name of the product"),
 *     @OA\Property(property="thumbnail_image", type="string", nullable=true, example="images/products/thumb.jpg", description="Path to the thumbnail image"),
 *     @OA\Property(property="stock", type="integer", example=50, description="Current stock quantity"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active", description="Availability status of the product"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Latest smartphone with high-resolution display", description="Detailed product description"),
 *     @OA\Property(property="price", type="number", format="float", example=499.99, description="Price of the product"),
 *     @OA\Property(property="store_id", type="integer", example=1, description="Foreign key referencing store"),
 *     @OA\Property(property="category_id", type="integer", example=2, description="Foreign key referencing category"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T12:00:00Z", description="Record creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-24T08:30:00Z", description="Record last update timestamp")
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
