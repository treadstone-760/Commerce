<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVerificationOtp extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'type',
        'expired_at',
        'verified_at',
        'is_verified',
    ];
}
