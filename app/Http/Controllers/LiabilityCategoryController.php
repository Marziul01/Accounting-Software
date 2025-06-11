<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use App\Models\LiabilityCategory;

class LiabilityCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if the user has permission to access this page
        if (auth()->user()->access->liability == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all liability categories from the database
        $liabilityCategories = LiabilityCategory::all();

        return view('admin.liability.liabilitycategory', [
            'liabilityCategories' => $liabilityCategories,
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
        // Check if the user has permission to create liability categories
        if (auth()->user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slug' => 'required|string|max:255',
        ]);

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (LiabilityCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        // Create a new liability category
        LiabilityCategory::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability category created successfully!',
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
        // Check if the user has permission to update liability categories
        if (auth()->user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slug' => 'required|string|max:255',
        ]);

        // Update the specified liability category
        $liabilityCategory = LiabilityCategory::findOrFail($id);
        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the contacts table
        while (LiabilityCategory::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        $liabilityCategory->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability category updated successfully!',
            'id' => $liabilityCategory->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the user has permission to delete liability categories
        if (auth()->user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }

        if( $id == 3 || $id == 4 ) {
            return back()->with('error', 'You cannot delete the default Liability category.');

        }
        // Find the liability category by ID
        $liabilityCategory = LiabilityCategory::findOrFail($id);

        // Delete the liability category
        $liabilityCategory->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Liability category deleted successfully!');
    }
}
