<?php

namespace App\Services\Category;
use Exception;
use Illuminate\Support\Str;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function add($request){
        try{

            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->is_active,
            ]);

            return Res('Category added successfully', 200, $category->toArray());

        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res("Server Error",500);
        }   
    }

    public static function viewAll(){
        try{
            $data = Category::with('children')->whereNull('parent_id')->get()->toArray();
            return Res('Categories', 200, $data);
        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res("Server Error",500);
        }
    }

    public static function viewSingle($id){
        try{

            $data = Category::with('children')->findOrFail($id);
            return Res('Category', 200, $data->toArray());

        }catch(Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res("Server Error",500);
        }
    }
}
