<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetSubCategory;
use App\Models\AssetTransaction;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Investment;
use App\Models\InvestmentCategory;
use App\Models\InvestmentSubCategory;
use App\Models\InvestmentTransaction;
use App\Models\Liability;
use App\Models\LiabilitySubCategory;
use App\Models\LiabilityTransaction;
use App\Models\Notification;
use App\Models\SMSEMAILTEMPLATE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Notification::where('created_at', '<', now()->subDays(3))->exists()) {
            Notification::where('created_at', '<', now()->subDays(3))
            ->delete();
        }

        // Monthly Chart

        $year = now()->year;

        $monthlyData = [
            'income'      => $this->getMonthlySums(Income::class, $year),
            'expenses'    => $this->getMonthlySums(Expense::class, $year),
            'assets'      => $this->getMonthlySums2(Asset::class, $year),
            'liabilities' => $this->getMonthlySums2(Liability::class, $year),
        ];

        //Bank Transactions 
        $latestBankTransactions = BankTransaction::orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        $banks = BankAccount::all();
        $totalbank = 0;
        foreach ($banks as $bank) {
            $totalbank += $bank->transactions->where('transaction_type', 'credit')->sum('amount') - $bank->transactions->where('transaction_type', 'debit')->sum('amount');
        }



        //Yearly Compare Chart
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        $monthlyComparisonData  = [
            'income'      => $this->getComparisonData(Income::class, $currentYear, $previousYear, 'date'),
            'expenses'    => $this->getComparisonData(Expense::class, $currentYear, $previousYear, 'date'),
            'assets'      => $this->getComparisonData(Asset::class, $currentYear, $previousYear, 'entry_date'),
            'liabilities' => $this->getComparisonData(Liability::class, $currentYear, $previousYear, 'entry_date'),
        ];


        $incomes = Income::select('id', 'amount', 'date', 'description', 'name')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Income',
                    'name' => $item->name ?? 'N/A',
                    'amount' => $item->amount,
                    'date' => $item->date,
                    'description' => $item->description,
                    'transaction_type' => null,
                ];
            });


        $expenses = Expense::select('id', 'amount', 'date', 'description', 'name')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Expense',
                    'name' => $item->name ?? 'N/A',
                    'amount' => $item->amount,
                    'date' => $item->date,
                    'description' => $item->description,
                    'transaction_type' => null,
                ];
            });

        $assets = AssetTransaction::with('asset')
            ->select('id', 'amount', 'transaction_date as date', 'description', 'transaction_type', 'asset_id')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Asset',
                    'amount' => $item->amount,
                    'name' => $item->asset?->name ?? 'N/A',
                    'transaction_type' => $item->transaction_type,
                    'date' => $item->date,
                    'description' => $item->description,
                ];
            });


        $liabilities = LiabilityTransaction::with('liability')
            ->select('id', 'amount', 'transaction_date as date', 'description', 'transaction_type', 'liability_id')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Liability',
                    'amount' => $item->amount,
                    'name' => $item->liability?->name ?? 'N/A',
                    'transaction_type' => $item->transaction_type,
                    'date' => $item->date,
                    'description' => $item->description,
                ];
            });

        $investments = InvestmentTransaction::with('investment')
            ->select('id', 'amount', 'transaction_date as date', 'description', 'transaction_type', 'investment_id')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Investment',
                    'amount' => $item->amount,
                    'name' => $item->investment?->name ?? 'N/A',
                    'transaction_type' => $item->transaction_type,
                    'date' => $item->date,
                    'description' => $item->description,
                ];
            });

        // Merge all collections
        $merged = collect()
            ->merge($incomes)
            ->merge($expenses)
            ->merge($assets)
            ->merge($liabilities)
            ->merge($investments)
            ->sortByDesc('date')
            ->take(5)
            ->values();


        $allInvestmentss = Investment::all();
        $alltotalinvestments = 0;
        foreach ($allInvestmentss as $investment) {
            $alltotalinvestments += $investment->transactions->where('transaction_type', 'Deposit')->sum('amount') - $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
        }


        
        $occasions = SMSEMAILTEMPLATE::get();
        $events = collect();

        foreach ($occasions as $occasion) {
            if (in_array($occasion->occassion, ['Birthday', 'Anniversary']) && $occasion->contact_ids) {
                $contactIds = explode(',', $occasion->contact_ids);
                $contacts = Contact::whereIn('id', $contactIds)->get();

                foreach ($contacts as $contact) {
                    $originalDate = $occasion->occassion === 'Birthday' 
                        ? $contact->date_of_birth 
                        : $contact->marriage_date;

                    if ($originalDate) {
                        $date = Carbon::parse($originalDate);

                        // Build the event date for this year
                        $eventDate = Carbon::createFromDate(now()->year, $date->month, $date->day);

                        // If already passed this year, shift to next year
                        if ($eventDate->isBefore(now()->startOfDay())) {
                            $eventDate->addYear();
                        }

                        $events->push([
                            'title' => $contact->name,
                            'date'  => $eventDate,
                            'type'  => $occasion->occassion,
                        ]);
                    }
                }
            } else {
                if ($occasion->custom_date) {
                    $eventDate = Carbon::parse($occasion->custom_date);

                    if ($eventDate->isBefore(now()->startOfDay())) {
                        $eventDate->addYear();
                    }

                    $events->push([
                        'title' => $occasion->occassion,
                        'date'  => $eventDate,
                    ]);
                }
            }
        }

        // Sort by upcoming date and take next 5
        $finalOccasions = $events->sortBy('date')->take(5);


        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $currentMonthtotals = [
            'incomes'     => Income::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount'),
            'expenses'    => Expense::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount'),
            'assets'      => AssetTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->where('transaction_type','Deposit')->sum('amount') - AssetTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->where('transaction_type','Withdraw')->sum('amount'),
            'liabilities' => LiabilityTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->where('transaction_type','Deposit')->sum('amount') - LiabilityTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->where('transaction_type','Withdraw')->sum('amount'),
            'investments' => InvestmentTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->where('transaction_type','Deposit')->sum('amount') - InvestmentTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->where('transaction_type','Withdraw')->sum('amount'),
            'bankbooks'   => BankTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->where('transaction_type','credit')->sum('amount') - BankTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->where('transaction_type','debit')->sum('amount'),
        ];

        

        return view('admin.dashboard.dashboard', [
            'monthlyData' => $monthlyData,
            'latestBankTransactions' => $latestBankTransactions,
            'monthlyComparisonData' => $monthlyComparisonData,
            'merged' => $merged,
            'incomes' => $incomes,
            'allassets' => Asset::all(),
            'allliabilities' => Liability::all(),
            'expenses' => $expenses,
            'alltotalinvestments' => $alltotalinvestments,
            'totalbank' => $totalbank,
            'incomeCategories' => IncomeCategory::where('status', 1)->where('id', '!=', 13)->get(),
            'finalOccasions' => $finalOccasions,
            'currentMonthtotals' => $currentMonthtotals,
            'banks' => $banks,
        ]);
    }

    private function getMonthlySums($model, $year)
    {
        $raw = $model::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = isset($raw[$i]) ? (float) $raw[$i] : 0;
        }

        return $result;
    }

    private function getMonthlySums2($model, $year)
    {
        $raw = $model::selectRaw('MONTH(entry_date) as month, SUM(amount) as total')
            ->whereYear('entry_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = isset($raw[$i]) ? (float) $raw[$i] : 0;
        }

        return $result;
    }

    private function getComparisonData($model, $currentYear, $previousYear, $dateField)
    {
        $current = $model::selectRaw("MONTH($dateField) as month, SUM(amount) as total")
            ->whereYear($dateField, $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $previous = $model::selectRaw("MONTH($dateField) as month, SUM(amount) as total")
            ->whereYear($dateField, $previousYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $result = [
            'current' => [],
            'previous' => []
        ];

        for ($i = 1; $i <= 12; $i++) {
            $result['current'][] = isset($current[$i]) ? (float) $current[$i] : 0;
            $result['previous'][] = isset($previous[$i]) ? (float) $previous[$i] : 0;
        }

        return $result;
    }

    public function Incomemodal()
    {
        if (auth()->user()->access->income != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        return view('admin.income.modal' ,[
            'incomeCategories' => IncomeCategory::where('status', 1)->where('id', '!=', 13)->get(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function Expensemodal()
    {
        if (auth()->user()->access->expense != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        return view('admin.expense.modal' ,[
            'expenseCategories' => ExpenseCategory::where('status', 1)->where('id', '!=', 7)->get(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function assetmodal()
    {
        if (auth()->user()->access->asset != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        $assets = Asset::where('category_id', 4)->get();

        return view('admin.asset.modal' ,[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 4)->where('status', 1)->get(),
            'assetTransactions' => AssetTransaction::all(),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function currentassettransaction()
    {
        if (auth()->user()->access->asset != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $assets = Asset::where('category_id', 4)->get();
        return view('admin.asset.modalTransaction' ,[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 4)->where('status', 1)->get(),
            'assetTransactions' => AssetTransaction::all(),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function fixedasset()
    {
        if (auth()->user()->access->asset != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        $assets = Asset::where('category_id', 5)->get();

        return view('admin.asset.fixedmodal' ,[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 5)->where('status', 1)->get(),
            'assetTransactions' => AssetTransaction::all(),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function fixedassettransaction()
    {
        if (auth()->user()->access->asset != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $assets = Asset::where('category_id', 5)->get();
        return view('admin.asset.fixedmodalTransaction' ,[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 5)->where('status', 1)->get(),
            'assetTransactions' => AssetTransaction::all(),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
        ]);
    }




    public function currentliability()
    {
        if (auth()->user()->access->liability != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        $Liabilities = Liability::where('category_id', 3)->get();

        return view('admin.liability.currentliability' ,[
            'liabilities' => $Liabilities,
            'liabilityCategories' => LiabilitySubCategory::where('liability_category_id', 3)->where('status', 1)->get(),
            'liabilityTransactions' => LiabilityTransaction::all(),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function currentliabilitytransaction()
    {
        if (auth()->user()->access->liability != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $Liabilities = Liability::where('category_id', 3)->get();
        return view('admin.liability.modalTransaction' ,[
            'liabilities' => $Liabilities,
            'liabilityCategories' => LiabilitySubCategory::where('liability_category_id', 3)->where('status', 1)->get(),
            'liabilityTransactions' => LiabilityTransaction::all(),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function fixedliability()
    {
        if (auth()->user()->access->liability != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        $Liabilities = Liability::where('category_id', 4)->get();

        return view('admin.liability.fixedmodal' ,[
            'liabilities' => $Liabilities,
            'liabilityCategories' => LiabilitySubCategory::where('liability_category_id', 4)->where('status', 1)->get(),
            'liabilityTransactions' => LiabilityTransaction::all(),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function fixedliabilitytransaction()
    {
        if (auth()->user()->access->liability != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $Liabilities = Liability::where('category_id', 4)->get();
        return view('admin.liability.fixedmodalTransaction' ,[
            'liabilities' => $Liabilities,
            'liabilityCategories' => LiabilitySubCategory::where('liability_category_id', 4)->where('status', 1)->get(),
            'liabilityTransactions' => LiabilityTransaction::all(),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
        ]);
    }



    public function investmentmodal()
    {
        if (auth()->user()->access->investment != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        $investmentCategories = InvestmentCategory::where('status', 1)->get();

        return view('admin.investment.currentliability' ,[
            'investmentCategories' => $investmentCategories,
            'investmentSubCategories' => InvestmentSubCategory::where('status', 1)->get(),
            'investments' => Investment::all(),
            'investmentTransactions' => InvestmentTransaction::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function investmenttransaction()
    {
        if (auth()->user()->access->investment != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $investmentCategories = InvestmentCategory::where('status', 1)->get();
        return view('admin.investment.modalTransaction' ,[
            'investmentCategories' => $investmentCategories,
            'investmentSubCategories' => InvestmentSubCategory::where('status', 1)->get(),
            'investments' => Investment::all(),
            'investmentTransactions' => InvestmentTransaction::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function investmentincome()
    {
        if (auth()->user()->access->investment != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        $investmentCategories = InvestmentCategory::where('status', 1)->get();

        return view('admin.investment.fixedmodal' ,[
            'investmentCategories' => $investmentCategories,
            'investmentSubCategories' => InvestmentSubCategory::where('status', 1)->get(),
            'investments' => Investment::all(),
            'investmentTransactions' => InvestmentTransaction::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function investmentexpense()
    {
        if (auth()->user()->access->investment != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $investmentCategories = InvestmentCategory::where('status', 1)->get();
        return view('admin.investment.fixedmodalTransaction' ,[
            'investmentCategories' => $investmentCategories,
            'investmentSubCategories' => InvestmentSubCategory::where('status', 1)->get(),
            'investments' => Investment::all(),
            'investmentTransactions' => InvestmentTransaction::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function bankbook()
    {
        if (auth()->user()->access->bankbook != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $bankTransactions = BankTransaction::all();
        return view('admin.bank_accounts.Transactionmodal' ,[
            'banktransactions' => $bankTransactions,
            'bankaccounts' => BankAccount::all(),
            'banks' => BankAccount::all(),
        ]);
    }

}
