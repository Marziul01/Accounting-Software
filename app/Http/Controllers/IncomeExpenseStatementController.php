<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Investment;
use Carbon\Carbon;
use App\Models\InvestmentIncome;
use App\Models\InvestmentExpense;
use App\Models\CurrentAsset;


class IncomeExpenseStatementController extends Controller
{
    public static function incomeExpenseStatement(Request $request)
    {
       if ($request->input('startDate') && $request->input('endDate')){
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
        }

        //income categories

        $incomecategories = IncomeCategory::where('status', 1)->get();


        if (!$request->input('startDate') || !$request->input('endDate')) {
            $startDate = Income::min('date') ?? now()->startOfMonth();
            $endDate = Income::max('date') ?? now()->endOfMonth();
        }

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Now safe to use ->copy()
        $lastMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $startDate->copy()->subMonth()->endOfMonth();

        $lastYearStart = $startDate->copy()->subYear()->startOfYear();
        $lastYearEnd = $startDate->copy()->subYear()->endOfYear();

        // Total income excluding category 13
        $totalIncomesExcludingCat13 = Income::whereBetween('date', [$startDate, $endDate])
            ->whereHas('incomeSubCategory.incomeCategory', function ($query) {
                $query->where('id', '!=', 13);
            })
            ->sum('amount'); // assuming 'amount' is the income value column

        // Total income for category 13
        $totalIncomeCat13 = Income::whereBetween('date', [$startDate, $endDate])
            ->whereHas('incomeSubCategory.incomeCategory', function ($query) {
                $query->where('id', 13);
            })
            ->sum('amount');

        // Previous period income excluding category 13
        $previousTotalIncomeExcluding13 = Income::where('date', '<', $startDate)
            ->whereHas('incomeSubCategory.incomeCategory', function ($query) {
                $query->where('id', '!=', 13);
            })
            ->sum('amount');
        // Previous period income for category 13
        $previousTotalIncome13 = Income::where('date', '<', $startDate)
            ->whereHas('incomeSubCategory.incomeCategory', function ($query) {
                $query->where('id', 13);
            })
            ->sum('amount');
        // Last month income excluding category 13
        $lastMonthIncomeExcluding13 = Income::whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->whereHas('incomeSubCategory.incomeCategory', function ($query) {
                $query->where('id', '!=', 13);
            })
            ->sum('amount');
        // Last month income for category 13
        $lastMonthIncome13 = Income::whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->whereHas('incomeSubCategory.incomeCategory', function ($query) {
                $query->where('id', 13);
            })
            ->sum('amount');
        // Last year income excluding category 13
        $lastYearIncomeExcluding13 = Income::whereBetween('date', [$lastYearStart, $lastYearEnd])
            ->whereHas('incomeSubCategory.incomeCategory', function ($query) {
                $query->where('id', '!=', 13);
            })
            ->sum('amount');
        // Last year income for category 13
        $lastYearIncome13 = Income::whereBetween('date', [$lastYearStart, $lastYearEnd])
            ->whereHas('incomeSubCategory.incomeCategory', function ($query) {
                $query->where('id', 13);
            })
            ->sum('amount');

        // Initialize collection for subcategories with incomes in date range
        $incomeSubCategories = collect();

        foreach ($incomecategories as $category) {
            $subCategories = $category->incomeSubCategories()
                ->where('status', 1)
                ->with(['incomes' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }])
                ->get()
                ->filter(function ($subCat) {
                    return $subCat->incomes->isNotEmpty();
                });

            $incomeSubCategories = $incomeSubCategories->merge($subCategories);
        }






        //expense categories
        $expenseCategories = ExpenseCategory::where('status', 1)->get();


        if (!$request->input('startDate') || !$request->input('endDate')) {
            $startDate = Expense::min('date') ?? now()->startOfMonth();
            $endDate = Expense::max('date') ?? now()->endOfMonth();
        }

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Now safe to use ->copy()
        $lastMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $startDate->copy()->subMonth()->endOfMonth();

        $lastYearStart = $startDate->copy()->subYear()->startOfYear();
        $lastYearEnd = $startDate->copy()->subYear()->endOfYear();

