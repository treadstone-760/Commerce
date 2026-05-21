<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductVariantImage extends Model
{
       protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => url('storage/'.$value),
        );
    }
}
