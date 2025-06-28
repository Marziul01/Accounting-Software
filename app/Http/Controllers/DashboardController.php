<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\BankTransaction;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Liability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if ( !Auth::check() ) {
            return redirect()->route('login');
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


        //Yearly Compare Chart
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        $monthlyComparisonData  = [
            'income'      => $this->getComparisonData(Income::class, $currentYear, $previousYear, 'date'),
            'expenses'    => $this->getComparisonData(Expense::class, $currentYear, $previousYear, 'date'),
            'assets'      => $this->getComparisonData(Asset::class, $currentYear, $previousYear, 'entry_date'),
            'liabilities' => $this->getComparisonData(Liability::class, $currentYear, $previousYear, 'entry_date'),
        ];


        return view('admin.dashboard.dashboard',[
            'monthlyData' => $monthlyData,
            'latestBankTransactions' => $latestBankTransactions,
            'monthlyComparisonData' => $monthlyComparisonData ,
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

}
