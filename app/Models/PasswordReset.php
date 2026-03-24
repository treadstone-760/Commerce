<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['email', "expires_at", 'token'])]
class PasswordReset extends Model
{
    //
}
