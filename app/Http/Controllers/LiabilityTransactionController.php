<?php

namespace App\Http\Controllers;

use App\Models\Liability;
use App\Models\LiabilityTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LiabilityTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        // Check if the user has permission to view liability transactions
        if (auth()->user()->access->liability == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view liability transactions.');
        }

        // Fetch the liability by slug
        $liability = Liability::where('slug', $slug)->firstOrFail();

        // Fetch all transactions related to the liability
        $transactions = LiabilityTransaction::where('liability_id', $liability->id)->orderBy('transaction_date' , 'asc' )->get();

        // Return the view with the liability and its transactions
        return view('admin.liability.transactions', compact('liability', 'transactions'));
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
        if (auth()->user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create liability transactions.');
        }
        
        // Validate the request data
        $request->validate([
            'liability_id' => 'required|exists:liabilities,id',
            'amount' => 'required',
            'transaction_type' => 'required',
            'transaction_date' => 'required|date',
        ]);

        $asset = Liability::findOrFail($request->liability_id);

        if ($request->transaction_type === 'Withdraw') {
            // Calculate total deposits and withdrawals
            $totalDeposit = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount');
            $totalWithdraw = $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');

            $currentBalance = $totalDeposit - $totalWithdraw;

            if ($request->amount > $currentBalance) {
                throw ValidationException::withMessages([
                    'amount' => ['Withdrawal amount exceeds current available balance of ' . number_format($currentBalance, 2)],
                ]);
            }
        }

        // Create a new asset sub-subcategory
        $assetTransaction = LiabilityTransaction::create($request->all());

        // Update the asset amount based on the transaction type
        

        if ($request->transaction_type === 'Deposit') {
            $asset->amount += $request->amount;
        } elseif ($request->transaction_type === 'Withdraw') {
            $asset->amount -= $request->amount;
        }

        $asset->save();

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability Transaction created successfully!',
            'id' => $assetTransaction->id,
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
        if (auth()->user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update liability transactions.');
        }
        $request->validate([
            'liability_id' => 'required|exists:liabilities,id',
            'amount' => 'required|numeric|min:0',
            'transaction_type' => 'required|in:Deposit,Withdraw',
            'transaction_date' => 'required|date',
        ]);

        // Find the existing asset transaction
        $assetTransaction = LiabilityTransaction::findOrFail($id);
        $asset = Liability::findOrFail($request->liability_id);

        if ($request->transaction_type === 'Withdraw') {
            $totalDeposit = $asset->transactions()->where('id', '!=', $assetTransaction->id)->where('transaction_type', 'Deposit')->sum('amount');
            $totalWithdraw = $asset->transactions()->where('id', '!=', $assetTransaction->id)->where('transaction_type', 'Withdraw')->sum('amount');
            $currentBalance = $totalDeposit - $totalWithdraw;

            if ($request->amount > $currentBalance) {
                throw ValidationException::withMessages([
                    'amount' => ['Withdrawal amount exceeds current available balance of ' . number_format($currentBalance, 2)],
                ]);
            }
        }
        
        // Save previous values before update
        $previousAmount = $assetTransaction->amount;
        $previousType = $assetTransaction->transaction_type;

        // Update the transaction record
        $assetTransaction->update($request->all());

        // Fetch the associated asset
        

        // Reverse the previous transaction
        if ($previousType === 'Deposit') {
            $asset->amount -= $previousAmount;
        } elseif ($previousType === 'Withdraw') {
            $asset->amount += $previousAmount;
        }

        // Apply the new transaction
        $newAmount = $request->amount;
        $newType = $request->transaction_type;

        if ($newType === 'Deposit') {
            $asset->amount += $newAmount;
        } elseif ($newType === 'Withdraw') {
            $asset->amount -= $newAmount;
        }

        // Save the updated asset amount
        $asset->save();

        return response()->json([
            'message' => 'Liability Transaction updated successfully!',
            'id' => $assetTransaction->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the user has permission to delete liability transactions
        if (auth()->user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete liability transactions.');
        }
        $assetTransaction = LiabilityTransaction::findOrFail($id);

        // Now delete the transaction
        $assetTransaction->delete();

        // Return back with success
        return back()->with('success', 'Liability Transaction deleted successfully!');
    }
}