        $totalExpensesExcludingCat7 = Expense::whereBetween('date', [$startDate, $endDate])
            ->whereHas('expenseSubCategory.expenseCategory', function ($query) {
                $query->where('id', '!=', 7);
            })
            ->sum('amount'); // assuming 'amount' is the expense value column

        // Total expenses for category 7
        $totalExpensesCat7 = Expense::whereBetween('date', [$startDate, $endDate])
            ->whereHas('expenseSubCategory.expenseCategory', function ($query) {
                $query->where('id', 7);
            })
            ->sum('amount');

        // Previous period expenses excluding category 7
        $previousTotalExpensesExcluding7 = Expense::where('date', '<', $startDate)
            ->whereHas('expenseSubCategory.expenseCategory', function ($query) {
                $query->where('id', '!=', 7);
            })
            ->sum('amount');
        // Previous period expenses for category 7
        $previousTotalExpenses7 = Expense::where('date', '<', $startDate)
            ->whereHas('expenseSubCategory.expenseCategory', function ($query) {
                $query->where('id', 7);
            })
            ->sum('amount');
        // Last month expenses excluding category 7
        $lastMonthExpensesExcluding7 = Expense::whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->whereHas('expenseSubCategory.expenseCategory', function ($query) {
                $query->where('id', '!=', 7);
            })
            ->sum('amount');
        // Last month expenses for category 7
        $lastMonthExpenses7 = Expense::whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->whereHas('expenseSubCategory.expenseCategory', function ($query) {
                $query->where('id', 7);
            })
            ->sum('amount');
        // Last year expenses excluding category 7
        $lastYearExpensesExcluding7 = Expense::whereBetween('date', [$lastYearStart, $lastYearEnd])
            ->whereHas('expenseSubCategory.expenseCategory', function ($query) {
                $query->where('id', '!=', 7);
            })
            ->sum('amount');
        // Last year expenses for category 7
        $lastYearExpenses7 = Expense::whereBetween('date', [$lastYearStart, $lastYearEnd])
            ->whereHas('expenseSubCategory.expenseCategory', function ($query) {
                $query->where('id', 7);
            })
            ->sum('amount');

        // Initialize collection for subcategories with expenses in date range
        $expenseSubCategories = collect();

        foreach ($expenseCategories as $category) {
            $subCategories = $category->expenseSubCategories()
                ->where('status', 1)
                ->with(['expenses' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }])
                ->get()
                ->filter(function ($subCat) {
                    return $subCat->expenses->isNotEmpty();
                });

            $expenseSubCategories = $expenseSubCategories->merge($subCategories);
        }




        // Fetch all investments
        if (!$request->input('startDate') || !$request->input('endDate')) {
            $startDate = Income::min('date') ?? now()->startOfMonth();
            $endDate = Income::max('date') ?? now()->endOfMonth();
        }
        $investmentIncomes = Investment::with(['investIncome' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }])->get();

        if (!$request->input('startDate') || !$request->input('endDate')) {
            $startDate = Expense::min('date') ?? now()->startOfMonth();
            $endDate = Expense::max('date') ?? now()->endOfMonth();
        }
        $investmentExpenses = Investment::with(['investExpense' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }])->get();


        $totalpreviousBalance = ( $previousTotalIncomeExcluding13 + $previousTotalIncome13 ) - ( $previousTotalExpensesExcluding7 + $previousTotalExpenses7 );

        return view('admin.accounts.incomeExpenseStatement',[
            'expenseCategories' => $expenseCategories,
            'expenseSubCategories' => $expenseSubCategories,
            'incomecategories' => $incomecategories,
            'incomeSubCategories' => $incomeSubCategories,
            'totalIncomesExcludingCat13' => $totalIncomesExcludingCat13,
            'totalIncomeCat13' => $totalIncomeCat13,
            'totalExpensesExcludingCat7' => $totalExpensesExcludingCat7,
            'totalExpensesCat7' => $totalExpensesCat7,
            'investmentIncomes' => $investmentIncomes,
            'investmentExpenses' => $investmentExpenses,
            'startDate' => $request->input('startDate') ?? null,
            'endDate' => $request->input('endDate') ?? null,
            'totalpreviousBalance' => $totalpreviousBalance,
        ]);
    }
}
