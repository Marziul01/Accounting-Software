<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiabilityCategory;

class LiabilityCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all liability categories from the database
        $liabilityCategories = LiabilityCategory::all();

        return view('admin.liability.liabilitycategory', [
            'liabilityCategories' => $liabilityCategories,
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
            'name' => 'required|string|max:255|unique:liability_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create a new liability category
        LiabilityCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability category created successfully!',
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
            'name' => 'required|string|max:255|unique:liability_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        // Update the specified liability category
        $liabilityCategory = LiabilityCategory::findOrFail($id);
        $liabilityCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability category updated successfully!',
            'id' => $liabilityCategory->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the liability category by ID
        $liabilityCategory = LiabilityCategory::findOrFail($id);

        // Delete the liability category
        $liabilityCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Liability category deleted successfully!');
    }
}
