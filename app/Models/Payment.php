<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
