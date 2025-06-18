<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\InvestmentCategory;
use App\Models\InvestmentExpense;
use App\Models\InvestmentIncome;
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
        $investment = Investment::create($request->except('amount'));

        $firsttransaction = new InvestmentTransaction();
        $firsttransaction->investment_id = $investment->id;
        $firsttransaction->amount = $request->amount;
        $firsttransaction->transaction_type = 'Deposit';
        $firsttransaction->transaction_date = $request->date;
        $firsttransaction->save();
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

        // Check if there are any transactions associated with this investment
        $transactions = InvestmentTransaction::where('investment_id', $investment->id)->get();
        foreach ($transactions as $transaction) {
            // Delete each transaction
            $transaction->delete();
        }
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

        $firstest = Investment::min('date');
        $latest = Investment::max('date');
        $firstesttransactions = InvestmentTransaction::min('transaction_date');
        $latesttransactions = InvestmentTransaction::max('transaction_date');
        $minDates = array_filter([$firstest, $firstesttransactions]);
        $maxDates = array_filter([$latest, $latesttransactions]);

        $minDate = !empty($minDates) ? min($minDates) : null;
        $maxDate = !empty($maxDates) ? max($maxDates) : null;

        $allinvestments = InvestmentTransaction::all();
        // Fetch all investment categories from the database
         $categories = InvestmentCategory::where('status',1)->get();
        $subcategories = InvestmentSubCategory::where('status',1)->get();

        // Default filter values
        $selectedCategoryId = $request->input('category_id', $categories->first()->id ?? null);
        $selectedSubcategoryId = $request->input('subcategory_id', '');
        $startDate = $minDate ? Carbon::parse($minDate)->toDateString() : Carbon::now()->toDateString();
        $endDate = $maxDate ? Carbon::parse($maxDate)->toDateString() : Carbon::now()->toDateString();

        if ($request->has('start_date')) {
            $startDate = $request->input('start_date');
        }
        if ($request->has('end_date')) {
            $endDate = $request->input('end_date');
        }

        $query = Investment::query();

        if ($selectedCategoryId) {
            $query->where('investment_category_id', $selectedCategoryId);
        }

        if ($selectedSubcategoryId) {
            $query->where('investment_sub_category_id', $selectedSubcategoryId);
        }

        // Eager load only transactions within the date range
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $filteredInvestments = $query->orderBy('date', 'desc')->get();

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
        // $query = Investment::with(['investmentCategory', 'investmentSubCategory']);

        // if ($request->category_id) {
        //     $query->where('investment_category_id', $request->category_id);
        // }

        // if ($request->subcategory_id) {
        //     $query->where('investment_sub_category_id', $request->subcategory_id);
        // }

        // if ($request->start_date) {
        //     $query->whereDate('date', '>=', $request->start_date);
        // }

        // if ($request->end_date) {
        //     $query->whereDate('date', '<=', $request->end_date);
        // }

        // $investments = $query->orderBy('date', 'desc')->get();


        $query = Investment::with(['investmentCategory', 'investmentSubCategory']);
        $startDate = $request->start_date ;
        $endDate = $request->end_date ;

        if ($request->category_id) {
            $query->where('investment_category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $query->where('investment_sub_category_id', $request->subcategory_id);
        }

        // Eager load only transactions within the date range
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $investments = $query->orderBy('date', 'desc')->get();

        // Only return JSON for AJAX
        if ($request->ajax()) {
            return response()->json(
                $investments->map(function ($investment) use ($startDate, $endDate) {
                    $initialAmount = $investment->transactions->first()->amount ?? 0;

                            // 2. Filtered transactions (between start and end)
                            $depositInRange = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                            $withdrawInRange = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                            $currentAmount = $depositInRange - $withdrawInRange;

                            

                            if ($investment->transactions->isNotEmpty() && $investment->transactions->first()->transaction_date >= $startDate) {
                                // If the first transaction is on or after the start date, previous amount is just the initial amount
                                $previousAmount = $initialAmount;
                            } else {
                                // Start date is on or after investment date
                                $depositBeforeStart = $investment->allTransactions
                                    ->where('transaction_type', 'Deposit')
                                    ->where('transaction_date', '<', $startDate)
                                    ->sum('amount');

                                $withdrawBeforeStart = $investment->allTransactions
                                    ->where('transaction_type', 'Withdraw')
                                    ->where('transaction_date', '<', $startDate)
                                    ->sum('amount');

                                $previousAmount = $depositBeforeStart - $withdrawBeforeStart;
                            }



                    return [
                        'id' => $investment->id,
                        'name' => $investment->name,
                        'description' => $investment->description,
                        'amount' => number_format($currentAmount,2) ?? 'No Transactions',
                        'date' => $investment->date,
                        'formatted_date' => \Carbon\Carbon::parse($investment->date)->format('d M, Y'),
                        'category_name' => $investment->investmentCategory->name ?? 'N/A',
                        'subcategory_name' => $investment->investmentSubCategory->name ?? 'N/A',
                        'slug' => $investment->slug ?? '',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
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
        $query = Investment::with(['investmentCategory', 'investmentSubCategory']);
        $startDate = $request->start_date ;
        $endDate = $request->end_date ;

        if ($request->category_id) {
            $query->where('investment_category_id', $category->id);
        }

        // Eager load only transactions within the date range
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $investments = $query->orderBy('date', 'desc')->get();

        $Categoryinvestments = Investment::where('investment_category_id', $category->id)
            ->get();

        $totalIncome = 0;
        $totalExpense = 0;

        if($Categoryinvestments->isNotEmpty()){
            foreach($Categoryinvestments as $investment) {
                $income = InvestmentIncome::where('investment_id', $investment->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->sum('amount');

                $expense = InvestmentExpense::where('investment_id', $investment->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->sum('amount');

                $totalIncome += $income;
                $totalExpense += $expense;
            }
        }

        return view('admin.investment.category-report', compact('category', 'investments', 'startDate', 'endDate', 'totalIncome', 'totalExpense'));
    }


    public function subcategoryReport($slug, Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $subcategory = InvestmentSubCategory::where('slug', $slug)->firstOrFail();
        $query = Investment::with(['investmentCategory', 'investmentSubCategory']);
        if ($request->category_id) {
            $query->where('investment_sub_category_id', $subcategory->id);
        }
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $investments = $query->orderBy('date', 'desc')->get();

        $SubCategoryinvestments = Investment::where('investment_sub_category_id', $subcategory->id)
            ->get();

        $totalIncome = 0;
        $totalExpense = 0;

        if($SubCategoryinvestments->isNotEmpty()){
            foreach($SubCategoryinvestments as $investment) {
                $income = InvestmentIncome::where('investment_id', $investment->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->sum('amount');

                $expense = InvestmentExpense::where('investment_id', $investment->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->sum('amount');

                $totalIncome += $income;
                $totalExpense += $expense;
            }
        }
        

        return view('admin.investment.subcategory-report', [
            'subcategory' => $subcategory,
            'investments' => $investments,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
        ]);
    }
    public function singleInvestmentReport(Request $request, $slug)
    {
        $investment = Investment::with(['investmentSubCategory.investmentCategory'])->where('slug', $slug)->firstOrFail();

        // Fetch only the transactions within the date range using the database query
        $transactions = $investment->transactions()
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->orderBy('transaction_date')
            ->get();

        $investmentIncomes = InvestmentIncome::where('investment_id', $investment->id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get();

        $totalinvestmentIncomes = $investmentIncomes->sum('amount');

        $investmentExpeses = InvestmentExpense::where('investment_id', $investment->id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get();

        $totalinvestmentExpeses = $investmentExpeses->sum('amount');

        $merged = $investmentIncomes->concat($investmentExpeses)->sortBy('date')->values();

        $totalInvested = $transactions->sum('investment_amount');
        $totalReturned = $transactions->sum('return_amount');
        $profitOrLoss = $totalReturned - $totalInvested;
        $startDate = $request->start_date;
        $endDate = $request->end_date ;

        return view('admin.investment.single_report', compact(
            'investment',
            'transactions',
            'totalInvested',
            'totalReturned',
            'profitOrLoss',
            'startDate',
            'endDate',
            'merged',
            'totalinvestmentIncomes',
            'totalinvestmentExpeses',
            'investmentIncomes',
            'investmentExpeses',
        ));
    }


    public function fullReport(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Fetch all categories
        $query = Investment::with(['investmentCategory', 'investmentSubCategory']);
        
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $investments = $query->orderBy('date', 'desc')->get();

        // Fetch all categories
        $categories = InvestmentCategory::where('status',1)->get();

        

        $totalIncome = 0;
        $totalExpense = 0;

        
                $income = InvestmentIncome::whereBetween('date', [$request->start_date, $request->end_date])
                    ->sum('amount');

                $expense = InvestmentExpense::whereBetween('date', [$request->start_date, $request->end_date])
                    ->sum('amount');

                $totalIncome += $income;
                $totalExpense += $expense;
           

        return view('admin.investment.full-report', compact('categories', 'startDate', 'endDate', 'investments', 'totalIncome', 'totalExpense'));
    }


}
