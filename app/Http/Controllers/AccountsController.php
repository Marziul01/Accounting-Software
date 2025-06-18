<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Models\Liability;
use App\Models\LiabilityTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountsController extends Controller
{
    public function Cashflowstatement(Request $request)
    {
        if ($request->input('startDate') && $request->input('endDate')){
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
        }
        

        // Fallback to earliest and latest transaction dates if not provided
        if (!$request->input('startDate') || !$request->input('endDate')) {
            $startDate = InvestmentTransaction::min('transaction_date') ?? now()->startOfMonth();
            $endDate   = InvestmentTransaction::max('transaction_date') ?? now()->endOfMonth();
        }

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Now safe to use ->copy()
        $lastMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $startDate->copy()->subMonth()->endOfMonth();

        $lastYearStart = $startDate->copy()->subYear()->startOfYear();
        $lastYearEnd = $startDate->copy()->subYear()->endOfYear();

        // Fetch all investments and their transactions between the dates
        $allInvestments = Investment::with(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }])->get();

        $totalInvestDeposit  = 0;
        $totalInvestWithdraw = 0;

        $previousTotalInvestDeposit = 0;
        $previousTotalInvestWithdraw = 0;

        $lastMonthInvestDeposit = 0;
        $lastMonthInvestWithdraw = 0;

        $lastYearInvestDeposit = 0;
        $lastYearInvestWithdraw = 0;

        foreach ($allInvestments as $investment) {
            $totalInvestDeposit  += $investment->transactions
                                    ->where('transaction_type', 'Deposit')
                                    ->sum('amount');

            $totalInvestWithdraw += $investment->transactions
                                    ->where('transaction_type', 'Withdraw')
                                    ->sum('amount');


            // Previous period
            $previousTotalInvestDeposit += $investment->allTransactions()
                ->where('transaction_type', 'Deposit')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            $previousTotalInvestWithdraw += $investment->allTransactions()
                ->where('transaction_type', 'Withdraw')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            // Last month
            $lastMonthInvestDeposit += $investment->allTransactions()
                ->where('transaction_type', 'Deposit')
                ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                ->sum('amount');

            $lastMonthInvestWithdraw += $investment->allTransactions()
                ->where('transaction_type', 'Withdraw')
                ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                ->sum('amount');

            // Last year
            $lastYearInvestDeposit += $investment->allTransactions()
                ->where('transaction_type', 'Deposit')
                ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                ->sum('amount');

            $lastYearInvestWithdraw += $investment->allTransactions()
                ->where('transaction_type', 'Withdraw')
                ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                ->sum('amount');
        }



        // ----- CURRENT ASSETS -----

        if (!$request->input('startDate') || !$request->input('endDate')) {
            $firstAssetDate  = Asset::min('entry_date');
            $lastAssetDate   = Asset::max('entry_date');
            $firstTxnDate    = AssetTransaction::min('transaction_date');
            $lastTxnDate     = AssetTransaction::max('transaction_date');

            $minDates = array_filter([$firstAssetDate, $firstTxnDate]);
            $maxDates = array_filter([$lastAssetDate, $lastTxnDate]);

            $startDate = !empty($minDates) ? Carbon::parse(min($minDates)) : now()->startOfMonth();
            $endDate   = !empty($maxDates) ? Carbon::parse(max($maxDates)) : now()->endOfMonth();
        }

        $startDate = Carbon::parse($startDate);
        $endDate   = Carbon::parse($endDate);

        $lastMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $lastMonthEnd   = $startDate->copy()->subMonth()->endOfMonth();

        $lastYearStart  = $startDate->copy()->subYear()->startOfYear();
        $lastYearEnd    = $startDate->copy()->subYear()->endOfYear();

        $allCurrentAssets = Asset::where('category_id', '4')->with(['transactions' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('transaction_date', [$startDate, $endDate]);
        }])->get();

        $totalCurrentAssetDeposit     = 0;
        $totalCurrentAssetWithdraw    = 0;
        $previousCurrentAssetDeposit  = 0;
        $previousCurrentAssetWithdraw = 0;
        $lastMonthCurrentAssetDeposit = 0;
        $lastMonthCurrentAssetWithdraw= 0;
        $lastYearCurrentAssetDeposit  = 0;
        $lastYearCurrentAssetWithdraw = 0;

        foreach ($allCurrentAssets as $asset) {
            $allTxns = $asset->allTransactions;

            

            // Current transactions (from eager-loaded relationship)
            $totalCurrentAssetDeposit  += $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
            $totalCurrentAssetWithdraw += $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');

            
                // Previous period totals
                $previousCurrentAssetDeposit += $asset->allTransactions()
                    ->where('transaction_type', 'Deposit')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                $previousCurrentAssetWithdraw += $asset->allTransactions()
                    ->where('transaction_type', 'Withdraw')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');


                // Last month
                $lastMonthCurrentAssetDeposit += $allTxns
                    ->where('transaction_type', 'Deposit')
                    ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                    ->sum('amount');

                

                $lastMonthCurrentAssetWithdraw += $allTxns
                    ->where('transaction_type', 'Withdraw')
                    ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                    ->sum('amount');

                // Last year
                $lastYearCurrentAssetDeposit += $allTxns
                    ->where('transaction_type', 'Deposit')
                    ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                    ->sum('amount');


                $lastYearCurrentAssetWithdraw += $allTxns
                    ->where('transaction_type', 'Withdraw')
                    ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                    ->sum('amount');
            
        }







        // ----- LIABILITIES -----

        // Fallback to earliest and latest transaction dates
        if (!$request->input('startDate') || !$request->input('endDate')) {
            $firstLiabilityDate = Liability::min('entry_date');
            $lastLiabilityDate  = Liability::max('entry_date');
            $firstTxnDate       = LiabilityTransaction::min('transaction_date');
            $lastTxnDate        = LiabilityTransaction::max('transaction_date');

            $minDates = array_filter([$firstLiabilityDate, $firstTxnDate]);
            $maxDates = array_filter([$lastLiabilityDate, $lastTxnDate]);

            $startDate = !empty($minDates) ? Carbon::parse(min($minDates)) : now()->startOfMonth();
            $endDate   = !empty($maxDates) ? Carbon::parse(max($maxDates)) : now()->endOfMonth();
        }

        $startDate = Carbon::parse($startDate);
        $endDate   = Carbon::parse($endDate);

        $lastMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $lastMonthEnd   = $startDate->copy()->subMonth()->endOfMonth();

        $lastYearStart  = $startDate->copy()->subYear()->startOfYear();
        $lastYearEnd    = $startDate->copy()->subYear()->endOfYear();

        $allLiabilities = Liability::with(['transactions' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('transaction_date', [$startDate, $endDate]);
        }])->get();

        $totalLiabilityDeposit         = 0;
        $totalLiabilityWithdraw        = 0;
        $totalPreviousLiabilityDeposit  = 0;
        $totalPreviousLiabilityWithdraw  = 0;
        $lastMonthLiabilityDeposit     = 0;
        $lastMonthLiabilityWithdraw    = 0;
        $lastYearLiabilityDeposit      = 0;
        $lastYearLiabilityWithdraw     = 0;

        foreach ($allLiabilities as $liability) {
            $allTxns = $liability->allTransactions;

            

            // Current transactions (from eager-loaded relationship)
            $totalLiabilityDeposit  += $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
            $totalLiabilityWithdraw += $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');

            
                // Previous Period (before startDate)
                $totalPreviousLiabilityDeposit += $liability->allTransactions()
                    ->where('transaction_type', 'Deposit')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                $totalPreviousLiabilityWithdraw += $liability->allTransactions()
                    ->where('transaction_type', 'Withdraw')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');


                // Last Month
                $monthDeposit = $allTxns
                    ->where('transaction_type', 'Deposit')
                    ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                    ->sum('amount');

                $monthWithdraw = $allTxns
                    ->where('transaction_type', 'Withdraw')
                    ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                    ->sum('amount');

                

                $lastMonthLiabilityDeposit  += $monthDeposit;
                $lastMonthLiabilityWithdraw += $monthWithdraw;

                // Last Year
                $yearDeposit = $allTxns
                    ->where('transaction_type', 'Deposit')
                    ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                    ->sum('amount');

                $yearWithdraw = $allTxns
                    ->where('transaction_type', 'Withdraw')
                    ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                    ->sum('amount');

                

                $lastYearLiabilityDeposit  += $yearDeposit;
                $lastYearLiabilityWithdraw += $yearWithdraw;

        }




        // ----- BANKS -----

        // Fallback to earliest and latest transaction dates if not provided
        if (!$request->input('startDate') || !$request->input('endDate')) {
            $startDate = BankTransaction::min('transaction_date') ?? now()->startOfMonth();
            $endDate   = BankTransaction::max('transaction_date') ?? now()->endOfMonth();
        }

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Now safe to use ->copy()
        $lastMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $startDate->copy()->subMonth()->endOfMonth();

        $lastYearStart = $startDate->copy()->subYear()->startOfYear();
        $lastYearEnd = $startDate->copy()->subYear()->endOfYear();


        // Fetch all bank accounts and their transactions between the dates
        $allBanks = BankAccount::with(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }])->get();

        $totalBankDeposit  = 0;
        $totalBankWithdraw = 0;

        $previousTotalBankDeposit = 0;
        $previousTotalBankWithdraw = 0;

        $lastMonthBankDeposit = 0;
        $lastMonthBankWithdraw = 0;

        $lastYearBankDeposit = 0;
        $lastYearBankWithdraw = 0;

        foreach ($allBanks as $bank) {
            $totalBankDeposit  += $bank->transactions
                                    ->where('transaction_type', 'credit')
                                    ->sum('amount');

            $totalBankWithdraw += $bank->transactions
                                    ->where('transaction_type', 'debit')
                                    ->sum('amount');


            // Previous period
            $previousTotalBankDeposit += $bank->allTransactions()
                ->where('transaction_type', 'credit')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');
            $previousTotalBankWithdraw += $bank->allTransactions()
                ->where('transaction_type', 'debit')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');
            // Last month
            $lastMonthBankDeposit += $bank->allTransactions()
                ->where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                ->sum('amount');
            $lastMonthBankWithdraw += $bank->allTransactions()
                ->where('transaction_type', 'debit')
                ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                ->sum('amount');
            // Last year
            $lastYearBankDeposit += $bank->allTransactions()
                ->where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                ->sum('amount');
            $lastYearBankWithdraw += $bank->allTransactions()
                ->where('transaction_type', 'debit')
                ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                ->sum('amount');
        }


        // Fetch all fixed assets and their transactions

        if (!$request->input('startDate') || !$request->input('endDate')) {

            $firstAssetDate  = Asset::min('entry_date');
            $lastAssetDate   = Asset::max('entry_date');
            $firstTxnDate    = AssetTransaction::min('transaction_date');
            $lastTxnDate     = AssetTransaction::max('transaction_date');

            // Collect non-null dates
            $minDates = array_filter([$firstAssetDate, $firstTxnDate]);
            $maxDates = array_filter([$lastAssetDate, $lastTxnDate]);

            // Parse as Carbon and take earliest/latest
            $startDate = !empty($minDates) ? Carbon::parse(min($minDates)) : now()->startOfMonth();
            $endDate   = !empty($maxDates) ? Carbon::parse(max($maxDates)) : now()->endOfMonth();
        }

        $startDate = Carbon::parse($startDate);
        $endDate   = Carbon::parse($endDate);

        $lastMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $lastMonthEnd   = $startDate->copy()->subMonth()->endOfMonth();

        $lastYearStart  = $startDate->copy()->subYear()->startOfYear();
        $lastYearEnd    = $startDate->copy()->subYear()->endOfYear();

        // Fetch all fixed assets
        $allFixedAssets = Asset::where('category_id', 5)
            ->with(['transactions' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            }])
            ->get();

        $totalFixedAsset = 0;
        $totalPreviousFixedAssetAmount = 0;
        $totallastMonthFixedAssetAmount = 0;
        $totallastYearFixedAssetAmount = 0;

        foreach ($allFixedAssets as $asset) {
            $allTxns = $asset->allTransactions();

            

            // Transactions between the dates
            $deposits = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
            $withdraws = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');

            

            
                // Calculate previous value
                $prevDeposits = $asset->allTransactions()
                    ->where('transaction_type', 'Deposit')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                $prevWithdraws = $asset->allTransactions()
                    ->where('transaction_type', 'Withdraw')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                
                $totalPreviousFixedAssetAmount += ($prevDeposits - $prevWithdraws);

                // === Last Month ===
                $monthDeposits = $allTxns
                    ->where('transaction_type', 'Deposit')
                    ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                    ->sum('amount');

                $monthWithdraws = $allTxns
                    ->where('transaction_type', 'Withdraw')
                    ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
                    ->sum('amount');

                // If the asset was created last month, include its initial amount
                

                $totallastMonthFixedAssetAmount += ($monthDeposits - $monthWithdraws);


                // === Last Year ===
                $yearDeposits = $allTxns
                    ->where('transaction_type', 'Deposit')
                    ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                    ->sum('amount');

                $yearWithdraws = $allTxns
                    ->where('transaction_type', 'Withdraw')
                    ->whereBetween('transaction_date', [$lastYearStart, $lastYearEnd])
                    ->sum('amount');

                // If the asset was created last year, include its initial amount
                

                $totallastYearFixedAssetAmount += ($yearDeposits - $yearWithdraws);
            

            $totalFixedAsset += ($deposits - $withdraws);
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




        $previousPeriod = $request->input('previousPeriod');

        if($request->input('previousPeriod') == 'lastMonth'){
            $totalpreviousBalance =  ( $lastMonthIncomeExcluding13 + $lastMonthIncome13 + $lastMonthInvestDeposit + $lastMonthCurrentAssetWithdraw  + $lastMonthLiabilityDeposit + $lastMonthBankWithdraw )
            - ( $lastMonthExpensesExcluding7 + $lastMonthExpenses7 + $lastMonthInvestWithdraw + $lastMonthCurrentAssetDeposit + $lastMonthLiabilityWithdraw + $lastMonthBankDeposit + $totallastMonthFixedAssetAmount );
        }else if($request->input('previousPeriod') == 'lastYear'){
            $totalpreviousBalance =  ( $lastYearIncomeExcluding13 + $lastYearIncome13 + $lastYearInvestDeposit + $lastYearCurrentAssetWithdraw  + $lastYearLiabilityDeposit + $lastYearBankWithdraw )
            - ( $lastYearExpensesExcluding7 + $lastYearExpenses7 + $lastYearInvestWithdraw + $lastYearCurrentAssetDeposit + $lastYearLiabilityWithdraw + $lastYearBankDeposit + $totallastYearFixedAssetAmount );
        }else if($request->input('previousPeriod') == null ){
            $totalpreviousBalance = ( $previousTotalIncomeExcluding13 + $previousTotalIncome13 + $previousTotalInvestDeposit + $previousCurrentAssetWithdraw  + $totalPreviousLiabilityDeposit + $previousTotalBankWithdraw )
            - ( $previousTotalExpensesExcluding7 + $previousTotalExpenses7 + $previousTotalInvestWithdraw + $previousCurrentAssetDeposit + $totalPreviousLiabilityWithdraw + $previousTotalBankDeposit + $totalPreviousFixedAssetAmount );
        }
        else{
            $totalpreviousBalance = ( $previousTotalIncomeExcluding13 + $previousTotalIncome13 + $previousTotalInvestDeposit + $previousCurrentAssetWithdraw  + $totalPreviousLiabilityDeposit + $previousTotalBankWithdraw )
            - ( $previousTotalExpensesExcluding7 + $previousTotalExpenses7 + $previousTotalInvestWithdraw + $previousCurrentAssetDeposit + $totalPreviousLiabilityWithdraw + $previousTotalBankDeposit + $totalPreviousFixedAssetAmount );
        }
 
        // dd(  $totalpreviousBalance,
        //     $previousTotalIncomeExcluding13, $previousTotalIncome13, $previousTotalInvestDeposit, $previousCurrentAssetWithdraw, 
        //     $totalPreviousLiabilityDeposit, $previousTotalBankWithdraw, 
        //     $previousTotalExpensesExcluding7, $previousTotalExpenses7, $previousTotalInvestWithdraw, 
        //     $previousCurrentAssetDeposit, $totalPreviousLiabilityWithdraw, $previousTotalBankDeposit, 
        //     $totalPreviousFixedAssetAmount
        // );
        

        return view('admin.accounts.Cashflowstatement',[
            'totalInvestDeposit'  => $totalInvestDeposit,
            'totalInvestWithdraw' => $totalInvestWithdraw,

            'totalCurrentAssetDeposit'   => $totalCurrentAssetDeposit,
            'totalCurrentAssettWithdraw'  => $totalCurrentAssetWithdraw,

            'totalLiabilitytDeposit'   => $totalLiabilityDeposit,
            'totalLiabilityWithdraw'  => $totalLiabilityWithdraw,

            'totalBankDeposit'   => $totalBankDeposit,
            'totalBankWithdraw'  => $totalBankWithdraw,

            'totalFixedAsset'  => $totalFixedAsset,

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
            'previousPeriod' => $previousPeriod,
        ]);
    }

}
