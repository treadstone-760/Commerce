<?php

namespace App\Services\Auth;

use App\Mail\SendOptVerification;
use Exception;
use App\Models\User;
use App\Models\UserVerificationOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RegisterService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function register($request){
        try{
    DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // lets send a verification otp
            $rand = rand(100000, 999999);

            $createOtps = UserVerificationOtp::create([
                'user_id' => $user->id,
                'otp' => Hash::make($rand),
                'type' => 'email_verification_otp',
                'expired_at' => now()->addMinutes(10)
            ]);

            DB::commit();
            //queue this email
            Mail::to($user->email)->queue(new SendOptVerification($rand));

            return Res('Registration successful', 200, $user->toArray());
        }catch(Exception $e){
            DB::rollBack();
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res("Server Error",500);
        }
    }

    
}
