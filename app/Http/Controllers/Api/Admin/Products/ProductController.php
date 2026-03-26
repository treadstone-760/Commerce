<?php

namespace App\Http\Controllers\Api\Admin\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function addProduct(Request $request)
    {
        try{

            $validate = Validator::make(request()->all() , [
                    'name' => 'required|string',
                    'description' => 'required|string',
                    'base_price' => 'required|numeric',
                    'category_id' => 'required|exists:categories,id',
            ]);

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
