<?php

namespace App\Http\Controllers\Api\Admin\Category;

use App\Http\Controllers\Controller;
use App\Services\Category\CategoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function add(Request $request)
    {
        if (! auth()->user()->can('category.add')) {
            return Res('Unauthorized', 401);
        }
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'parent_id' => 'nullable|exists:categories,id',
                // "is_active" => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return Res('validation error', 400, $validator->errors()->toArray());
            }

            return CategoryService::add($request);

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public function viewAll()
    {
        if (! auth()->user()->can('category.show')) {
            return Res('Unauthorized', 401);
        }
        try {
            return CategoryService::viewAll();
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }

    public function viewSingle($id)
    {
        if (! auth()->user()->can('category.show')) {
            return Res('Unauthorized', 401);
        }
        try {
            return CategoryService::viewSingle($id);
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public function update(Request $request , $id){
        if(!auth()->user()->can('category.update')) {
            return Res('Unauthorized', 401);
        }

        try{

            $validate = Validator::make($request->all() , [
                'name' => 'required|string',
                'description' => 'required|string',
                // 'parent_id' => 'nullable|exists:categories,id',
                // 'is_active' => 'required|boolean',
            ]);

            if($validate->fails()){
                return Res("Validation Error" , 422 , $validate->errors()->toArray());
            }
            return CategoryService::update($request , $id);

        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res("Server Error",500);
        }


    }

    public function delete($id){
        if(!auth()->user()->can('category.delete')) {
            return Res('Unauthorized', 401);
        }
        try{
            return CategoryService::delete($id);
        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res("Server Error",500);
        }
    }

    public function statusUpdate(Request $request , $id){
        if(!auth()->user()->can('category.status_update')) {
            return Res('Unauthorized', 401);
        }
        try{
            return CategoryService::statusUpdate($request , $id);
        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }
}
