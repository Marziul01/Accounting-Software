<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all expense categories from the database
        $expenseCategories = ExpenseCategory::all();

        return view('admin.expense.expensecategory', [
            'expenseCategories' => $expenseCategories,
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
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create a new expense category
        ExpenseCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Expense category created successfully!',
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
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        // Update the expense category
        $expenseCategory = ExpenseCategory::findOrFail($id);
        $expenseCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Expense category updated successfully!',
            'id' => $expenseCategory->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the expense category by ID
        $expenseCategory = ExpenseCategory::findOrFail($id);

        // Delete the expense category
        $expenseCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Expense category deleted successfully!');
    }
}
