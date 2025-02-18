<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'discount'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function calculateTotalPrice()
    {
        $this->total_price = $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $this->total_price -= $this->discount;
        $this->save();
    }
}