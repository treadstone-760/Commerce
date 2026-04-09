<?php

namespace App\Http\Controllers\Api\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            if (! auth()->user()->hasRole('super_admin')) {
                return Res('Unauthorized', 401);
            }

            $total_sales = Order::where('status', 'paid')->sum('total_amount');
            $total_orders = Order::where('status', 'paid')->count();

            $products_with_low_stock = ProductVariant::with(['product',
            ])->where('stock', '<', 10)->get();
            $customer_count = User::where('user_type', 'customer')->count();

            // Get month with sales

            $monthlySales = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_sales')
            )
                ->where('status', 'paid') // only paid orders
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
            $latest_orders = Order::where('status', 'paid')->latest()->limit(20)->get();

            $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
                ->with('product') // eager load product info
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get();

            return Res('Successfull', 200, [
                'total_sales' => $total_sales,
                'total_orders' => $total_orders,
                'products_with_low_stock' => $products_with_low_stock,
                'customer_count' => $customer_count,
                'monthlySales' => $monthlySales,
                'latest_orders' => $latest_orders,
                'topProducts' => $topProducts
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
