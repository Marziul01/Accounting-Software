<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomeCategory;

class IncomeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all income categories from the database
        $incomeCategories = IncomeCategory::all();

        return view('admin.income.incomecategory', [
            'incomeCategories' => $incomeCategories,
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
            'name' => 'required|string|max:255|unique:income_categories,name',
            'desc' => 'nullable|string|max:1000',
        ]);

        // Create a new income category
        IncomeCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Income category created successfully!',
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
            'name' => 'required|string|max:255|unique:income_categories,name,' . $id,
            'desc' => 'nullable|string|max:1000',
        ]);

        // Find the income category by ID and update it
        $incomeCategory = IncomeCategory::findOrFail($id);
        $incomeCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Income category updated successfully!',
            'id' => $incomeCategory->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the income category by ID and delete it
        $incomeCategory = IncomeCategory::findOrFail($id);
        $incomeCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Income category deleted successfully!');
    }
}
