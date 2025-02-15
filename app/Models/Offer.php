<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'oldprice',
        'newprice',
        'category',
        'image_name',
    ];
}
