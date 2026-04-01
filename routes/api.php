<?php

use App\Http\Controllers\Api\Admin\Category\CategoryController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPassword;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Products\ProductController;
use App\Http\Controllers\Api\Commerce\CommerceHome;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'registerUser']); 
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('forgot-password', [ForgotPassword::class, 'sendResetOtp']);
Route::post('verify-reset-password', [ForgotPassword::class, 'verifyResetPassword']);
Route::post('reset-password', [ForgotPassword::class, 'resetPassword']);



//Category
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('add-category', [CategoryController::class, 'add']);
    Route::get('view-category', [CategoryController::class, 'viewAll']);
    Route::get('view-category/{id}', [CategoryController::class, 'viewSingle']); 
    Route::put('update-category/{id}', [CategoryController::class, 'update']);
    Route::delete('delete-category/{id}', [CategoryController::class, 'delete']);
    Route::put('status-update/{id}', [CategoryController::class, 'statusUpdate']);
});

//Products
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('add-product', [ProductController::class, 'addProduct']);
    Route::get('view-all-product', [ProductController::class, 'showAllProduct']); 
    Route::get('view-product-by-category/{id}', [ProductController::class, 'retrieveProductByCategory']);
    Route::get('view-single-product/{id}', [ProductController::class, 'retrieveSingleProduct']);
    Route::post('change-product-status/{id}', [ProductController::class, 'changeProductStatus']);
});


//Commerce
Route::get('get/product' , [ CommerceHome::class, 'getProducts']);
Route::get('get/product/{id}' , [ CommerceHome::class, 'viewsingleProduct']);
Route::get('get/category' , [ CommerceHome::class, 'getCategories']);
Route::get('get/category/{id}' , [ CommerceHome::class, 'viewSingleCategoryWithProducts']);

