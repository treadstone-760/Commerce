<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable ([
    'product_id',
    'name'
])]
class ProductOption extends Model
{
    //
}
