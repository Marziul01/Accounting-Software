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
        // Check if the user has permission to access this page
        if (auth()->user()->access->investment == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
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
        // Check if the user has permission to create investment subcategories
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create investment subcategories.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'investment_category_id' => 'required|exists:investment_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        $baseSlug = $request->name;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the investments table
        while (InvestmentSubCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
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
        // Check if the user has permission to update investment subcategories
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update investment subcategories.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'investment_category_id' => 'required|exists:investment_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        // Find the investment subcategory and update it
        $investmentSubCategory = InvestmentSubCategory::findOrFail($id);
        $baseSlug = $request->name;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the investments table
        while (InvestmentSubCategory::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
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
        // Check if the user has permission to delete investment subcategories
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete investment subcategories.');
        }
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
