<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomeCategory;
use App\Models\IncomeSubCategory;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;

class IncomeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if(Auth::user()->access->income == 3 ){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
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
        if(Auth::user()->access->income != 2){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission .');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string|max:1000',
            'slug' => 'required|string|max:255',
        ]);

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (IncomeCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);

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
        if(Auth::user()->access->income != 2){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission .');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string|max:1000',
            'slug' => 'required|string|max:255',
        ]);

        // Find the income category by ID and update it
        $incomeCategory = IncomeCategory::findOrFail($id);

        // Check if the slug already exists in the database
        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;
        while (IncomeCategory::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
    
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
        if(Auth::user()->access->income != 2){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission .');
        }
        // Find the income category by ID and delete it
        $incomeCategory = IncomeCategory::findOrFail($id);
        $incomeCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Income category deleted successfully!');
    }
}
