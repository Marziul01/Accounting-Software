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
        // Check if the user has permission to access this page
        if (auth()->user()->access->asset == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
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
        // Check if the user has permission to create asset sub-subcategories
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create asset sub-subcategories.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (AssetSubSubCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);

        // Create a new asset sub-subcategory
        AssetSubSubCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Asset subcategory created successfully!',
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
        // Check if the user has permission to update asset sub-subcategories
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update asset sub-subcategories.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        // Update the asset sub-subcategory
        $assetSubSubCategory = AssetSubSubCategory::findOrFail($id);
        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the contacts table
        while (AssetSubSubCategory::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        $assetSubSubCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Asset subcategory updated successfully!',
            'id' => $id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the user has permission to delete asset sub-subcategories
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete asset sub-subcategories.');
        }
        // Find the asset sub-subcategory by ID
        $assetSubSubCategory = AssetSubSubCategory::findOrFail($id);

        // Delete the asset sub-subcategory
        $assetSubSubCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Asset subcategory deleted successfully!');
    }

    public function getByCategory($category_id)
    {
        $subCategories = AssetSubSubCategory::where('asset_category_id', $category_id)->where('status', 1)->get();

        return response()->json($subCategories);
    }
}
