<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseSubCategory;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->access->expense == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all expense subcategories from the database
        $expenseSubCategories = ExpenseSubCategory::all();

        return view('admin.expense.expensesubcategory', [
            'expenseSubCategories' => $expenseSubCategories,
            'expenseCategories' => ExpenseCategory::where('status', 1)->where('id', '!=', 7)->get(),
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
        if (Auth::user()->access->expense != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create .');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (ExpenseSubCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        // Create a new expense subcategory
        ExpenseSubCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Expense subcategory created successfully!',
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
        if (Auth::user()->access->expense != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create .');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        // Find the expense subcategory by ID and update it
        $expenseSubCategory = ExpenseSubCategory::findOrFail($id);
        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the contacts table
        while (ExpenseSubCategory::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        $expenseSubCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Expense subcategory updated successfully!',
            'id' => $id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->access->expense != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete .');
        }

        if($id == 14 || $id == 15) {
            return back()->with('error', 'Default expense subcategory cannot be deleted.');

        }

        // Find the expense subcategory by ID
        $expenseSubCategory = ExpenseSubCategory::findOrFail($id);

        // Delete the expense subcategory
        $expenseSubCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Expense subcategory deleted successfully!');
    }

    public function getByCategory($category_id)
    {
        $subCategories = ExpenseSubCategory::where('expense_category_id', $category_id)->where('status', 1)->get();

        return response()->json($subCategories);
    }
}
