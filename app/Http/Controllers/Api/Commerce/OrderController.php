<?php

namespace App\Http\Controllers\Api\Commerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function addToCart(Request $request)
    {
        try{

        }catch(\Exception $e){
            // return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
