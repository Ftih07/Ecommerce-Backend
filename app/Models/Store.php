<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
