<?php

namespace App\Http\Controllers\Api\Admin\Products;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\Product\ProductService;

class ProductController extends Controller
{
    public function addProduct(Request $request)
    {
        if(!auth()->user()->can('products.add')){
            return Res("Unauthorized" , 401);
        }
        
        try {

            $validate = [
                'name' => 'required|string',
                'description' => 'required|string',
                'base_price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'currency' => 'required|string',
                'options' => 'nullable|array',
                'variants' => 'nullable|array',

            ];

            if ($request->has('options')) {
                $validate = array_merge($validate, [
                    'options.*.name' => 'required|string',

                    'options.*.values' => 'required|array',
                    'options.*.values.*' => 'required|string',
                ]);
            }
            if ($request->has('variants')) {
                $validate = array_merge($validate, [
                    'variants.*.sku' => 'required|string',
                    'variants.*.price' => 'required|numeric',
                    'variants.*.stock' => 'required|integer',
                    'variants.*.attributes' => 'required|array',
                ]);
            }

            $validate = Validator::make(request()->all(), $validate);
            if($validate->fails()){
                return Res("Validation Error" , 422 , $validate->errors()->toArray());
            }
            return ProductService::addProduct($request);

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public function showAllProduct(){
        try{
            if(!auth()->user()->can('products.show')){
                return Res("Unauthorized" , 401);
            }
            return ProductService::viewAll();
        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return Res('Server Error', 500);
        }
    }

    public function retrieveProductByCategory($id){
        try{
            if(!auth()->user()->can('products.show')){
                return Res("Unauthorized" , 401);
            }
            return ProductService::viewByCategory($id);
        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return Res('Server Error', 500);
        }
    }
}
