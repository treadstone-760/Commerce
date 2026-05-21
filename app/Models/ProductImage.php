<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductImage extends Model
{
      protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => url('storage/'.$value),
        );
    }
}
