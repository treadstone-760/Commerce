<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
#[Fillable([
   'product_id',
    'user_id',
    'device_id',
   
])]
class ViewedProduct extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
