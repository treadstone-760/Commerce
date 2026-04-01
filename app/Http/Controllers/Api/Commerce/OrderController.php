<?php

namespace App\Http\Controllers\Api\Commerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderController extends Controller
{
    public function addToCart(Request $request , $id)
    {
        try{

            // 1. Validate input
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        // 2. Get product
        $product = Product::findOrFail($id);

        // 3. Get variant (direct query - better)
        $variant = ProductVariant::where('id', $request->product_variant_id)
            ->where('product_id', $id)
            ->first();

        if (!$variant) {
            return response()->json([
                'message' => 'Invalid variant for this product'
            ], 400);
        }

        // 4. Determine owner (user or guest)
        $userId = auth()?->id();
        $cartId = $request->header('X-Cart-Id');

        if (!$userId && !$cartId) {
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
        $cart = new Cart();

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


      

        }catch(\Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return Res('Sorry something went wrong' , 500);
            // return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
