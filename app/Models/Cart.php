<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable([
    'user_id',
    'product_id',
    'price',
    'quantity',
    'status',
    'cart_id'
])]
class Cart extends Model
{
    protected $table = 'carts';
}
