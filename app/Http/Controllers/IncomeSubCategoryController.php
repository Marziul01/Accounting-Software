<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomeSubCategory;
use App\Models\IncomeCategory;

class IncomeSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all income subcategories from the database
        $incomeSubCategories = IncomeSubCategory::all();

        return view('admin.income.incomesubcategory', [
            'incomeSubCategories' => $incomeSubCategories,
            'incomeCategories' => IncomeCategory::where('status', 1)->get(),
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
            'name' => 'required|string|max:255|unique:income_sub_categories,name',
            'description' => 'nullable|string|max:1000',
            'income_category_id' => 'required|exists:income_categories,id',
        ]);

        // Create a new income subcategory
        IncomeSubCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Income subcategory created successfully!',
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
            'name' => 'required|string|max:255|unique:income_sub_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'income_category_id' => 'required|exists:income_categories,id',
        ]);

        // Find the income subcategory and update it
        $incomeSubCategory = IncomeSubCategory::findOrFail($id);
        $incomeSubCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Income subcategory updated successfully!',
            'id' => $incomeSubCategory->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the income subcategory and delete it
        $incomeSubCategory = IncomeSubCategory::findOrFail($id);
        $incomeSubCategory->delete();

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Income subcategory deleted successfully!',
        ]);
    }

    public function getByCategory($category_id)
    {
        $subCategories = IncomeSubCategory::where('income_category_id', $category_id)->where('status', 1)->get();

        return response()->json($subCategories);
    }
}
