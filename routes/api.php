<?php

use App\Http\Controllers\Api\Admin\Category\CategoryController;
use App\Http\Controllers\Api\Admin\Customers\CustomerController;
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
use App\Http\Controllers\Api\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Api\Admin\UserManagement\UserManagementController;
use App\Http\Controllers\Api\Admin\Reports\ReportController;

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
Route::post('checkout' , [ OrderController::class, 'checkout'])->middleware('auth:sanctum' , 'active');
Route::post('checkout-webhook' , [ OrderController::class, 'paystackWebhook']);
Route::get('verify-payment/{id}' , [ OrderController::class, 'verifyPayment'])->middleware('auth:sanctum');

Route::get('my-order' , [ OrderController::class, 'myOrder'])->middleware('auth:sanctum' , 'active');
Route::get('view-single-order/{id}' , [ OrderController::class, 'viewSingleOrder'])->middleware('auth:sanctum' , 'active');

//shipping address
Route::post('add-address' , [ AddressController::class, 'addAddress'])->middleware('auth:sanctum' , 'active');
Route::get('my-address' , [ AddressController::class, 'myAddress'])->middleware('auth:sanctum' , 'active');
Route::get('view-single-address/{id}' , [ AddressController::class, 'viewSingle'])->middleware('auth:sanctum' , 'active');
Route::put('update-address/{id}' , [ AddressController::class, 'update'])->middleware('auth:sanctum' , 'active');
Route::put('default-address/{id}' , [ AddressController::class, 'defaultAddress'])->middleware('auth:sanctum' , 'active');

//Dashboard
Route::get('dashboard' , [ DashboardController::class, 'index'])->middleware('auth:sanctum' , 'active');


//Admin Customers
Route::get('view-customers' , [ CustomerController::class, 'viewallCustomer'])->middleware('auth:sanctum' , 'active');
Route::get('view-single-customer-details/{id}' , [ CustomerController::class, 'viewSingleCustomer'])->middleware('auth:sanctum' , 'active');
Route::post('change-customer-status/{id}' , [ CustomerController::class, 'changeCustomerStatus'])->middleware('auth:sanctum' , 'active');

           
//Admin User
Route::post('create-admin' , [UserManagementController::class , 'store'])->middleware('auth:sanctum' , 'active');
Route::get('view-admin' , [UserManagementController::class , 'viewAllAdmins'])->middleware(['auth:sanctum' , 'active']);
Route::get('view-admin/{id}' , [UserManagementController::class , 'viewSingleAdmin'])->middleware('auth:sanctum' , 'active');
Route::put('update-admin/{id}' , [UserManagementController::class , 'updateAdmin'])->middleware('auth:sanctum' , 'active');
Route::put('change-admin-status/{id}' , [UserManagementController::class , 'changeAdminStatus'])->middleware('auth:sanctum' , 'active');

//Role Permission
Route::post('create-role' , [UserManagementController::class , 'createNewRole'])->middleware('auth:sanctum' , 'active');
Route::get('view-role' , [UserManagementController::class , 'viewAllRoles'])->middleware('auth:sanctum' , 'active');
Route::get('get/role/{id}' , [UserManagementController::class , 'viewSingleRole'])->middleware('auth:sanctum' , 'active');
Route::put('update-role/{id}' , [UserManagementController::class , 'updateRole'])->middleware('auth:sanctum' , 'active');

//Permission
Route::get('view-permission' , [UserManagementController::class , 'viewAllPermissions'])->middleware('auth:sanctum' , 'active');
// Route::post('create-permission' , [UserManagementController::class , 'createNewPermission'])->middleware('auth:sanctum' , 'active');
Route::post('assign-permission-to-role/{id}' , [UserManagementController::class , 'assignPermissionToRole'])->middleware('auth:sanctum' , 'active');


Route::get('reports/get-revenue' , [ReportController::class , 'salesReport'])->middleware('auth:sanctum' , 'active');