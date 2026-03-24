<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                "email" => "required|email",
                "password" => "required"
            ]);

            if($validate->fails()){
                return Res($validate->errors()->first(),400);
            }
            
            return AuthService::login($request);

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
