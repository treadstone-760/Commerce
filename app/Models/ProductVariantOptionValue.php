<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'product_variant_id',
    'option_value_id'
    
])]
class ProductVariantOptionValue extends Model
{
    public function product_variant(){
        return $this->belongsTo(ProductVariant::class);
    }
    public function ProductOptionValue(){
        return $this->belongsTo(ProductOptionValue::class);
    }
}
