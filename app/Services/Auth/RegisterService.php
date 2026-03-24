<?php

namespace App\Services\Auth;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);
            
            return Res('Registration successful', 200, $user->toArray());
        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res("Server Error",500);
        }
    }

    
}
