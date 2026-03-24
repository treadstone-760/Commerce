<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'otp', 'expired_at', 'verified_at', 'nullified_at'])]

class AuthOtp extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
