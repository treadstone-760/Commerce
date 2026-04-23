<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
// use League\Uri\Builder;

#[Fillable([
    'invoice_number',
    'status',
    'sub_total',
    'total_amount',
    'address_id',
    'user_id',
    'paid_at',
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


    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'address_id');
    }

    public function scopeFileterRevenueByMonths(Builder $builder, $from, $to)
    {
        if (! ($from && $to)) {
            return $builder;
        }

        return $builder->whereBetween('created_at', [$from, $to]);

    }
}
