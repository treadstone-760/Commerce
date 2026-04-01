<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'order_id',
    'product_id',
    'quantity',
    'total_price'
])]
class OrderItem extends Model
{
    //
}
