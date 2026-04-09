<?php

namespace App\Http\Controllers\Api\Commerce;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function addAddress(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                
                'address_line_1' => ['required', 'string', 'max:255'],
                'address_line_2' => ['nullable', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],

                'landmark' => ['nullable', 'string', 'max:255'],
                'gps_address' => ['nullable', 'string', 'max:50'],

                'delivery_instructions' => ['nullable', 'string', 'max:500'],

                'address_type' => ['required', 'in:home,office'],
                'is_default' => ['nullable', 'boolean'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $create = new ShippingAddress();
            $create->address_line_1 = $request->address_line_1;
            $create->address_line_2 = $request->address_line_2;
            $create->city = $request->city;
            $create->user_id = auth()->user()->id;
            $create->landmark = $request->landmark;
            $create->gps_address = $request->gps_address;

            $create->delivery_instructions = $request->delivery_instructions;

            $create->address_type = $request->address_type;
            $create->is_default = $request->is_default;

            $create->save();

            return Res('Address added successfully', 200 , $create->toArray());
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Something went wrong', 500);
        }
    }

    public function myAddress(){
        try{
            $address = ShippingAddress::where('user_id', auth()->user()->id)->get();
            return Res('Successfull', 200, $address->toArray());
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
