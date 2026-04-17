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
            $admins = User::with('roles')->where('user_type', 'admin')->paginate($per_page);
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

    public function viewSingleAdmin($id){
        try{
            $admin = User::with('roles')->where('id' , $id)->where('user_type' , 'admin')->first();
            return Res('Successfull', 200, $admin->toArray());
        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return Res('Something went wrong', 500);
        }
    }

    public function updateAdmin($id)
    {
        try{
            if(!auth()->user()->can('user.edit')){
                return Res('Unauthorized', 401);
            }
            $validate = Validator::make(request()->all() , [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,'.request()->id,
                'phone' => 'required|unique:users,phone,'.request()->id,
                "role" => 'required'
            ]);
            if($validate->fails()){
                return Res("Validation Error" , 422 , $validate->errors()->toArray());
            }
            $role = Role::where('name' , request()->role)->first();
            $insert = User::find(request()->id);

            $insert->name = request()->name;
            $insert->email = request()->email;
            $insert->phone = request()->phone;
            $insert->user_type = "admin";
            $insert->save();
            $insert->syncRoles($role);

            return Res('User Updated Successfully', 200 , $insert->load('roles')->toArray());

        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return Res('Something went wrong', 500);
        }
    }

    public function  changeAdminStatus(Request $request , $id)
    {
        try{
            if(!auth()->user()->can('user.status.update')){
                return Res('Unauthorized', 401);
            }
            $validate = Validator::make(request()->all() , [
                'status' => 'required|in:active,inactive'
            ]);
            if($validate->fails()){
                return Res("Validation Error" , 422 , $validate->errors()->toArray());
            }
            $admin = User::where('id' , $id)->where('user_type' , 'admin')->first();
            if(!$admin){
                return Res('Admin not found', 404);
            }
            $admin->status = request()->status;
            $admin->save();
            return Res('Status changed successfully', 200);
        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return Res('Something went wrong', 500);
        }
    }

    public function createNewRole(Request $request){
        try{
            if(!auth()->user()->can('role.create')){
                return Res('Unauthorized', 401);
            }
            $validate = Validator::make($request->all() , [
                'name' => 'required|string|unique:roles,name'
            ]);
            if($validate->fails()){
                return Res("Validation Error" , 422 , $validate->errors()->toArray());
            }
            
            $role = Role::create(['name' => $request->name]);
            return Res('Role Created Successfully', 200 , $role->toArray());
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
