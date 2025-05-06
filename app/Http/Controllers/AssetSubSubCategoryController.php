<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetSubSubCategory;
use App\Models\AssetSubCategory;
use App\Models\AssetCategory;

class AssetSubSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all asset sub-subcategories from the database
        $assetSubSubCategories = AssetSubSubCategory::all();

        return view('admin.asset.assetsubsubcategory', [
            'assetSubSubCategories' => $assetSubSubCategories,
            'assetSubCategories' => AssetSubCategory::where('status', 1)->get(),
            'assetCategories' => AssetCategory::where('status', 1)->get(),
        ]);
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
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_sub_sub_categories,name',
            'description' => 'nullable|string|max:1000',
            'asset_sub_category_id' => 'required|exists:asset_sub_categories,id',
            'asset_category_id' => 'required|exists:asset_categories,id',
        ]);

        // Create a new asset sub-subcategory
        AssetSubSubCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Asset sub-subcategory created successfully!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_sub_sub_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'asset_sub_category_id' => 'required|exists:asset_sub_categories,id',
            'asset_category_id' => 'required|exists:asset_categories,id',
        ]);

        // Update the asset sub-subcategory
        $assetSubSubCategory = AssetSubSubCategory::findOrFail($id);
        $assetSubSubCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Asset sub-subcategory updated successfully!',
            'id' => $id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the asset sub-subcategory by ID
        $assetSubSubCategory = AssetSubSubCategory::findOrFail($id);

        // Delete the asset sub-subcategory
        $assetSubSubCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Asset sub-subcategory deleted successfully!');
    }

    public function getByCategory($category_id)
    {
        $subCategories = AssetSubSubCategory::where('asset_sub_category_id', $category_id)->where('status', 1)->get();

        return response()->json($subCategories);
    }
}
