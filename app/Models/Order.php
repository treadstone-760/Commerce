<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'invoice_numnber',
    'status',
    'sub_total',
    'total_amount',
    'address_id',
    'user_id'
])]
class Order extends Model
{
    //
}