<?php

namespace App\Services\Auth;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
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

    public static function login($input){
        try{

            $credentials = $input->only('email', 'password');

            $user = User::where('email', $credentials['email'])->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return Res('Unauthorized', 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return Res('Login successful', 200, ['token' => $token]);

        }catch(Exception $e){
            Log::error([
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile()
            ]);
            return Res("Server Error",500);
        }
    }
}
