<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $fillable = [
        'user_id',
        'address_line_1',
        'address_line_2',
        'city',
        'landmark',
        'gps_address',
        'delivery_instructions',
        'address_type',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
