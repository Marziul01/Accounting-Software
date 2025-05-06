<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\InvestmentCategory;
use App\Models\InvestmentSubCategory;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all investment categories from the database
        $investmentCategories = InvestmentCategory::where('status', 1)->get();

        return view('admin.investment.investment', [
            'investmentCategories' => $investmentCategories,
            'investmentSubCategories' => InvestmentSubCategory::where('status', 1)->get(),
            'investments' => Investment::all(),
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
        $request->validate([
            'name' => 'required|string|max:255|unique:investments,name',
            'description' => 'nullable|string|max:1000',
            'investment_category_id' => 'required|exists:investment_categories,id',
            'investment_sub_category_id' => 'required|exists:investment_sub_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'investment_type' => 'required',
        ]);
        // Create a new investment
        Investment::create($request->all());
        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Investment created successfully!',
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
            'name' => 'required|string|max:255|unique:investments,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'investment_category_id' => 'required|exists:investment_categories,id',
            'investment_sub_category_id' => 'required|exists:investment_sub_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'investment_type' => 'required',
        ]);

        // Update the investment
        $investment = Investment::findOrFail($id);
        $investment->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Investment updated successfully!',
            'id' => $investment->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the investment and delete it
        $investment = Investment::findOrFail($id);
        $investment->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Investment deleted successfully!');
    }
}
