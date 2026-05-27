<?php

namespace App\Services\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use App\Models\ProductVariantOptionValue;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Support\Str;

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
            $category = Category::with('parentRecursive')->find ($request->category_id);
            
            $product->slug = $category->getFullNameAttribute().'-'.Str::slug($request->name).'-'.Str::uuid()->toString().'-'.$request->base_price;
            $product->save();
            // Add product images
            if ($request->has('images')) {
                foreach ($request->images as $image) {

                    $product_images = new ProductImage;
                    $product_images->product_id = $product->id;
                    //
                    $img = convertFile($image);
                    // save to storage
                    Storage::disk('public')->put($img['file_name'], $img['file']);
                    $product_images->image = $img['file_name'];
                    $product_images->save();

                }
            }

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
                    // return $variant['images'];
                    $_variant = ProductVariant::create([
                        'sku' => $variant['sku'],
                        'product_id' => $product->id,
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                        // 'attributes' => $variant['attributes']
                    ]);
                    if(isset($variant['images'])) {
                        foreach ($variant['images'] as $image) {
                            
                            $product_images = new ProductVariantImage();
                            $product_images->product_variant_id = $_variant->id;
                            //
                            $img = convertFile($image);
                            // save to storage
                            Storage::disk('public')->put($img['file_name'], $img['file']);
                            $product_images->image = $img['file_name'];
                            $product_images->save();
                        }
                    }

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

    public static function updateProduct($request, $id){
        try{
            // start transaction
            DB::beginTransaction();

            $product = Product::find($id);
            $product->name = $request->name;
            $product->description = $request->description;
            $product->base_price = $request->base_price;
            $product->currency = $request->currency;
            $product->category_id = $request->category_id;
            // $product->status = $request->is_active;
            $product->featured = $request->featured;
            if($request->has('images')){
                foreach ($request->images as $image) {
                    //delete all product images
                    ProductImage::where('product_id', $product->id)->delete();
                    $product_images = new ProductImage;
                    $product_images->product_id = $product->id;
                    //
                    $img = convertFile($image);
                    // save to storage
                    Storage::disk('public')->put($img['file_name'], $img['file']);
                    $product_images->image = $img['file_name'];
                    $product_images->save();
                }
            }
            $product->save();

            //lets work on product options
            ProductOption::where('product_id', $product->id)->delete();
            if($request->has('options')) {
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
            //variants
            ProductVariant::where('product_id', $product->id)->delete();
            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    // return $variant['images'];
                    $_variant = ProductVariant::create([
                        'sku' => $variant['sku'],
                        'product_id' => $product->id,
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                        // 'attributes' => $variant['attributes']
                    ]);
                    if(isset($variant['images'])) {
                        foreach ($variant['images'] as $image) {
                            
                            $product_images = new ProductVariantImage();
                            $product_images->product_variant_id = $_variant->id;
                            //
                            $img = convertFile($image);
                            // save to storage
                            Storage::disk('public')->put($img['file_name'], $img['file']);
                            $product_images->image = $img['file_name'];
                            $product_images->save();
                        }
                    }

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

            // return Res('Product updated successfully', 200, $product->load(   'images', 'ProductOption.ProductOptionValue', 'productVariant.ProductVariantOptionValue', 'productVariant.image')->toArray());
            return Res(
    'Product updated successfully',
    200,
    $product->load([
        'images',
        'ProductOption' => function ($query) {
            $query->with('ProductOptionValue');
        },
        'productVariant' => function ($query) {
            $query->with([
                'ProductVariantOptionValue',
                'image',
            ]);
        },
    ])->toArray()
);
        }catch(Exception $e){
            DB::rollBack();
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return Res('Something went wrong', 500);
        }
    }

    public static function viewAll()
    {
        try {

            $data = Product::with([
                'images',
                'ProductOption' => function ($query) {
                    $query->with('ProductOptionValue');
                },
                'productVariant' => function ($query) {
                    $query->with(['ProductVariantOptionValue' , 'image'] );
                },
            ]
            )
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

    public static function viewByCategory($id)
    {
        try {
            $data = Category::with([
                'Product' => function ($query) {
                    $query->with([
                        'images',
                        'ProductOption' => function ($query) {
                            $query->with('ProductOptionValue');
                        },
                        'productVariant' => function ($query) {
                            $query->with(['ProductVariantOptionValue' , 'image'] );
                        },
                    ])->where('status', 1);
                }])->find($id)->toArray();

            return Res('Products', 200, [
                'category' => $data,
            ]);

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }

    }

    public static function viewSingle($id)
    {
        try {
            $data = Product::with([
                'images',
                'ProductOption' => function ($query) {
                    $query->with('ProductOptionValue');
                },
                'productVariant' => function ($query) {
                    $query->with(['ProductVariantOptionValue' , 'image'] );
                }])->find($id);

            if (! $data) {
                return Res('Product not found', 404);
            }

            return Res('Product', 200, [
                'product' => $data->toArray(),
            ]);
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public static function changeProductStatus($id)
    {
        try {
            $product = Product::find($id);
            if (! $product) {
                return Res('Product not found', 404);
            }
            $product->status = ! $product->status;
            $product->save();

            return Res('Product status changed successfully', 200);
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
