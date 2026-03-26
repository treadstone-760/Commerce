<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'name',
    'slug',
    'description',
    'parent_id',
    'is_active',
    
])]
class Category extends Model
{
    

     public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
     public function product(){
        return $this->hasMany(Product::class);
    }
}
