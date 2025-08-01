<?php

namespace App\Http\Controllers;

use App\Models\Expense;
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


class InvestmentExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
                ['message' => 'You have no permission to create investment loss'],
                403
            );
        }

        // 🛡️ validate
        $validator = Validator::make($request->all(), [
            'investment_id'            => 'required|exists:investments,id',
            'amount'                   => 'required|numeric|min:0',
            'category_id'       => 'required|exists:expense_categories,id',
            'subcategory_id'   => 'required|exists:expense_sub_categories,id',
            'date'                     => 'required|date',
            'description'              => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request) {

                $investment = Investment::findOrFail($request->investment_id);
                $name = $investment->name;
                // generate slug from name (supports Bangla and Unicode)
                $slug = Str::slug($name, '-', null);

                // fallback if slug is empty (e.g., all Bangla chars removed)
                if (empty($slug)) {
                    $slug = md5($name . microtime());
                }

                // 👉 1. create the investment-expense record
                $Investmentexpense = InvestmentExpense::create([
                    'investment_id' => $request->investment_id,
                    'category_id'  => $request->category_id,
                    'subcategory_id' => $request->subcategory_id,
                    'amount'        => $request->amount,
                    'date'          => $request->date,
                    'description'   => $request->description,
                ]);

                // 👉 2. create the matching expense row
                $expense = Expense::create([
                    'expense_category_id'       => $request->category_id,
                    'expense_sub_category_id'   => $request->subcategory_id,
                    'name'        => $name,
                    'slug'        => $slug,
                    'description' => $request->description,
                    'amount'      => $request->amount,
                    'date'        => $request->date,
                ]);

                $Investmentexpense->expense_id = $expense->id;
                $Investmentexpense->save();

            });

            return response()->json(['message' => 'loss recorded successfully!']);
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
                ['message' => 'You have no permission to update investment loss'],
                403
            );
        }

        $validator = Validator::make($request->all(), [
            'amount'                   => 'required|numeric|min:0',
            'category_id'       => 'required|exists:expense_categories,id',
            'subcategory_id'   => 'required|exists:expense_sub_categories,id',
            'date'                     => 'required|date',
            'description'              => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $investmentexpense = InvestmentExpense::findOrFail($id);

                // update investment_income
                $investmentexpense->update([
                    'amount'      => $request->amount,
                    'date'        => $request->date,
                    'description' => $request->description,
                ]);

                
                // update the related income record
                $expense = Expense::find($investmentexpense->expense_id);

                if ($expense) {
                    $expense->update([

                        'description' => $request->description,
                        'amount'      => $request->amount,
                        'date'        => $request->date,
                    ]);
                }
            });

            return response()->json(['message' => 'loss updated successfully!']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error updating loss.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth()->user()->access->investment != 2) {
            return response()->json(
                ['message' => 'You have no permission to delete investment loss'],
                403
            );
        }

        try {
            DB::transaction(function () use ($id) {
                $investmentexpense = InvestmentExpense::findOrFail($id);
                $expense = Expense::findOrFail($investmentexpense->expense_id);

                // Delete the expense record
                $expense->delete();

                // Delete the investment expense record
                $investmentexpense->delete();
            });

            return back()->with('success', 'Loss deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error deleting loss: ' . $e->getMessage());
        }
    }
}
