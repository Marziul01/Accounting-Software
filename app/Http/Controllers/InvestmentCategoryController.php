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
        // Check if the user has permission to access this page
        if (auth()->user()->access->investment == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
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
        // Check if the user has permission to create investment categories
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create investment categories.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slug' => 'required|string|max:255',
        ]);

        $baseSlug = $request->name;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the investments table
        while (InvestmentCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
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
        // Check if the user has permission to update investment categories
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update investment categories.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slug' => 'required|string|max:255',
        ]);

        // Update the investment category
        $investmentCategory = InvestmentCategory::findOrFail($id);
        $baseSlug = $request->name;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the investments table
        while (InvestmentCategory::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
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
        // Check if the user has permission to delete investment categories
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete investment categories.');
        }
        // Find the investment category by ID and delete it
        $investmentCategory = InvestmentCategory::findOrFail($id);
        $investmentCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Investment category deleted successfully!');
    }
}
