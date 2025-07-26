<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\Liability;
use App\Models\LiabilityTransaction;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\IncomeSubCategory;
use App\Models\ExpenseSubCategory;
use App\Models\AssetCategory;
use App\Models\LiabilityCategory;
use App\Models\BankAccountCategory;

class FinancialStatement extends Controller
{
    public function financialStatement(Request $request)
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

        // Fetch all investments and their transactions between the dates
        $allInvestments = Investment::with([
            'transactions' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            },
            'investExpense' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        ])->get();

        $totalInvestDeposit  = 0;
        $totalInvestWithdraw = 0;
        $totalInvestExpenses = 0;
        $totalInvestAmount = 0;

        $previousTotalInvestDeposit = 0;
        $previousTotalInvestWithdraw = 0;
        $previousTotalInvestAmount = 0;

        foreach ($allInvestments as $investment) {
            $deposit = $investment->transactions
                        ->where('transaction_type', 'Deposit')
                        ->sum('amount');

            $withdraw = $investment->transactions
                        ->where('transaction_type', 'Withdraw')
                        ->sum('amount');
            
            $expenses = $investment->investExpense->sum('amount');

            $totalInvestDeposit += $deposit;
            $totalInvestWithdraw += $withdraw;
            $totalInvestExpenses += $expenses;

            $totalInvestAmount += ($deposit - $withdraw - $expenses) ; // ✅ correct

            // Previous period
            $prevDeposit = $investment->allTransactions()
                ->where('transaction_type', 'Deposit')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            $prevWithdraw = $investment->allTransactions()
                ->where('transaction_type', 'Withdraw')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');
            
            $prevexpese = $investment->allinvestExpense()
                ->where('date', '<', $startDate)
                ->sum('amount');

            $previousTotalInvestDeposit += $prevDeposit;
            $previousTotalInvestWithdraw += $prevWithdraw;

            $previousTotalInvestAmount += ($prevDeposit - $prevWithdraw - $prevexpese);
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


        $allCurrentAssets = Asset::where('category_id', '4')->with(['transactions' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('transaction_date', [$startDate, $endDate]);
        }])->get();

        $totalCurrentAssetDeposit     = 0;
        $totalCurrentAssetWithdraw    = 0;
        $totalCurrentAssetAmount = 0;

        $previousCurrentAssetDeposit  = 0;
        $previousCurrentAssetWithdraw = 0;
        $previousCurrentAssetAmount = 0;
        

        foreach ($allCurrentAssets as $asset) {
            $allTxns = $asset->allTransactions;

            

            // Current transactions (from eager-loaded relationship)
            $deposit  = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
            $withdraw = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');

            
                // Previous period totals
                $prevDeposits = $asset->allTransactions()
                    ->where('transaction_type', 'Deposit')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                $prevWithdraws = $asset->allTransactions()
                    ->where('transaction_type', 'Withdraw')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                
                $previousCurrentAssetDeposit  += $prevDeposits;
                $previousCurrentAssetWithdraw += $prevWithdraws;
                $previousCurrentAssetAmount += ($prevDeposits - $prevWithdraws);


            $totalCurrentAssetDeposit  += $deposit;
            $totalCurrentAssetWithdraw += $withdraw;
            $totalCurrentAssetAmount += ($deposit - $withdraw);
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


        $allShortLiabilities = Liability::where('category_id' , 3 )->with(['transactions' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('transaction_date', [$startDate, $endDate]);
        }])->get();

        $totalShortLiabilityDeposit         = 0;
        $totalShortLiabilityWithdraw        = 0;
        $totalShortLiabilityAmount = 0;

        $totalPreviousShortLiabilityDeposit  = 0;
        $totalPreviousShortLiabilityWithdraw  = 0;
        $totalPreviousShortLiabilityAmount = 0;
        

        foreach ($allShortLiabilities as $liability) {
            $allTxns = $liability->allTransactions;

            

            // Current transactions (from eager-loaded relationship)
            $deposit  = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
            $withdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');

            
                // Previous Period (before startDate)
                $prevDeposits = $liability->allTransactions()
                    ->where('transaction_type', 'Deposit')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                $prevWithdraws = $liability->allTransactions()
                    ->where('transaction_type', 'Withdraw')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                
                $totalPreviousShortLiabilityDeposit  += $prevDeposits;
                $totalPreviousShortLiabilityWithdraw += $prevWithdraws;

                $totalPreviousShortLiabilityAmount += ($prevDeposits - $prevWithdraws);

            $totalShortLiabilityDeposit  += $deposit;
            $totalShortLiabilityWithdraw += $withdraw;
            $totalShortLiabilityAmount += ($deposit - $withdraw);
        }


        // Fetch all long-term liabilities
        $allLongLiabilities = Liability::where('category_id', 4)->with(['transactions' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('transaction_date', [$startDate, $endDate]);
        }])->get();
        $totalLongLiabilityDeposit         = 0;
        $totalLongLiabilityWithdraw        = 0;
        $totalLongLiabilityAmount = 0;
        $totalPreviousLongLiabilityDeposit  = 0;
        $totalPreviousLongLiabilityWithdraw  = 0;
        $totalPreviousLongLiabilityAmount = 0;

        foreach ($allLongLiabilities as $liability) {
            $allTxns = $liability->allTransactions;

            

            // Current transactions (from eager-loaded relationship)
            $deposit  = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
            $withdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');

            
                // Previous Period (before startDate)
                $prevDeposits = $liability->allTransactions()
                    ->where('transaction_type', 'Deposit')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                $prevWithdraws = $liability->allTransactions()
                    ->where('transaction_type', 'Withdraw')
                    ->where('transaction_date', '<', $startDate)
                    ->sum('amount');

                
                $totalPreviousLongLiabilityDeposit  += $prevDeposits;
                $totalPreviousLongLiabilityWithdraw += $prevWithdraws;

                $totalPreviousLongLiabilityAmount += ($prevDeposits - $prevWithdraws);


            // Total amount for the liability
            $totalLongLiabilityDeposit  += $deposit;
            $totalLongLiabilityWithdraw += $withdraw;
            $totalLongLiabilityAmount += ($deposit - $withdraw);
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
        $totalBankAmount = 0;

        $previousTotalBankDeposit = 0;
        $previousTotalBankWithdraw = 0;
        $previousTotalBankAmount = 0;

        foreach ($allBanks as $bank) {
            // Current period
            $deposit = $bank->transactions
                        ->where('transaction_type', 'credit')
                        ->sum('amount');

            $withdraw = $bank->transactions
                        ->where('transaction_type', 'debit')
                        ->sum('amount');

            $totalBankDeposit  += $deposit;
            $totalBankWithdraw += $withdraw;
            $totalBankAmount   += ($deposit - $withdraw); // ✅ correct

            // Previous period
            $prevDeposit = $bank->allTransactions()
                ->where('transaction_type', 'credit')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            $prevWithdraw = $bank->allTransactions()
                ->where('transaction_type', 'debit')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            $previousTotalBankDeposit  += $prevDeposit;
            $previousTotalBankWithdraw += $prevWithdraw;
            $previousTotalBankAmount   += ($prevDeposit - $prevWithdraw); // ✅ correct
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


        // Fetch all fixed assets
        $allFixedAssets = Asset::where('category_id', 5)
            ->with(['transactions' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            }])
            ->get();

        $totalFixedAsset = 0;
        $totalPreviousFixedAssetAmount = 0;

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



        //expense categories
        $expenseCategories = ExpenseCategory::where('status', 1)->get();


        if (!$request->input('startDate') || !$request->input('endDate')) {
            $startDate = Expense::min('date') ?? now()->startOfMonth();
            $endDate = Expense::max('date') ?? now()->endOfMonth();
        }

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);


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


        $totalNetGainorLossBalance = ( $totalIncomesExcludingCat13 + $totalIncomeCat13 ) - ( $totalExpensesExcludingCat7 + $totalExpensesCat7 );

        $totalpreviousBalance = ( $previousTotalIncomeExcluding13 + $previousTotalIncome13 + $previousTotalInvestWithdraw + $previousCurrentAssetWithdraw  + $totalPreviousLongLiabilityDeposit + $totalPreviousShortLiabilityDeposit + $previousTotalBankWithdraw )
            - ( $previousTotalExpensesExcluding7 + $previousTotalExpenses7 + $previousTotalInvestDeposit + $previousCurrentAssetDeposit + $totalPreviousLongLiabilityWithdraw + $totalPreviousShortLiabilityWithdraw + $previousTotalBankDeposit + $totalPreviousFixedAssetAmount );
        
        $currrentBalance = ( $totalIncomesExcludingCat13 + $totalIncomeCat13 + $totalInvestWithdraw + $totalCurrentAssetWithdraw + $totalLongLiabilityDeposit + $totalShortLiabilityDeposit + $totalBankWithdraw )
            - ( $totalExpensesExcludingCat7 + $totalExpensesCat7 + $totalInvestDeposit+ $totalCurrentAssetDeposit + $totalLongLiabilityWithdraw + $totalShortLiabilityWithdraw + $totalBankDeposit + $totalFixedAsset ) + $totalpreviousBalance ;

        // $handCash = $currrentBalance - ($totalBankDeposit - $totalBankWithdraw);
        $handCash = $currrentBalance;

        return view('admin.accounts.financialStatement',[
            'totalInvestDeposit'  => $totalInvestDeposit,
            'totalInvestWithdraw' => $totalInvestWithdraw,

            'totalCurrentAssetDeposit'   => $totalCurrentAssetDeposit,
            'totalCurrentAssetWithdraw'  => $totalCurrentAssetWithdraw,


            'totalBankDeposit'   => $totalBankDeposit,
            'totalBankWithdraw'  => $totalBankWithdraw,

            'totalFixedAsset'  => $totalFixedAsset,
            
            'totalIncomesExcludingCat13' => $totalIncomesExcludingCat13,
            'totalIncomeCat13' => $totalIncomeCat13,
            'totalExpensesExcludingCat7' => $totalExpensesExcludingCat7,
            'totalExpensesCat7' => $totalExpensesCat7,
            
            'startDate' => $request->input('startDate') ?? null,
            'endDate' => $request->input('endDate') ?? null,
            'totalpreviousBalance' => $totalpreviousBalance,
            'currrentBalance' => $currrentBalance,
            'handCash' => $handCash,
            'totalNetGainorLossBalance' => $totalNetGainorLossBalance,
            
            'totalShortLiabilityDeposit' => $totalShortLiabilityDeposit,
            'totalShortLiabilityWithdraw' => $totalShortLiabilityWithdraw,
            'totalLongLiabilityDeposit' => $totalLongLiabilityDeposit,
            'totalLongLiabilityWithdraw' => $totalLongLiabilityWithdraw,
            'totalInvestAmount' => $totalInvestAmount,
        ]);
    }
}
