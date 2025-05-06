<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all expenses from the database
        $expenses = Expense::all();

        return view('admin.expense.expense', [
            'expenses' => $expenses,
            'expenseCategories' => ExpenseCategory::where('status', 1)->get(),
            'expenseSubCategories' => ExpenseSubCategory::where('status', 1)->get(),
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
            'name' => 'required|string|max:255|unique:expenses,name',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'expense_sub_category_id' => 'required|exists:expense_sub_categories,id',
        ]);

        // Create a new expense record
        Expense::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Expense record created successfully!',
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
            'name' => 'required|string|max:255|unique:expenses,name,' . $id,
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'expense_sub_category_id' => 'required|exists:expense_sub_categories,id',
        ]);

        // Update the expense record
        $expense = Expense::findOrFail($id);
        $expense->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Expense record updated successfully!',
            'id' => $expense->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the expense record and delete it
        $expense = Expense::findOrFail($id);
        $expense->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Expense record deleted successfully!');
    }
}
