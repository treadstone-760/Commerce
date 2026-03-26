<?php

namespace App\Services\Product;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

        public static function addProduct($request){
            try{
                //save into products table
                //start transaction
                DB::beginTransaction();
                $product = Product::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'base_price' => $request->base_price,
                    'currency' => $request->currency,
                    'category_id' => $request->category_id,
                    'is_active' => $request->is_active,
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
