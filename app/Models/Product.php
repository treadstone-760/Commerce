<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use League\Uri\Builder;

#[Fillable([
    'name',
    'description',
    'base_price',
    'currency',
    'category_id',
    'status',

])]
class Product extends Model
{
    public function productOption()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function productVariant()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeSearch(Builder $query, string $search)
    {

        if (! $search) {
            return $query;
        }

        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%');
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    // create a getter for images
  
}
