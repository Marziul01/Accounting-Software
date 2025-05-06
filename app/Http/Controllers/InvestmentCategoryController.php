<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvestmentCategory;

class InvestmentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all investment categories from the database
        $investmentCategories = InvestmentCategory::all();

        return view('admin.investment.investmentcategory', [
            'investmentCategories' => $investmentCategories,
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
            'name' => 'required|string|max:255|unique:investment_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create a new investment category
        InvestmentCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Investment category created successfully!',
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
            'name' => 'required|string|max:255|unique:investment_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        // Update the investment category
        $investmentCategory = InvestmentCategory::findOrFail($id);
        $investmentCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Investment category updated successfully!',
            'id' => $id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the investment category by ID and delete it
        $investmentCategory = InvestmentCategory::findOrFail($id);
        $investmentCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Investment category deleted successfully!');
    }
}
