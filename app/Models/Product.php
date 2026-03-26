<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable([
    'name',
    'description',
    'base_price',
    'currency'
])]
class Product extends Model
{
    
}
