<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\Auth\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        try {
            if ($request->has('email') && $request->has('password')) {
                $validate = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

                if ($validate->fails()) {
                    return Res($validate->errors()->first(), 400);
                }
                return AuthService::login($request);
            }else if($request->has('phone')){
                $vali = Validator::make($request->all() , [
                    'phone' => 'required|digits:10'
                ]);
                if($vali->fails()){
                    return Res("Validation error" , 422 , $vali->errors()->toArray());
                }

                return  AuthService::loginMobile($request) ;
            }

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public function verifyOtp(Request $request){
        try{
            $validate = Validator::make($request->all() , [
                'phone' => 'required|digits:10',
                'otp' => 'required'
            ]);

            if($validate->fails()){
                return Res("Validation Error" , 422 , $validate->errors()->toArray());
            }
            return AuthService::verifyOtp($request);

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
