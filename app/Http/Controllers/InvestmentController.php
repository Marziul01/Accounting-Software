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
        // Check if the user has permission to access this page
        if (auth()->user()->access->investment == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
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
        // Check if the user has permission to create investments
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create investments.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'investment_category_id' => 'required|exists:investment_categories,id',
            'investment_sub_category_id' => 'required|exists:investment_sub_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'investment_type' => 'required',
            'slug' => 'required|string|max:255',
        ]);
        
        $baseSlug = $request->name;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the investments table
        while (Investment::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
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

        // Check if the user has permission to update investments
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update investments.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'investment_category_id' => 'required|exists:investment_categories,id',
            'investment_sub_category_id' => 'required|exists:investment_sub_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'investment_type' => 'required',
            'slug' => 'required|string|max:255',
        ]);

        // Update the investment
        $investment = Investment::findOrFail($id);
        $baseSlug = $request->name;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the investments table
        while (Investment::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
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
        // Check if the user has permission to delete investments
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete investments.');
        }
        // Find the investment and delete it
        $investment = Investment::findOrFail($id);
        $investment->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Investment deleted successfully!');
    }
}
