<?php

namespace App\Services\Auth;

use App\Jobs\SendSmsJob;
use App\Mail\ForgetPassword;
use App\Models\PasswordReset;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
                'otp' => Hash::make($otp),
                'verified' => false,
                'nullified_at' => null,
                'expired_at' => now()->addMinutes(5),
            ]);

            $message = "Your verification code is {$otp}. It will expire in 5 minutes. Please do not share this code with anyone. ";
            $sms = SendSmsJob::dispatch($message, $user->phone);

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

    public static function verifyOtp($request)
    {
        try {
            $user = User::where('phone', $request->phone)->first();
            $otp = $user->otp()
                ->where('nullified_at', null)
                ->where('verified', false)
                ->first();

            if ($otp->expired_at < now()) {
                // return 123;
                return Res('OTP has expired', 400);
            }

            if (Hash::check($request->otp, $otp->otp)) {
                $otp = $otp->update([
                    'nullified_at' => now(),
                    'verified' => true,
                ]);
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

    public static function sendResetOtp($request)
    {
        try {

            $user = User::where('email', $request->email)->first();
            $otp = rand(100000, 999999);

            $insert = PasswordReset::updateOrCreate(
                ['email' => $request->email],
                [
                    'token' => Hash::make($otp),
                    'expires_at' => now()->addMinutes(5),
                ]);
            Log::info($otp);
            Mail::to($request->email)->queue(new ForgetPassword($otp));

            return Res('OTP sent successfully. Please kindly check your email for the verification code.', 200);

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }

    }

    public static function verify_otp($request)
    {
        try {
            $confirm_otp = PasswordReset::where('email', $request->email)->where('expires_at', '>', now())->first();


            if (! $confirm_otp) {
               return Res('Invalid OTP', 400);
            }

            // ✅ Check against reset_token (not token)
            if (Hash::check($request->otp, $confirm_otp->token)) {

                $user = User::where('email', $request->email)->first();

                if (! $user) {
                    return response()->json(['message' => 'User not found'], 404);
                }

                // Generate new reset token
                $newToken = Str::uuid()->toString();

                $confirm_otp->update([
                    'reset_token' => Hash::make($newToken), 
                    'reset_token_expires_at' => now()->addMinutes(5),
                    "expires_at"=> now()
                ]);

                return Res('Password reset successful', 200, ['reset_tokens' => $newToken]);
            }
            return Res('Invalid OTP', 400);

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
