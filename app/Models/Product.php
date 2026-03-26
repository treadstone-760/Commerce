<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable([
    'name',
    'description',
    'base_price',
    'currency',
    "category_id"

])]
class Product extends Model
{
    
    public function ProductOption(){
        return $this->hasMany(ProductOption::class);
    }

    public function ProductVariant(){
        return $this->hasMany(ProductVariant::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

   

}
