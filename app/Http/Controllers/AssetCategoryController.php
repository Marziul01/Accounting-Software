<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetCategory;

class AssetCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all asset categories from the database
        $assetCategories = AssetCategory::all();

        return view('admin.asset.assetcategory', [
            'assetCategories' => $assetCategories,
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
            'name' => 'required|string|max:255|unique:asset_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create a new asset category
        AssetCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Asset category created successfully!',
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
            'name' => 'required|string|max:255|unique:asset_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        // Find the asset category by ID and update it
        $assetCategory = AssetCategory::findOrFail($id);
        $assetCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Asset category updated successfully!',
            'id' => $id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the asset category by ID and delete it
        $assetCategory = AssetCategory::findOrFail($id);
        $assetCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Asset category deleted successfully!');
    }
}
