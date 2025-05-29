<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\IncomeSubCategory;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->access->income == 3 ){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all income records from the database
        $incomes = Income::all();
        $firstDate = $incomes->min('date');
        $lastDate = $incomes->max('date');

        return view('admin.income.income', [
            'incomes' => $incomes,
            'incomeCategories' => IncomeCategory::where('status', 1)->get(),
            'incomeSubCategories' => IncomeSubCategory::where('status', 1)->get(),
            'firstDate' => $firstDate,
            'lastDate' => $lastDate,
        ]);
    }

    public static function report()
    {
        if(Auth::user()->access->income == 3 ){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        $incomes = Income::all();
        $firstDate = $incomes->min('date');
        $lastDate = $incomes->max('date');
        return view('admin.income.report', [
            'incomes' => Income::all(),
            'incomeCategories' => IncomeCategory::where('status', 1)->get(),
            'incomeSubCategories' => IncomeSubCategory::where('status', 1)->get(),
            'firstDate' => $firstDate,
            'lastDate' => $lastDate,
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
            'income_category_id' => 'required|exists:income_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'income_sub_category_id' => 'required|exists:income_sub_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (Income::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);

        // Create a new income record
        Income::create($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Income record created successfully!',
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
            'income_category_id' => 'required|exists:income_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'income_sub_category_id' => 'required|exists:income_sub_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        $bankTransaction = Income::findOrFail($id);

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Only regenerate slug if it's already used by a different record
        while (
            Income::where('slug', $slug)->where('id', '!=', $bankTransaction->id)->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        // Merge the slug into the request data
        $request->merge(['slug' => $data['slug']]);

        // Find the income record and update it
        $income = Income::findOrFail($id);
        $income->update($request->all());

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Income record updated successfully!',
            'id' => $income->id,
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
        // Find the income record by ID
        $income = Income::findOrFail($id);

        // Delete the income record
        $income->delete();

        // Redirect back to the index with a success message
        return back()->with('success', 'Income record deleted successfully!');
    }

    public function IncomesubcategoryReport($slug, Request $request)
    {
        $subcategory = IncomeSubCategory::where('slug', $slug)->firstOrFail();

        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();

        $incomes = Income::where('income_sub_category_id', $subcategory->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return view('admin.income.subcategory-report', compact('subcategory', 'incomes', 'startDate', 'endDate'));
    }

    public function filter(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $incomeCategories = IncomeCategory::with('incomeSubCategories')->get();
        $incomes = Income::whereBetween('date', [$startDate, $endDate])->get();

        return response()->json([
            'html' => view('admin.income.partial-table', compact('incomeCategories', 'incomes', 'startDate', 'endDate'))->render()
        ]);
    }


    public function fullReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();

        $incomes = Income::whereBetween('date', [$startDate, $endDate])->get();
        $incomeCategories = IncomeCategory::where('status', 1)->get();

        return view('admin.income.full-report', compact('incomes', 'startDate', 'endDate' , 'incomeCategories'));
    }

    public function IncomecategoryReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();
        $category = IncomeCategory::where('slug', $request->slug)->firstOrFail();

        $incomes = Income::whereBetween('date', [$startDate, $endDate])->get();
        $incomeCategories = IncomeCategory::where('status', 1)->get();

        return view('admin.income.category-report', compact('category','incomes', 'startDate', 'endDate' , 'incomeCategories'));
    }



}
