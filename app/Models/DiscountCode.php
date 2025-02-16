<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $fillable = [
        'code', 'discount', 'valid_from', 'valid_to', 'is_active', 'to', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}