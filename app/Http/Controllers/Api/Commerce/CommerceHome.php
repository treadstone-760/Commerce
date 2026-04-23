<?php

namespace App\Http\Controllers\Api\Commerce;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;

class CommerceHome extends Controller
{
    public function getProducts()
    {
        try {

            // Get all products
            $paginate = request('paginate', 10);
            $products = Product::with([
                'ProductOption' => function ($query) {
                    $query->with('ProductOptionValue');
                },
                'productVariant' => function ($query) {
                    $query->with('ProductVariantOptionValue');
                }])
                ->where('status', 1)
                ->paginate($paginate);

            return Res('Products', 200, [
                'products' => $products,
            ]);

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),

            ]);

            return Res('Something went wrong', 500);
        }
    }

    public function viewsingleProduct($id)
    {
        try {
            $product = Product::with([
                'ProductOption' => function ($query) {
                    $query->with('ProductOptionValue');
                },
                'productVariant' => function ($query) {
                    $query->with('ProductVariantOptionValue');
                },
            ])->where('id', $id)->first();

            if (! $product) {
                return Res('Product not found', 404);
            }

            return Res('Product', 200, [
                'product' => $product,
            ]);
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Something went wrong', 500);
        }
    }

    public function getCategories()
    {
        try {
            $paginate = request('paginate', 10);
        // Get all categories 💼
            $categories = Category::with(['children', 'products'])
                ->withCount(['children', 'product'])
                ->where('is_active', 1)
                ->paginate($paginate);

        return Res('Categories', 200, [
            'categories' => $categories
        ]);
            
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Something went wrong', 500);
        }
    }

    public function viewSingleCategoryWithProducts($id){
        try{
            $category = Category::with(['products'])
                ->withCount(['children', 'products'])
                ->where('id', $id)
                // ->where('is_active', 1)
                ->first();

            return Res('Category', 200, [
                'category' => $category
            ]);
        }catch(Exception $e){

            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Something went wrong', 500);
        }
    }
}
