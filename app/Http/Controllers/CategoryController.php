<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json(['success' => true, 'data' => $categories], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch categories', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        try {
            $category = Category::create($request->only('name'));
            return response()->json(['success' => true, 'message' => 'Category created successfully', 'data' => $category], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create category', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return response()->json(['success' => true, 'data' => $category], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Category not found', 'error' => $e->getMessage()], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);

        try {
            $category = Category::findOrFail($id);
            $category->update($request->only('name'));
            return response()->json(['success' => true, 'message' => 'Category updated successfully', 'data' => $category], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update category', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete category', 'error' => $e->getMessage()], 500);
        }
    }
}
