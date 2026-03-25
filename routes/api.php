<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPassword;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'registerUser']); 
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('forgot-password', [ForgotPassword::class, 'sendResetOtp']);
Route::post('verify-reset-password', [ForgotPassword::class, 'verifyResetPassword']);

