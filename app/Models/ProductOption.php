<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable ([
    'product_id',
    'name'
])]
class ProductOption extends Model
{
    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function ProductOptionValue(){
        return $this->hasMany(ProductOptionValue::class);
    }
}
