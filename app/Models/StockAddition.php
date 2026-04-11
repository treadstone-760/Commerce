<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['product_variant_id','product_id','user_id', 'quantity'])]
class StockAddition extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function productVariant(){
        return $this->belongsTo(ProductVariant::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
