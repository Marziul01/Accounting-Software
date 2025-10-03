<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense; 
use App\Models\AssetTransaction;
use App\Models\LiabilityTransaction;
use App\Models\InvestmentTransaction;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Models\Asset;
use App\Models\AssetSubCategory;
use App\Models\Liability;
use App\Models\LiabilitySubCategory;
use App\Models\Investment;
use App\Models\InvestmentCategory;
use App\Models\InvestmentSubCategory;
use App\Models\Contact;
use App\Models\BankAccount;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Carbon\Carbon;


class TransactionHistory extends Controller
{
    public function index( Request $request)
    {

        $incomes = Income::select('id', 'amount', 'date', 'description', 'name', 'created_at','updated_at')
            ->get()
            ->map(function ($item) {
                // ✅ choose updated_at if present, otherwise created_at
                $timeSource = $item->updated_at ?? $item->created_at;

                // ✅ build full datetime using "date" for the day and time part from updated_at/created_at
                $datePart = \Carbon\Carbon::parse($item->date)->toDateString();
                $timePart = \Carbon\Carbon::parse($timeSource)->toTimeString();

                $sortDatetime = \Carbon\Carbon::parse("$datePart $timePart");
                return (object)[
                    'type' => 'Income',
                    'name' => $item->name ?? 'N/A',
                    'amount' => $item->amount,
                    'date' => $item->date,
                    'description' => $item->description,
                    'transaction_type' => null,
                    'other' => $item,
                    'sortDatetime' => $sortDatetime,
                ];
            });


        $expenses = Expense::select('id', 'amount', 'date', 'description', 'name', 'created_at','updated_at')
            ->get()
            ->map(function ($item) {
                // ✅ choose updated_at if present, otherwise created_at
                $timeSource = $item->updated_at ?? $item->created_at;

                // ✅ build full datetime using "date" for the day and time part from updated_at/created_at
                $datePart = \Carbon\Carbon::parse($item->date)->toDateString();
                $timePart = \Carbon\Carbon::parse($timeSource)->toTimeString();

                $sortDatetime = \Carbon\Carbon::parse("$datePart $timePart");
                return (object)[
                    'type' => 'Expense',
                    'name' => $item->name ?? 'N/A',
                    'amount' => $item->amount,
                    'date' => $item->date,
                    'description' => $item->description,
                    'transaction_type' => null,
                    'other' => $item,
                    'sortDatetime' => $sortDatetime,
                ];
            });

        $assets = AssetTransaction::with('asset')
            ->select('id', 'amount', 'transaction_date as date', 'description', 'transaction_type', 'asset_id', 'created_at','updated_at')
            ->get()
            ->map(function ($item) {
                // ✅ choose updated_at if present, otherwise created_at
                $timeSource = $item->updated_at ?? $item->created_at;

                // ✅ build full datetime using "date" for the day and time part from updated_at/created_at
                $datePart = \Carbon\Carbon::parse($item->date)->toDateString();
                $timePart = \Carbon\Carbon::parse($timeSource)->toTimeString();

                $sortDatetime = \Carbon\Carbon::parse("$datePart $timePart");
                return (object)[
                    'type' => 'Asset',
                    'amount' => $item->amount,
                    'name' => $item->asset?->name ?? 'N/A',
                    'transaction_type' => $item->transaction_type,
                    'date' => $item->date,
                    'description' => $item->description,
                    'other' => $item,
                    'sortDatetime' => $sortDatetime,
                ];
            });


        $liabilities = LiabilityTransaction::with('liability')
            ->select('id', 'amount', 'transaction_date as date', 'description', 'transaction_type', 'liability_id', 'created_at','updated_at')
            ->get()
            ->map(function ($item) {
                // ✅ choose updated_at if present, otherwise created_at
                $timeSource = $item->updated_at ?? $item->created_at;

                // ✅ build full datetime using "date" for the day and time part from updated_at/created_at
                $datePart = \Carbon\Carbon::parse($item->date)->toDateString();
                $timePart = \Carbon\Carbon::parse($timeSource)->toTimeString();

                $sortDatetime = \Carbon\Carbon::parse("$datePart $timePart");
                return (object)[
                    'type' => 'Liability',
                    'amount' => $item->amount,
                    'name' => $item->liability?->name ?? 'N/A',
                    'transaction_type' => $item->transaction_type,
                    'date' => $item->date,
                    'description' => $item->description,
                    'other' => $item,
                    'sortDatetime' => $sortDatetime,
                ];
            });

        $investments = InvestmentTransaction::with('investment')
            ->select('id', 'amount', 'transaction_date as date', 'description', 'transaction_type', 'investment_id', 'created_at','updated_at')
            ->get()
            ->map(function ($item) {
                // ✅ choose updated_at if present, otherwise created_at
                $timeSource = $item->updated_at ?? $item->created_at;

                // ✅ build full datetime using "date" for the day and time part from updated_at/created_at
                $datePart = \Carbon\Carbon::parse($item->date)->toDateString();
                $timePart = \Carbon\Carbon::parse($timeSource)->toTimeString();

                $sortDatetime = \Carbon\Carbon::parse("$datePart $timePart");
                return (object)[
                    'type' => 'Investment',
                    'amount' => $item->amount,
                    'name' => $item->investment?->name ?? 'N/A',
                    'transaction_type' => $item->transaction_type,
                    'date' => $item->date,
                    'description' => $item->description,
                    'other' => $item,
                    'sortDatetime' => $sortDatetime,
                ];
            });

        $bankTransactions = BankTransaction::with('bankAccount')
            ->select('id', 'amount', 'transaction_date as date', 'description', 'transaction_type', 'bank_account_id' ,'name', 'from', 'from_id','transfer_from', 'created_at','updated_at')
            ->get()
            ->whereNull('transfer_from')
            ->map(function ($item) {
                // ✅ choose updated_at if present, otherwise created_at
                $timeSource = $item->updated_at ?? $item->created_at;

                // ✅ build full datetime using "date" for the day and time part from updated_at/created_at
                $datePart = \Carbon\Carbon::parse($item->date)->toDateString();
                $timePart = \Carbon\Carbon::parse($timeSource)->toTimeString();

                $sortDatetime = \Carbon\Carbon::parse("$datePart $timePart");
                return (object)[
                    'type' => 'BankTransaction',
                    'amount' => $item->amount,
                    'name' => $item->name,
                    'transaction_type' => $item->transaction_type,
                    'date' => $item->date,
                    'description' => $item->description,
                    'other' => $item,
                    'from' => $item->from,
                    'from_id' => $item->from_id,
                    'sortDatetime' => $sortDatetime,
                ];
            });
        // Merge all collections
        $merged = collect()
            ->merge($incomes)
            ->merge($expenses)
            ->merge($assets)
            ->merge($liabilities)
            ->merge($investments)
            ->merge($bankTransactions)
            ->sortByDesc(function ($item) {
                // Use updated_at if available, else created_at
                $time = $item->other->updated_at ?? $item->other->created_at;

                // Build a datetime: date (Y-m-d) + time (H:i:s)
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d') . ' ' .
                    \Carbon\Carbon::parse($time)->format('H:i:s');
            })
            ->values();

        $trsn = $merged; // your merged transaction collection

        // 1. Filter by transaction type if provided
        if ($request->filled('type')) {
            $trsn = $trsn->where('type', $request->type);
        }

        // 2. Date filtering
        $startDate = $request->filled('startDate') ? Carbon::parse($request->startDate)->startOfDay() : null;
        $endDate   = $request->filled('endDate') ? Carbon::parse($request->endDate)->endOfDay() : null;

        $period = $request->filled('period') ? $request->period : null;

        if ($startDate && $endDate) {
            // Priority: filter by date range
            $trsn = $trsn->filter(function($row) use ($startDate, $endDate) {
                $date = Carbon::parse($row->date);
                return $date->between($startDate, $endDate);
            });
        } else {
            // Use quick period filter, default to current month
            $period = $period ?? 'month';
            $now = Carbon::now();

            $trsn = $trsn->filter(function($row) use ($period, $now) {
                $date = Carbon::parse($row->date);

                if ($period === 'today') {
                    return $date->isSameDay($now);
                } elseif ($period === 'month') {
                    return $date->isSameMonth($now);
                } elseif ($period === 'year') {
                    return $date->isSameYear($now);
                }
                return true;
            });
        }

        // Return DataTables for AJAX requests
        if ($request->ajax()) {
            return DataTables::of($trsn)
                ->addIndexColumn()
                ->editColumn('type', fn($row) => $row->type ?? 'Not Assigned')
                ->editColumn('name', fn($row) => $row->name ?? 'Not Assigned')
                ->editColumn('transaction_type', fn($row) => $row->transaction_type ?? 'Not Assigned')
                ->editColumn('amount', fn($row) => $row->amount ?? 'Not Assigned')
                ->editColumn('date', fn($row) => Carbon::parse($row->date)->format('d M, Y'))
                ->editColumn('description', fn($row) => $row->description ?? 'Not Assigned')
                ->addColumn('action', fn($row) => view('admin.transaction._actions', compact('row'))->render())
                ->rawColumns(['action'])
                ->make(true);
        }

        // Determine active filter button for front-end
        $today = Carbon::now()->format('Y-m-d');
        $firstDayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $lastDayOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $firstDayOfYear = Carbon::now()->startOfYear()->format('Y-m-d');
        $lastDayOfYear = Carbon::now()->endOfYear()->format('Y-m-d');

        $activeFilter = 'month'; // default to month
        $startDateVal = $request->startDate ?? null;
        $endDateVal = $request->endDate ?? null;

        if ($startDateVal && $endDateVal) {
            if ($startDateVal === $today && $endDateVal === $today) {
                $activeFilter = 'today';
            } elseif ($startDateVal === $firstDayOfMonth && $endDateVal === $lastDayOfMonth) {
                $activeFilter = 'month';
            } elseif ($startDateVal === $firstDayOfYear && $endDateVal === $lastDayOfYear) {
                $activeFilter = 'year';
            } else {
                $activeFilter = 'date';
            }
        } elseif ($period) {
            $activeFilter = $period;
        }

        return view('admin.transaction.history', [
            'transactions' => $merged,
            'activeFilter' => $activeFilter,
            'startDate' => $startDateVal,
            'endDate' => $endDateVal,
        ]);

    }

