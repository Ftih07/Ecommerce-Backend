<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Casts\Attribute;


class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    protected $fillable = [
        'password',
        'email',
        'profile_image',
        'address',
        'name'
    ];

    protected $hidden = [
        'password', 
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
}
