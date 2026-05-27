<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'product_id',
    'name',
])]
class ProductOption extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function ProductOptionValue()
    {
        return $this->hasMany(ProductOptionValue::class);
    }

    public function deleteProductOptionValue()
    {
        ProductOptionValue::where('product_option_id', $this->id)->delete();
    }

    protected static function booted()
    {
        static::deleting(function ($option) {
            $option->productOptionValues()->delete();
        });
    }
}
