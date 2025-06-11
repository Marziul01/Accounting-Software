<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\InvestmentIncome;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\IncomeSubCategory;
use App\Models\Investment;
use App\Models\InvestmentExpense;
use Illuminate\Support\Str;

class InvestmentIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        // 🔐 permission gate
        if (auth()->user()->access->investment == 3) {
            return response()->json(
                ['message' => 'You have no permission to view investment income'],
                403
            );
        }

        // 🗂️ fetch the investment by slug
        $investment = Investment::where('slug', $slug)->firstOrFail();
        // 📂 fetch all incomes related to this investment
        $incomes = InvestmentIncome::where('investment_id', $investment->id)->get();
        $expenses = InvestmentExpense::where('investment_id', $investment->id)->get();

        $merged = $incomes->concat($expenses)->sortBy('date')->values();

        return view('admin.investment.incomeexpenses', [
            'investment' => $investment,
            'records' => $merged,
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
        // 🔐 permission gate
        if (auth()->user()->access->investment != 2) {
            return response()->json(
                ['message' => 'You have no permission to create investment income'],
                403
            );
        }

        // 🛡️ validate
        $validator = Validator::make($request->all(), [
            'investment_id'            => 'required|exists:investments,id',
            'amount'                   => 'required|numeric|min:0',
            'category_id'       => 'required|exists:income_categories,id',
            'subcategory_id'   => 'required|exists:income_sub_categories,id',
            'date'                     => 'required|date',
            'description'              => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request) {

                // 🗂️ fetch the investment by ID
                $investment = Investment::findOrFail($request->investment_id);
                $name = $investment->name;
                // generate slug from name (supports Bangla and Unicode)
                $slug = Str::slug($name, '-', null);

                // fallback if slug is empty (e.g., all Bangla chars removed)
                if (empty($slug)) {
                    $slug = md5($name . microtime());
                }

                // 👉 1. create the investment-income record
                $InvestmentIncome = InvestmentIncome::create([
                    'investment_id' => $request->investment_id,
                    'category_id'  => $request->category_id,
                    'subcategory_id' => $request->subcategory_id,
                    'amount'        => $request->amount,
                    'date'          => $request->date,
                    'description'   => $request->description,
                ]);

                // 👉 2. create the matching income row
                $income = Income::create([
                    'income_category_id'       => $request->category_id,
                    'income_sub_category_id'   => $request->subcategory_id,
                    'name'        => $name,
                    'slug'        => $slug,
                    'description' => $request->description,
                    'amount'      => $request->amount,
                    'date'        => $request->date,
                ]);

                $InvestmentIncome->income_id = $income->id;
                $InvestmentIncome->save();

            });

            return response()->json(['message' => 'Income recorded successfully!']);
        } catch (\Throwable $e) {
            // any failure rolls everything back
            return response()->json(
                ['message' => 'Error saving records', 'error' => $e->getMessage()],
                500
            );
        }
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
    public function update(Request $request, $id)
    {
        if (auth()->user()->access->investment != 2) {
            return response()->json(
                ['message' => 'You have no permission to update investment income'],
                403
            );
        }

        $validator = Validator::make($request->all(), [
            'amount'                   => 'required|numeric|min:0',
            'category_id'       => 'required|exists:income_categories,id',
            'subcategory_id'   => 'required|exists:income_sub_categories,id',
            'date'                     => 'required|date',
            'description'              => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $investmentIncome = InvestmentIncome::findOrFail($id);

                // update investment_income
                $investmentIncome->update([
                    'amount'      => $request->amount,
                    'date'        => $request->date,
                    'description' => $request->description,
                ]);

                
                // update the related income record
                $income = Income::find($investmentIncome->income_id);

                if ($income) {
                    $income->update([
                        
                        'description' => $request->description,
                        'amount'      => $request->amount,
                        'date'        => $request->date,
                    ]);
                }
            });

            return response()->json(['message' => 'Income updated successfully!']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error updating income.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 🔐 permission gate
        if (auth()->user()->access->investment != 2) {
            return response()->json(
                ['message' => 'You have no permission to delete investment income'],
                403
            );
        }

        try {
            DB::transaction(function () use ($id) {
                $investmentIncome = InvestmentIncome::findOrFail($id);

                // delete the related income record
                if ($investmentIncome->income_id) {
                    Income::destroy($investmentIncome->income_id);
                }

                // delete the investment income record
                $investmentIncome->delete();
            });

            return back()->with('success', 'Investment income deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error deleting investment income: ' . $e->getMessage());
        }
    }
}
