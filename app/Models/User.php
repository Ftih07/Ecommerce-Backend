<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Jika ingin gunakan default Laravel Auth
use Illuminate\Database\Eloquent\Model; // Jika tidak menggunakan default Auth, pakai Model biasa

class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    protected $fillable = [
        'password',
        'email',
        'profile_image',
        'address'
    ];

    // Jika pakai bcrypt, set mutator:
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'user_id');
    }
}
