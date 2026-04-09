<?php

use App\Http\Controllers\Api\Admin\Category\CategoryController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPassword;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Products\ProductController;
use App\Http\Controllers\Api\Commerce\AddressController;
use App\Http\Controllers\Api\Commerce\CommerceHome;
use App\Http\Controllers\Api\Commerce\OrderController;


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
    Route::post('logout' , [AuthController::class, 'logout']);
});


//Commerce
Route::get('get/product' , [ CommerceHome::class, 'getProducts']);
Route::get('get/product/{id}' , [ CommerceHome::class, 'viewsingleProduct']);
Route::get('get/category' , [ CommerceHome::class, 'getCategories']);
Route::get('get/category/{id}' , [ CommerceHome::class, 'viewSingleCategoryWithProducts']);

//Cart
Route::post('add-to-cart/{id}' , [ OrderController::class, 'addToCart']);
Route::get('view-cart' , [ OrderController::class, 'viewCart']);
//Checkout
Route::post('checkout' , [ OrderController::class, 'checkout'])->middleware('auth:sanctum');
Route::post('checkout-webhook' , [ OrderController::class, 'paystackWebhook']);
Route::get('verify-payment/{id}' , [ OrderController::class, 'verifyPayment'])->middleware('auth:sanctum');

Route::get('my-order' , [ OrderController::class, 'myOrder'])->middleware('auth:sanctum');
Route::get('view-single-order/{id}' , [ OrderController::class, 'viewSingleOrder'])->middleware('auth:sanctum');

//shipping address
Route::post('add-address' , [ AddressController::class, 'addAddress'])->middleware('auth:sanctum');
Route::get('my-address' , [ AddressController::class, 'myAddress'])->middleware('auth:sanctum');
Route::get('view-single-address/{id}' , [ AddressController::class, 'viewSingle'])->middleware('auth:sanctum');
