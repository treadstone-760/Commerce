<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'product_option_id',
    'value',
])]
class ProductOptionValue extends Model
{
    public function ProductOption(){
        return $this->belongsTo(ProductOption::class);
    }

    public function productvariantOptionValue(){
        return $this->hasMany(ProductVariantOptionValue::class);
    }

    
}
