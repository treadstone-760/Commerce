<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantOptionValue;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function addProduct($request)
    {
        try {

            $optionValueMap = [];
            // save into products table
            // start transaction
            DB::beginTransaction();
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'currency' => $request->currency,
                'category_id' => $request->category_id,
                'is_active' => $request->is_active,
            ]);

            if ($request->has('options')) {
                foreach ($request->options as $option) {
                    $product_options = ProductOption::create([
                        'name' => $option['name'],
                        'product_id' => $product->id,
                    ]);
                    foreach ($option['values'] as $value) {
                        $product_option_values = ProductOptionValue::create([
                            'product_option_id' => $product_options->id,
                            'value' => $value,
                        ]);

                        $optionValueMap[$option['name']][$value] = $product_option_values->id;

                    }

                }

            }

            // return $optionValueMap;

            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    $_variant = ProductVariant::create([
                        'sku' => $variant['sku'],
                        'product_id' => $product->id,
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                        // 'attributes' => $variant['attributes']
                    ]);

                    foreach ($variant['attributes'] as $attribute) {

                        foreach (array_values($optionValueMap) as $value) {
                            if (isset($value[$attribute])) {
                                ProductVariantOptionValue::create([
                                    'product_variant_id' => $_variant->id,
                                    'option_value_id' => $value[$attribute],
                                ]);
                            }

                        }

                    }
                }

            }

            DB::commit();

            return Res('Product added successfully', 200, $product->toArray());

        } catch (Exception $e) {
            DB::rollBack();
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public static function viewAll()
    {
        try {

            $data = Product::with([
                'ProductOption' => function ($query) {
                    $query->with('ProductOptionValue');
                }, 
                'productVariant' => function ($query) {
                    $query->with('ProductVariantOptionValue');
                }])
                ->get()->toArray();

            return Res('Products', 200, $data);

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }
}
