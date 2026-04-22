<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use League\Uri\Builder;

#[Fillable([
    'name',
    'description',
    'base_price',
    'currency',
    "category_id",
    'status'

])]
class Product extends Model
{
    
    public function productOption(){
        return $this->hasMany(ProductOption::class);
    }

    public function productVariant(){
        return $this->hasMany(ProductVariant::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function scopeSearch(Builder $query, string $search){
        
        if(!$search){
            return $query;
        }

         return $query->where(function($query) use ($search){
                $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%');
            });
        }
                   
    }

   

}