    public function editincome($id)
    {
        if (auth()->user()->access->income != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        return view('admin.income.editmodal' ,[
            'incomeCategories' => IncomeCategory::where('status', 1)->where('id', '!=', 13)->get(),
            'banks' => BankAccount::all(),
            'income' => Income::find($id),
            'bankIncomeTransactions' => BankTransaction::where('from', 'Income')->where('from_id', $id)->first(),
        ]);
    }

    public function editexpense($id)
    {
        if (auth()->user()->access->expense != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        return view('admin.expense.editmodal' ,[
            'expenseCategories' => ExpenseCategory::where('status', 1)->where('id', '!=', 7)->get(),
            'banks' => BankAccount::all(),
            'expense' => Expense::find($id),
            'bankIncomeTransactions' => BankTransaction::where('from', 'Expense')->where('from_id', $id)->first(),
        ]);
    }

    public function editassettransaction($id)
    {
        if (auth()->user()->access->asset != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $assets = Asset::where('category_id', 4)->get();
        return view('admin.asset.editmodalTransaction' ,[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 4)->where('status', 1)->get(),
            'assetTransaction' => AssetTransaction::find($id),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
            'currentTransaction' => BankTransaction::where('from', 'Asset')->where('from_id', $id)->first(),
        ]);
    }

    public function editliabilitytransaction($id)
    {
        if (auth()->user()->access->liability != 2) {
            return back()->with('error' , 'You are authorized !');
        }

        $Liabilities = Liability::where('category_id', 3)->get();

        return view('admin.liability.editliability' ,[
            'liabilities' => $Liabilities,
            'liabilityCategories' => LiabilitySubCategory::where('liability_category_id', 3)->where('status', 1)->get(),
            'liabilityTransaction' => LiabilityTransaction::find($id),
            'users' => Contact::all(),
            'banks' => BankAccount::all(),
            'currentTransaction' => BankTransaction::where('from', 'Liability')->where('from_id', $id)->first(),
        ]);
    }

    public function editinvestmenttransaction($id)
    {
        if (auth()->user()->access->investment != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $investmentCategories = InvestmentCategory::where('status', 1)->get();
        return view('admin.investment.editmodalTransaction' ,[
            'investmentCategories' => $investmentCategories,
            'investmentSubCategories' => InvestmentSubCategory::where('status', 1)->get(),
            'investments' => Investment::all(),
            'investmentTransaction' => InvestmentTransaction::find($id),
            'banks' => BankAccount::all(),
            'currentTransaction' => BankTransaction::where('from', 'investment')->where('from_id', $id)->first(),
        ]);
    }

    public function editbanktransaction($id)
    {
        if (auth()->user()->access->bankbook != 2) {
            return back()->with('error' , 'You are authorized !');
        }
        $bankTransactions = BankTransaction::find($id);
        return view('admin.bank_accounts.editTransactionmodal' ,[
            'banktransactions' => $bankTransactions,
            'bankaccounts' => BankAccount::all(),
            'banks' => BankAccount::all(),
        ]);
    }

    public function assetinvoice($id)
    {
        // Fetch the transaction
        $transaction = AssetTransaction::with('asset')->findOrFail($id);
        $asset = $transaction->asset;

        // Example calculation (adjust with your logic)
        $totalAmountBn = $transaction->amount;
        $previousAmountBn = 0;
        $requestASmount = $transaction->amount;

        // Render Blade view into HTML
        $html = view('pdf.asset_deposit_invoice', [
            'asset' => $transaction->asset,
            'request' => $transaction,
            'totalAmount' => $totalAmountBn,
            'previousAmount' => $previousAmountBn,
            'requestASmount' => $requestASmount,
        ])->render();

        // mPDF config
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $customFontDir = storage_path('fonts');

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'fontDir' => array_merge($fontDirs, [$customFontDir]),
            'fontdata' => $fontData + [
                'solaimanlipi' => [
                    'R' => 'SolaimanLipi.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'solaimanlipi',
            'tempDir' => storage_path('app/tmp'),
        ]);

        $mpdf->WriteHTML($html);

        // Open directly in new tab
        return response($mpdf->Output('', 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function liabilityinvoice($id)
    {
        // Fetch the transaction
        $transaction = LiabilityTransaction::with('liability')->findOrFail($id);
        $liability = $transaction->liability;

        // Example calculation (adjust with your logic)
        $totalAmountBn = $transaction->amount;
        $previousAmountBn = 0;
        $requestASmount = $transaction->amount;

        // Render Blade view into HTML
        $html = view('pdf.liability_deposit_invoice', [
            'liability' => $transaction->liability,
            'request' => $transaction,
            'totalAmount' => $totalAmountBn,
            'previousAmount' => $previousAmountBn,
            'requestASmount' => $requestASmount,
        ])->render();

        // mPDF config
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $customFontDir = storage_path('fonts');

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'fontDir' => array_merge($fontDirs, [$customFontDir]),
            'fontdata' => $fontData + [
                'solaimanlipi' => [
                    'R' => 'SolaimanLipi.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'solaimanlipi',
            'tempDir' => storage_path('app/tmp'),
        ]);

        $mpdf->WriteHTML($html);

        // Open directly in new tab
        return response($mpdf->Output('', 'I'))
            ->header('Content-Type', 'application/pdf');
    }
}
