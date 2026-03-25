<?php

use App\Http\Controllers\Api\Admin\Category\CategoryController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPassword;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'registerUser']); 
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('forgot-password', [ForgotPassword::class, 'sendResetOtp']);
Route::post('verify-reset-password', [ForgotPassword::class, 'verifyResetPassword']);
Route::post('reset-password', [ForgotPassword::class, 'resetPassword']);



Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('add-category', [CategoryController::class, 'add']);
    Route::get('view-category', [CategoryController::class, 'viewAll']);
    Route::get('view-category/{id}', [CategoryController::class, 'viewSingle']); 
    Route::put('update-category/{id}', [CategoryController::class, 'update']);
    Route::delete('delete-category/{id}', [CategoryController::class, 'delete']);
    Route::put('status-update/{id}', [CategoryController::class, 'statusUpdate']);
});
