<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Validator;

class ForgotPassword extends Controller
{
    public function sendResetOtp(Request $request){
        try{

            $validate = Validator::make(request()->all() , [
                'email' => 'required|email|exists:users,email',
            ]);

            if($validate->fails()){
                return Res("Validation Error" , 422 , $validate->errors()->toArray());
            }
            return AuthService::sendResetOtp($request);

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
