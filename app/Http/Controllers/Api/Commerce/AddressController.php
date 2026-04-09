<?php

namespace App\Http\Controllers\Api\Commerce;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function addAddress(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'address_line_1' => Rule::unique('shipping_addresses')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
                'address_line_2' => ['nullable', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],

                'landmark' => ['nullable', 'string', 'max:255'],
                'gps_address' => Rule::unique('shipping_addresses')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),

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
            DB::BeginTransaction();
            $create = new ShippingAddress;
            $create->address_line_1 = $request->address_line_1;
            $create->address_line_2 = $request->address_line_2;
            $create->city = $request->city;
            $create->user_id = auth()->user()->id;
            $create->landmark = $request->landmark;
            $create->gps_address = $request->gps_address;

            $create->delivery_instructions = $request->delivery_instructions;

            $create->address_type = $request->address_type;
            $create->is_default = $request->is_default;

            if ($create->is_default == true) {
                ShippingAddress::where('user_id', auth()->user()->id)
                    ->update(['is_default' => false]);
            }

            $create->save();
            DB::commit();

            return Res('Address added successfully', 200, $create->toArray());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Something went wrong', 500);
        }
    }

    public function myAddress()
    {
        try {
            $address = ShippingAddress::where('user_id', auth()->user()->id)->get();

            return Res('Successfull', 200, $address->toArray());
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Something went wrong', 500);
        }
    }

    public function viewSingle($id)
    {
        try {
            $address = ShippingAddress::where('id', $id)->where('user_id', auth()->user()->id)->first();

            return Res('Successfull', 200, $address->toArray());
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Something went wrong', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'address_line_1' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('shipping_addresses')
                        ->where(fn ($query) => $query->where('user_id', auth()->id()))
                        ->ignore($id)],
                'address_line_2' => 'nullable|string',
                'city' => 'required|string',
                'landmark' => 'nullable|string',
                'gps_address' => ['nullable', 'string', 'max:50',
                    Rule::unique('shipping_addresses')
                        ->where(fn ($query) => $query->where('user_id', auth()->id()))
                        ->ignore($id),
                    'delivery_instructions' => 'nullable|string',
                    'address_type' => 'required|in:home,office',
                    'is_default' => 'nullable|boolean',
                ],
            ]);

            if ($validate->fails()) {
                return Res('Validation Error', 422, $validate->errors()->toArray());
            }
            $address = ShippingAddress::where('id', $request->id)->where('user_id', auth()->user()->id)->first();
            $address->address_line_1 = $request->address_line_1;
            $address->address_line_2 = $request->address_line_2;
            $address->city = $request->city;
            $address->landmark = $request->landmark;
            $address->gps_address = $request->gps_address;
            $address->delivery_instructions = $request->delivery_instructions;
            $address->address_type = $request->address_type;
            $address->is_default = $request->is_default;
            if ($address->is_default == true) {
                ShippingAddress::where('user_id', auth()->user()->id)
                    ->update(['is_default' => false]);
            }
            $address->save();

            return Res('Successfull', 200, $address->toArray());
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
