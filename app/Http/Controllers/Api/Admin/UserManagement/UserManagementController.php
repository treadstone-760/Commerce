<?php

namespace App\Http\Controllers\Api\Admin\UserManagement;


use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;

use League\Uri\Http;

class UserManagementController extends Controller
{
    public function store(Request $request){
        try{
            if(!auth()->user()->can('user.create')){
                return Res('Unauthorized', 401);
            }
            $validate = Validator::make($request->all() , [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|unique:users,phone',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
                "role" => 'required'

            ]);
            if($validate->fails()){
                return Res("Validation Error" , 422 , $validate->errors()->toArray());
            }
             $role = Role::where('name' , $request->role)->first();
            
            $insert = new User();
            $insert->name = $request->name;
            $insert->email = $request->email;
            $insert->phone = $request->phone;
            $insert->password = Hash::make($request->password);
            $insert->user_type = "admin";
            $insert->save();

            $insert->assignRole($role);
            return Res('User Created Successfully', 200 , $insert->toArray());

        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return Res('Something went wrong', 500);
        }
    }

    public function viewAllAdmins()
    {
        try {
            $per_page = request()->per_page ?? 10;
            if (!auth()->user()->can('user.view')) {
                return Res('Unauthorized', 401);
            }
            $admins = User::where('user_type', 'admin')->paginate($per_page);
            return response()->json([
                'status' => 200,
                'data' => UserResource::collection($admins),
                'pagination' =>  paginationDetails($admins)
            ], 200);
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
