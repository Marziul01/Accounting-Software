<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvestmentTransactionController extends Controller
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

        if ($request->transaction_type === 'Deposit') {
            $investment->amount += $request->amount;
        } elseif ($request->transaction_type === 'Withdraw') {
            $investment->amount -= $request->amount;
        }

        $investment->save();

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

        // Save the updated asset amount
        $investment->save();

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

        // Get the associated asset
        $investment = Investment::findOrFail($investmentTransaction->asset_id);

        // Reverse the transaction effect on the asset amount
        if ($investmentTransaction->transaction_type === 'Deposit') {
            $investment->amount -= $investmentTransaction->amount;
        } elseif ($investmentTransaction->transaction_type === 'Withdraw') {
            $investment->amount += $investmentTransaction->amount;
        }

        // Save the updated asset
        $investment->save();

        // Now delete the transaction
        $investmentTransaction->delete();

        // Return back with success
        return back()->with('success', 'Investment Transaction deleted successfully!');
    }
}
