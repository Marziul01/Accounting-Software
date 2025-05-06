<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiabilitySubCategory;
use App\Models\LiabilityCategory;

class LiabilitySubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all liability subcategories from the database
        $liabilitySubCategories = LiabilitySubCategory::all();

        return view('admin.liability.liabilitysubcategory', [
            'liabilitySubCategories' => $liabilitySubCategories,
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
            'name' => 'required|string|max:255|unique:liability_sub_categories,name',
            'description' => 'nullable|string|max:1000',
            'liability_category_id' => 'required|exists:liability_categories,id',
        ]);

        // Create a new liability subcategory
        LiabilitySubCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability subcategory created successfully!',
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
            'name' => 'required|string|max:255|unique:liability_sub_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'liability_category_id' => 'required|exists:liability_categories,id',
        ]);

        // Find the liability subcategory by ID and update it
        $liabilitySubCategory = LiabilitySubCategory::findOrFail($id);
        $liabilitySubCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability subcategory updated successfully!',
            'id' => $liabilitySubCategory->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the liability subcategory by ID and delete it
        $liabilitySubCategory = LiabilitySubCategory::findOrFail($id);
        $liabilitySubCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Liability subcategory deleted successfully!');
    }

    public function getByCategory($category_id)
    {
        $subCategories = LiabilitySubCategory::where('liability_category_id', $category_id)->where('status', 1)->get();

        return response()->json($subCategories);
    }
}
