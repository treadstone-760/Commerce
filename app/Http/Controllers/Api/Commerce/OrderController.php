<?php

namespace App\Http\Controllers\Api\Commerce;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function addToCart(Request $request, $id)
    {
        try {

            // 1. Validate input
            $request->validate([
                'product_variant_id' => 'required|exists:product_variants,id',
                'quantity' => 'nullable|integer|min:1',
            ]);

            // 2. Get product
            $product = Product::findOrFail($id);

            // 3. Get variant (direct query - better)
            $variant = ProductVariant::where('id', $request->product_variant_id)
                ->where('product_id', $id)
                ->first();

            if (! $variant) {
                return response()->json([
                    'message' => 'Invalid variant for this product',
                ], 400);
            }

            // 4. Determine owner (user or guest)
            $userId = auth()?->id();
            $cartId = $request->header('X-Cart-Id');

            if (! $userId && ! $cartId) {
                return Res(
                    'Cart ID required for guest users', 400);
            }

            // 5. Check if item already exists
            $cartQuery = Cart::where('product_id', $id)
                ->where('product_variant_id', $variant->id);

            if ($userId) {
                $cartQuery->where('user_id', $userId);
            } else {
                $cartQuery->where('cart_id', $cartId);
            }

            $existingCart = $cartQuery->first();

            if ($existingCart) {
                // ✅ Update quantity instead of creating new row
                $existingCart->quantity += $request->quantity ?? 1;
                $existingCart->save();

                return Res(
                    'Cart updated',
                    200,
                    $existingCart
                );
            }

            // 6. Create new cart item
            $cart = new Cart;

            if ($userId) {
                $cart->user_id = $userId;
            } else {
                $cart->cart_id = $cartId;
            }

            $cart->product_id = $id;
            $cart->product_variant_id = $variant->id;
            $cart->quantity = $request->quantity ?? 1;
            $cart->price = $variant->price;
            $cart->status = 'pending';

            $cart->save();

            return Res(
                'Cart created',
                200,
                $cart->toArray()
            );

        } catch (\Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Sorry something went wrong', 500);
            // return response()->json(['error' => $e->getMessage()], 500);
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
            return Res('Order created successfully', 200, $order->toArray());

        } catch (\Exception $e) {
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
