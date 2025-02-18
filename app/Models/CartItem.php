<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'product_name', 'quantity', 'price'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}