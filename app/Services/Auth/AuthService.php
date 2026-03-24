<?php

namespace App\Services\Auth;

use App\Jobs\SendSmeJob;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function login($input)
    {
        try {

            $credentials = $input->only('email', 'password');

            $user = User::where('email', $credentials['email'])->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return Res('Unauthorized', 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return Res('Login successful', 200, ['token' => $token]);

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public static function loginMobile($input)
    {
        try {

            $credentials = $input->only('phone');

            $user = User::where('phone', $credentials['phone'])->first();

            if (! $user) {
                return Res('Account not found', 401);
            }
            $otp = rand(100000, 999999);
            
            $user->otp()->where('nullified_at', null)->where('verified', false)->update([
                'nullified_at' => now(),    
            ]);
             
            $user->otp()->create([
                'otp' => $otp,
                'verified' => false,
                'nullified_at' => null,
                'expired_at' => now()->addMinutes(5),
            ]);

            $message = "Your verification code is {$otp}. It will expire in 5 minutes.
                         Please do not share this code with anyone. ";
            $sms =  SendSmeJob::dispatch($message , $user->phone);

            return Res('OTP sent successfully', 200);

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }
}
