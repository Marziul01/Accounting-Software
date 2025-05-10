<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetSubCategory;
use App\Models\AssetCategory;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;

class AssetSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->access->asset == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all asset subcategories from the database
        $assetSubCategories = AssetSubCategory::all();

        return view('admin.asset.assetsubcategory', [
            'assetSubCategories' => $assetSubCategories,
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
        if (Auth::user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create .');
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
        while (AssetSubCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        // Create a new asset subcategory
        AssetSubCategory::create($request->all());

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
        if (Auth::user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create .');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        // Find the asset subcategory by ID and update it
        $assetSubCategory = AssetSubCategory::findOrFail($id);
        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the contacts table
        while (AssetSubCategory::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        $assetSubCategory->update($request->all());

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
        if (Auth::user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete .');
        }
        // Find the asset subcategory by ID and delete it
        $assetSubCategory = AssetSubCategory::findOrFail($id);
        $assetSubCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Asset subcategory deleted successfully!');
    }

    public function getByCategory($category_id)
    {
        $subCategories = AssetSubCategory::where('asset_category_id', $category_id)->where('status', 1)->get();

        return response()->json($subCategories);
    }
}
