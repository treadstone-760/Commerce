<?php

namespace App\Http\Controllers\Api\Admin\Customers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //

    public function viewallCustomer()
    {
        try{
            $per_page = request('per_page') ?? 10;
            $customers = User::where('user_type' , 'customer')->paginate($per_page);
            return Res("Success" , 200 , $customers->toArray());
        }catch(Exception $e){
            Log::error(
                [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]
            );
            return Res('Something went wrong' , 500);
        }
    }

    public function viewSingleCustomer($id){
        try{
            $customer = User::with([
                'order'=>fn($query) => $query->with(['orderItems' => fn($query) => $query->with(['product' , 'productVariant']) , "shippingAddress"]),
               
            ])->where('id' , $id)->first();

            if(!$customer){
                return Res('Customer not found' , 404);
            }
          return Res("Success" , 200 , $customer->toArray());
           
        }catch(Exception $e){
            Log::error(
                [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]
            );
            return Res('Something went wrong' , 500);
        }
    }
}
