<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiabilitySubSubCategory;
use App\Models\LiabilitySubCategory;
use App\Models\LiabilityCategory;

class LiabilitySubSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all liability sub-subcategories from the database
        $liabilitySubSubCategories = LiabilitySubSubCategory::all();

        return view('admin.liability.liabilitysubsubcategory', [
            'liabilitySubSubCategories' => $liabilitySubSubCategories,
            'liabilitySubCategories' => LiabilitySubCategory::where('status', 1)->get(),
            'liabilityCategories' => LiabilityCategory::where('status', 1)->get(),
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
            'name' => 'required|string|max:255|unique:liability_sub_sub_categories,name',
            'description' => 'nullable|string|max:1000',
            'liability_sub_category_id' => 'required|exists:liability_sub_categories,id',
            'liability_category_id' => 'required|exists:liability_categories,id',
        ]);

        // Create a new liability sub-subcategory
        LiabilitySubSubCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability sub-subcategory created successfully!',
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
            'name' => 'required|string|max:255|unique:liability_sub_sub_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'liability_sub_category_id' => 'required|exists:liability_sub_categories,id',
            'liability_category_id' => 'required|exists:liability_categories,id',
        ]);

        // Update the specified liability sub-subcategory
        $liabilitySubSubCategory = LiabilitySubSubCategory::findOrFail($id);
        $liabilitySubSubCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability sub-subcategory updated successfully!',
            'id' => $liabilitySubSubCategory->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the liability sub-subcategory by ID and delete it
        $liabilitySubSubCategory = LiabilitySubSubCategory::findOrFail($id);
        $liabilitySubSubCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Liability sub-subcategory deleted successfully!');
    }

    public function getByCategory($category_id)
    {
        $subCategories = LiabilitySubSubCategory::where('liability_sub_category_id', $category_id)->where('status', 1)->get();

        return response()->json($subCategories);
    }
}
