<?php

namespace App\Http\Controllers\Api\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        try {
            $total_revenue = Order::FileterRevenueByMonths($request->from, $request->to)->where('status', 'paid');

            return Res('Success', 200, [
                'total_revenue' => $total_revenue->with('orderItems')->sum('total_amount'),
                'paid_orders' =>  $total_revenue->get(),

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
