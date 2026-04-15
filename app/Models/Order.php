<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'invoice_number',
    'status',
    'sub_total',
    'total_amount',
    'address_id',
    'user_id',
    'paid_at'
])]
class Order extends Model
{
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}