<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'sku',
    'price',
    'product_id',
    "stock"
])]
class ProductVariant extends Model
{
    //
}
