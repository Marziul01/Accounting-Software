<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\InvestmentCategory;
use App\Models\InvestmentSubCategory;
use App\Models\InvestmentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

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
            'investmentTransactions' => InvestmentTransaction::all(),
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

        $finalAmount = $request->amount;

        $transactions = InvestmentTransaction::where('investment_id', $id)->get();

        foreach ($transactions as $transaction) {
            if ($transaction->transaction_type === 'Deposit') {
                $finalAmount += $transaction->amount;
            } elseif ($transaction->transaction_type === 'Withdraw') {
                $finalAmount -= $transaction->amount;
            }
        }

        $investment->amount = $finalAmount;
        $investment->save();

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

    public function report(Request $request )
    {
        // Check if the user has permission to access this page
        if (auth()->user()->access->investment == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all investment categories from the database
         $categories = InvestmentCategory::all();
        $subcategories = InvestmentSubCategory::all();

        // Default filter values
        $selectedCategoryId = $request->input('category_id', $categories->first()->id ?? null);
        $selectedSubcategoryId = $request->input('subcategory_id', '');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        // Fetch filtered investments
        $query = Investment::query();

        if ($selectedCategoryId) {
            $query->where('investment_category_id', $selectedCategoryId);
        }

        if ($selectedSubcategoryId) {
            $query->where('investment_sub_category_id', $selectedSubcategoryId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $filteredInvestments = $query->with(['investmentCategory', 'investmentSubCategory'])->latest()->get();

        return view('admin.investment.investment-report', [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'filteredInvestments' => $filteredInvestments,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedSubcategoryId' => $selectedSubcategoryId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }


    public function getSubcategories($categoryId)
    {
        $subcategories = InvestmentSubCategory::where('investment_category_id', $categoryId)->get();

        return response()->json($subcategories);
    }

    
    public function filterInvestments(Request $request)
    {
        $query = Investment::with(['investmentCategory', 'investmentSubCategory']);

        if ($request->category_id) {
            $query->where('investment_category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $query->where('investment_sub_category_id', $request->subcategory_id);
        }

        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $investments = $query->orderBy('date', 'desc')->get();

        // Only return JSON for AJAX
        if ($request->ajax()) {
            return response()->json(
                $investments->map(function ($investment) {
                    return [
                        'id' => $investment->id,
                        'name' => $investment->name,
                        'description' => $investment->description,
                        'amount' => $investment->amount,
                        'date' => $investment->date,
                        'formatted_date' => \Carbon\Carbon::parse($investment->date)->format('d M, Y'),
                        'category_name' => $investment->investmentCategory->name ?? 'N/A',
                        'subcategory_name' => $investment->investmentSubCategory->name ?? 'N/A',
                        'slug' => $investment->slug ?? '',
                    ];
                })
            );
        }

        // If not AJAX, redirect back or show a view
        return redirect()->back();
    }


    public function categoryReport(Request $request, $slug)
    {
        $category = InvestmentCategory::where('slug', $slug)->firstOrFail();

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = Investment::with('investmentSubCategory')
            ->where('investment_category_id', $category->id);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $investments = $query->get()->groupBy('investment_sub_category_id');
        $investmentTransactions = InvestmentTransaction::all();

        return view('admin.investment.category-report', compact('category', 'investments', 'startDate', 'endDate' , 'investmentTransactions'));
    }

    public function subcategoryReport($slug, Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $subcategory = InvestmentSubCategory::where('slug', $slug)->firstOrFail();

        $investments = Investment::with('transactions')
            ->where('investment_sub_category_id', $subcategory->id)
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->where('date', '<=', $endDate)
            ->get();

        $transactions = InvestmentTransaction::whereIn('investment_id', $investments->pluck('id'))->get();

        return view('admin.investment.subcategory-report', [
            'subcategory' => $subcategory,
            'investments' => $investments,
            'investmentTransactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
    public function singleInvestmentReport($slug)
    {
        $investment = Investment::with(['transactions', 'investmentSubCategory.investmentCategory'])->where('slug', $slug)->firstOrFail();

        $transactions = $investment->transactions;

        $totalInvested = $transactions->sum('investment_amount');
        $totalReturned = $transactions->sum('return_amount');
        $profitOrLoss = $totalReturned - $totalInvested;

        return view('admin.investment.single_report', compact(
            'investment',
            'transactions',
            'totalInvested',
            'totalReturned',
            'profitOrLoss'
        ));
    }

    public function fullReport(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Fetch all categories
        $categories = InvestmentCategory::with(['investmentSubCategories.investments' => function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        }])->get();

        // Get all investment transactions
        $investmentTransactions = InvestmentTransaction::all();

        return view('admin.investment.full-report', compact('categories', 'startDate', 'endDate', 'investmentTransactions'));
    }


}
