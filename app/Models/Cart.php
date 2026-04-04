<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable([
    'user_id',
    'product_id',
    'price',
    'product_variant_id',
    'quantity',
    'status',
    'cart_id'
])]
class Cart extends Model
{
    protected $table = 'carts';

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function productVariant(){
        return $this->belongsTo(ProductVariant::class);
    }
}
