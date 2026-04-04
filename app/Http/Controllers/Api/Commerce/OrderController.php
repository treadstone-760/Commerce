<?php

namespace App\Http\Controllers\Api\Commerce;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function addToCart(Request $request, $id)
    {
        try {
            $userId = Auth::guard('sanctum')->user() ?? null;
            $cartId = $request->header('X-Cart-Id');

            if (! $userId && ! $cartId) {
                return Res(
                    'Guest Header X-Cart-Id required for guest users', 400);
            }

            // 1. Validate input
            $validate = Validator::make($request->all(), [
                'product_variant_id' => 'nullable|exists:product_variants,id',
                'quantity' => 'nullable|integer|min:1',
            ]);

            if ($validate->fails()) {
                return Res('Validation error', 400, $validate->errors()->toArray());
            }

            // 2. Get product
            $variant = Product::with([
                'productVariant' => function ($query) use ($request) {
                    $query->where('id', $request->product_variant_id);
                },
            ])->find($id);

            // 3. Get variant (direct query - better)
            // $variant = ProductVariant::where('id', $request->product_variant_id)
            //     ->where('product_id', $id)
            //     ->first();

            // if There is no variant use the product id instead
            if (! $variant) {
                return response()->json([
                    'message' => 'Sorry product not found',
                ], 400);
            }

            // Check if product already exist in cart (both user and guest)
            // $checkCart = Cart::where('product_id', $id)
            //     ->where('product_variant_id', $variant->productVariant->first()->id)
            //     ->where(function ($query) use ($userId, $request) {
            //         $query->where('user_id', optional($userId)->id)
            //             ->orWhere('cart_id', $request->header('X-Cart-Id'));
            //     })
            //     ->first();

            // if ($checkCart) {
            //     return Res(
            //         'Product already exist in cart',
            //         400
            //     );
            // }

            // 5. Check if item already exists update quantity when its added to cart again
            $cartQuery = Cart::where('product_id', $id)
                ->where('product_variant_id', $request->product_variant_id);

            if ($userId) {
                $cartQuery->where('user_id', $userId->id);
            } else {
                $cartQuery->where('cart_id', $cartId);
            }
            $existingCart = $cartQuery->first();
            if ($existingCart) {
                // ✅ Update quantity instead of creating new row
                $existingCart->quantity += 1;
                $existingCart->save();

                return Res(
                    'Cart updated',
                    200,
                    $existingCart->toArray()
                );
            }

            // 6. Create new cart item
            $cart = new Cart;

            if ($userId) {
                $cart->user_id = $userId->id;
            } else {
                $cart->cart_id = $cartId;
            }

            $cart->product_id = $id;
            $cart->product_variant_id = $variant->productVariant->first()->id;
            $cart->quantity = $request->quantity ?? 1;
            $cart->price = $variant->productVariant->first()->price ?? $variant->base_price;

            $cart->save();

            return Res(
                'Cart created',
                200,
                $cart->toArray()
            );

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Sorry something went wrong', 500);
            // return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function viewCart(Request $request)
    {
        try {
            $userId = Auth::guard('sanctum')->user() ?? null;
            $cartId = $request->header('X-Cart-Id') ?? null;

            // Guest authentication required
            if (! $userId && ! $cartId) {
                return Res(
                    'Guest Header X-Cart-Id required for guest users', 400);
            }

            $query = Cart::with(['product', 'productVariant']);

            if ($userId) {
                $query->where('user_id', $userId->id);
            } else {
                $query->where('cart_id', $cartId);
            }

            $cart = $query->get();


            return Res(
                'Cart retrieved',
                200,
                $cart->toArray()
            );

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Something went wrong', 500);
        }
    }

    public function checkout(Request $request)
    {
        // TODO: Implement checkout logic
        // user has to be logged in
        // Validate user (must be logged in)
        // Fetch cart items
        // Validate stock
        // Calculate totals
        // Create order
        // Move cart → order_items
        // (Optional) process payment
        // Clear cart

        try {

            $get_cart_id = $request->header('X-Cart-Id');

            if ($get_cart_id) {

                $carts = Cart::where('cart_id', $get_cart_id)->get();
                foreach ($carts as $cart) {
                    $cart->user_id = auth()->id();
                    $cart->cart_id = null; // VERY IMPORTANT
                    $cart->save();
                }
            }
            $carts = Cart::where('user_id', auth()->id())->get();
            $total_price = 0;
            if ($carts->isEmpty()) {
                return Res('Cart is empty', 400);
            }
            foreach ($carts as $cart) {
                $product = Product::findOrFail($cart->product_id);
                $variant = ProductVariant::findOrFail($cart->product_variant_id);
                // validate stock
                if ($variant->stock < $cart->quantity) {
                    return Res('{$variant->name} Out of stock', 400);
                }
                $total_price += $variant->price * $cart->quantity;
            }
            DB::beginTransaction();
            $order = new Order;
            $order->user_id = auth()->id();
            $order->total_amount = $total_price;
            $order->sub_total = $total_price;
            $order->status = 'pending';
            $order->invoice_numnber = invoiceNumber(10);
            $order->save();
            foreach ($carts as $cart) {
                $order_item = new OrderItem;
                $order_item->order_id = $order->id;
                $order_item->product_id = $cart->product_id;
                $order_item->product_variant_id = $cart->product_variant_id;
                $order_item->quantity = $cart->quantity;
                $order_item->total_price = $cart->price * $cart->quantity;
                $order_item->unit_price = $cart->price;
                $order_item->save();
                $cart->delete();
            }
            DB::commit();

            // Redirect to payment gateway
            $payment = new PaymentService;

            $response = $payment->makePayment([
                'email' => auth()->user()->email,
                'amount' => $order->total_amount * 100,
                'reference' => $order->invoice_numnber,
            ]);

            return $response;

            return Res('Order created successfully', 200, $order->toArray());

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
}
