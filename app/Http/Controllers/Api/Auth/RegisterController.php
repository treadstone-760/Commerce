<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Http\Request;
use App\Services\Auth\RegisterService;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Log;

class RegisterController
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        
    }

    public function registerUser(Request $request){
       try{

            $validate = Validator::make($request->all(), [
                'name' => 'required|string',
                "email" => 'required|email|unique:users,email',
                "phone" => 'required|digits:10|unique:users,phone',
                "password" => 'required|string',
                "password_confirmation" => 'required|string|same:password',
            ]);

            if ($validate->fails()) {
                return Res($validate->errors()->first(), 400);
            }

            return RegisterService::register($request);

            
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
