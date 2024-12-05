<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAllCategories(){
    $categories = categories::all();

    return response()->json([
        'message' => 'Categories retrieved successfully',
        'categories' => $categories,
    ]);
    }

    public function getCategory($id){
    $category = categories::find($id);

    // Check if the category exists
    if (!$category) {
        return response()->json([
            'message' => 'Category not found',
        ], 404);
    }

    return response()->json([
        'message' => 'Category retrieved successfully',
        'category' => $category,
    ]);
    }

    public function createCategory(CreateCategoryRequest $request){
        $category = Categories::create($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    public function updateCategory(UpdateCategoryRequest $request, $id){
        $category = Categories::findOrFail($id);
        $category->update($request->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ], 200);

    }

    public function deleteCategory($id){
        $category = Categories::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }

}
