<?php

namespace App\Services\Category;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function add($request)
    {
        try {

            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->is_active,
            ]);

            return Res('Category added successfully', 200, $category->toArray());

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public static function viewAll()
    {
        try {
            $data = Category::with('children')->whereNull('parent_id')->get()->toArray();

            return Res('Categories', 200, $data);
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public static function viewSingle($id)
    {
        try {

            $data = Category::with('children')->find($id);

            if (! $data) {
                return Res('Category not found', 404);
            }

            return Res('Category', 200, $data->toArray());

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public static function update($request, $id)
    {
        try {

            $category = Category::find($id);
            if (! $category) {
                return Res('Category not found', 404);
            }
            $data = [
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ];

            if ($request->has('parent_id')) {
                $data['parent_id'] = $request->parent_id;
            }

            if ($request->has('is_active')) {
                $data['is_active'] = $request->is_active;
            }

            $category->update($data);

            return Res('Category updated successfully', 200, $category->toArray());

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public static function delete($id)
    {
        try {

            $category = Category::find($id);
            if (! $category) {
                return Res('Category not found', 404);
            }
            $category->delete();

            return Res('Category deleted successfully', 200);
        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Res('Server Error', 500);
        }
    }

    public static function statusUpdate($request, $id)
    {
        try {

            $category = Category::find($id);
            if (! $category) {
                return Res('Category not found', 404);
            }
            $data = [
                'is_active' => ! $category->is_active,
            ];
            $category->update($data);

            return Res('Category status updated successfully', 200, $category->toArray());

        } catch (Exception $e) {
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }

    }
}
