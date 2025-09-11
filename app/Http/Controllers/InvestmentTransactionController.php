<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvestmentTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        // Check if the user has permission to view investment transactions
        if (auth()->user()->access->investment == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view investment transactions.');
        }

        // Fetch the investment by slug
        $investment = Investment::where('slug', $slug)->firstOrFail();

        // Fetch all transactions related to this investment
        $transactions = InvestmentTransaction::where('investment_id', $investment->id)->orderBy('transaction_date' , 'asc' )->get();
        $banks = BankAccount::all();
        $bankTransaction = BankTransaction::where('from', 'investment')->get();
        return view('admin.investment.transactions', compact('investment', 'transactions','banks','bankTransaction'));
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
        // Check if the user has permission to create liability transactions
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create investment transactions.');
        }
        $request->validate([
            'investment_id' => 'required|exists:investments,id',
            'amount' => 'required',
            'transaction_type' => 'required',
            'transaction_date' => 'required|date',
        ]);

        // Create a new asset sub-subcategory
        $investmentTransaction = InvestmentTransaction::create($request->all());

        // Update the asset amount based on the transaction type
        $investment = Investment::findOrFail($request->investment_id);

        if($request->transaction_type === 'Deposit'){
            if ($request->has('bank_account_id') && $request->bank_account_id) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                $balance = $bankAccount->transactions()->where('transaction_type', 'credit')->sum('amount')
                        - $bankAccount->transactions()->where('transaction_type', 'debit')->sum('amount');

                if ($bankAccount && $request->amount > $balance) {
                    return response()->json([
                        'errors' => [
                            'amount' => ['Investment amount cannot be greater than the bank balance.']
                        ]
                    ], 422);
                }
            }
        }

        if ($request->transaction_type === 'Deposit') {
            $investment->amount += $request->amount;
        } elseif ($request->transaction_type === 'Withdraw') {
            $investment->amount -= $request->amount;
        }

        $investment->save();

        if($request->has('bank_account_id') && $request->bank_account_id) {
            $bankAccount = BankAccount::find($request->bank_account_id);
            if ($bankAccount) {
                $bankTransaction = new BankTransaction();
                $bankTransaction->bank_account_id = $bankAccount->id;
                $bankTransaction->transaction_date = $request->transaction_date;
                $bankTransaction->amount = $request->amount;

                if($request->transaction_type === 'Deposit'){
                    $bankTransaction->transaction_type = 'debit';
                }else{
                    $bankTransaction->transaction_type = 'credit';
                }

                $bankTransaction->description = $request->bank_description;
                $bankTransaction->name = 'Investment: '.$investment->name;
                $bankTransaction->slug = 'investment-' . $investment->slug;
                $bankTransaction->from = 'investment';
                $bankTransaction->from_id = $investmentTransaction->id;
                $bankTransaction->save();
            }
        }

        Notification::create([
            'message' => Auth()->user()->name . ' created a new ' . $request->transaction_type . ' Transaction investment: ' . $investment->name .'('. $request->amount .' BDT)' .'.',
            'sent_date' => now(),
        ]);

        return response()->json([
            'message' => 'Investment Transaction created successfully!',
            'id' => $investmentTransaction->id,
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
        // Check if the user has permission to update liability transactions
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update investment transactions.');
        }
        $request->validate([
            'investment_id' => 'required|exists:investments,id',
            'amount' => 'required|numeric|min:0',
            'transaction_type' => 'required|in:Deposit,Withdraw',
            'transaction_date' => 'required|date',
        ]);

        if($request->transaction_type === 'Deposit'){
            if ($request->has('bank_account_id') && $request->bank_account_id) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                $currentTransaction = BankTransaction::where('from', 'investment')->where('from_id', $id)->first();
                $balance = $bankAccount->transactions()->where('transaction_type', 'credit')->sum('amount')
                        - $bankAccount->transactions()->where('transaction_type', 'debit')->sum('amount') + ($currentTransaction ? $currentTransaction->amount : 0);

                if ($bankAccount && $request->amount > $balance) {
                    return response()->json([
                        'errors' => [
                            'amount' => ['Investment amount cannot be greater than the bank balance.']
                        ]
                    ], 422);
                }
            }
        }

        // Find the existing asset transaction
        $investmentTransaction = InvestmentTransaction::findOrFail($id);
        
        // Save previous values before update
        $previousAmount = $investmentTransaction->amount;
        $previousType = $investmentTransaction->transaction_type;

        // Update the transaction record
        $investmentTransaction->update($request->all());

        // Fetch the associated asset
        $investment = Investment::findOrFail($request->investment_id);

        // Reverse the previous transaction
        if ($previousType === 'Deposit') {
            $investment->amount -= $previousAmount;
        } elseif ($previousType === 'Withdraw') {
            $investment->amount += $previousAmount;
        }

        // Apply the new transaction
        $newAmount = $request->amount;
        $newType = $request->transaction_type;

        if ($newType === 'Deposit') {
            $investment->amount += $newAmount;
        } elseif ($newType === 'Withdraw') {
            $investment->amount -= $newAmount;
        }

        $investment->save();

        if($request->has('bank_account_id') && $request->bank_account_id) {
            $bankAccount = BankAccount::find($request->bank_account_id);
            if ($bankAccount) {
                $bankTransaction = BankTransaction::where('from', 'investment')->where('from_id', $id)->first();
                if (!$bankTransaction) {
                    $bankTransaction = new BankTransaction();
                    $bankTransaction->bank_account_id = $bankAccount->id;
                    $bankTransaction->from = 'investment';
                    $bankTransaction->from_id = $investmentTransaction->id;
                    $bankTransaction->name = 'Investment: '.$investment->name; 
                    $bankTransaction->slug = 'investment-' . $investment->slug;
                } else {
                    // If bank account changed, update it
                    if ($bankTransaction->bank_account_id != $bankAccount->id) {
                        $bankTransaction->bank_account_id = $bankAccount->id;
                    }
                }
                $bankTransaction->transaction_date = $request->transaction_date;
                $bankTransaction->amount = $request->amount;
                if($request->transaction_type === 'Deposit'){
                    $bankTransaction->transaction_type = 'debit';
                }else{
                    $bankTransaction->transaction_type = 'credit';
                }
                $bankTransaction->description = $request->bank_description;
                $bankTransaction->save();
            }
        } else {
            $bankTransaction = BankTransaction::where('from', 'investment')->where('from_id', $id)->first();
            if ($bankTransaction) {
                $bankTransaction->delete();
            }
        }

        Notification::create([
            'message' => Auth()->user()->name . ' updated a ' . $request->transaction_type . ' Transaction investment: ' . $investment->name .'('. $request->amount .' BDT)' .'.',
            'sent_date' => now(),
        ]);

        return response()->json([
            'message' => 'Investment Transaction updated successfully!',
            'id' => $investmentTransaction->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the user has permission to delete liability transactions
        if (auth()->user()->access->investment != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete investment transactions.');
        }
        $investmentTransaction = InvestmentTransaction::findOrFail($id);

        // Fetch the associated investment
        $investment = Investment::findOrFail($investmentTransaction->investment_id);    
        // Reverse the transaction effect on investment amount
        if ($investmentTransaction->transaction_type === 'Deposit') {
            $investment->amount -= $investmentTransaction->amount;
        } elseif ($investmentTransaction->transaction_type === 'Withdraw') {
            $investment->amount += $investmentTransaction->amount;
        }
        $investment->save();    

        $bankTransaction = BankTransaction::where('from', 'investment')->where('from_id', $id)->first();
        if ($bankTransaction) {
            $bankTransaction->delete();
        }

        $investmentTransaction->delete();

        Notification::create([
            'message' => Auth()->user()->name . ' deleted a ' . $investmentTransaction->transaction_type . ' Transaction investment: ' . $investment->name .'('. $investmentTransaction->amount .' BDT)' .'.',
            'sent_date' => now(),
        ]); 

        // Return back with success
        return back()->with('success', 'Investment Transaction deleted successfully!');
    }
}
