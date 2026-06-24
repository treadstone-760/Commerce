<?php

namespace App\Http\Controllers\Api\Commerce;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
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
            // return auth('sanctum')->user();
            $product = Product::with([
                'images',
                'ProductOption' => function ($query) {
                    $query->with('ProductOptionValue');
                } , 
                'productVariant' => function ($query) {
                    $query->with('ProductVariantOptionValue');
                },'productVariant.image'
            ])->where('id', $id)->first();

            if (! $product) {
                return Res('Product not found', 404);
            }

            // create view Count
            $userId = auth('sanctum')->id();
            $deviceId = request()->header('X-Device-Id') ?? request()->device_id;

            $query = $product->views();

            $query->create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'device_id' => $userId ? null : $deviceId,
            ]);

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
                'categories' => $categories,
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

    public function viewSingleCategoryWithProducts($id)
    {
        try {
            $category = Category::with(['products'])
                ->withCount(['children', 'products'])
                ->where('id', $id)
                // ->where('is_active', 1)
                ->first();

            return Res('Category', 200, [
                'category' => $category,
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

    public function getFeaturedProduct()
    {
        try {

            // get featured Products
            $paginate = request('paginate', 10);
            $products = Product::with([
                'images',
            ])->where('status', 1)
                ->where('featured', 1)
                ->paginate($paginate);

            return Res('Featured Products', 200, [
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

    public function mostViewedProducts()
    {
        try {
            // get featured Products
            $paginate = request('paginate', 10);

            // for the past 7days
            $viewedProducts = Product::whereHas('views', function ($query) {
                $query->where('created_at', '>=', now()->subDays(7));
            })
                ->withCount([
                            'views as views_count' => function ($query) {
                                $query->where('created_at', '>=', now()->subDays(7));
                            },
                        ])
                ->withCount([
                            'views as unique_viewers_count' => function ($query) {
                                $query->where('created_at', '>=', now()->subDays(7))
                                    ->select(DB::raw('count(distinct user_id)'));
                            },
                ])
                ->orderByDesc('unique_viewers_count')
                ->take(20)
                ->get();

            return Res('Most Viewed Products', 200, [
                'viewedProducts' => $viewedProducts,
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
}
