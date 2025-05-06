<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvestmentSubCategory;
use App\Models\InvestmentCategory;
use App\Models\Investment;

class InvestmentSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all investment subcategories from the database
        $investmentSubCategories = InvestmentSubCategory::all();

        return view('admin.investment.investmentsubcategory', [
            'investmentSubCategories' => $investmentSubCategories,
            'investmentCategories' => InvestmentCategory::where('status', 1)->get(),
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
            'name' => 'required|string|max:255|unique:investment_sub_categories,name',
            'description' => 'nullable|string|max:1000',
            'investment_category_id' => 'required|exists:investment_categories,id',
        ]);

        // Create a new investment subcategory
        InvestmentSubCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Investment subcategory created successfully!',
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
            'name' => 'required|string|max:255|unique:investment_sub_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'investment_category_id' => 'required|exists:investment_categories,id',
        ]);

        // Find the investment subcategory and update it
        $investmentSubCategory = InvestmentSubCategory::findOrFail($id);
        $investmentSubCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Investment subcategory updated successfully!',
            'id' => $investmentSubCategory->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the investment subcategory by ID and delete it
        $investmentSubCategory = InvestmentSubCategory::findOrFail($id);
        $investmentSubCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Investment subcategory deleted successfully!');
    }

    public function getByCategory($category_id)
    {
        $subCategories = InvestmentSubCategory::where('investment_category_id', $category_id)->where('status', 1)->get();

        return response()->json($subCategories);
    }
}
