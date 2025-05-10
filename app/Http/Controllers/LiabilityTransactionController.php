<?php

namespace App\Http\Controllers;

use App\Models\Liability;
use App\Models\LiabilityTransaction;
use Illuminate\Http\Request;

class LiabilityTransactionController extends Controller
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
        if (auth()->user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create liability transactions.');
        }
        $request->validate([
            'liability_id' => 'required|exists:assets,id',
            'amount' => 'required',
            'transaction_type' => 'required',
            'transaction_date' => 'required|date',
        ]);

        // Create a new asset sub-subcategory
        $assetTransaction = LiabilityTransaction::create($request->all());

        // Update the asset amount based on the transaction type
        $asset = Liability::findOrFail($request->liability_id);

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
        
        // Save previous values before update
        $previousAmount = $assetTransaction->amount;
        $previousType = $assetTransaction->transaction_type;

        // Update the transaction record
        $assetTransaction->update($request->all());

        // Fetch the associated asset
        $asset = Liability::findOrFail($request->asset_id);

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

        // Get the associated asset
        $asset = Liability::findOrFail($assetTransaction->asset_id);

        // Reverse the transaction effect on the asset amount
        if ($assetTransaction->transaction_type === 'Deposit') {
            $asset->amount -= $assetTransaction->amount;
        } elseif ($assetTransaction->transaction_type === 'Withdraw') {
            $asset->amount += $assetTransaction->amount;
        }

        // Save the updated asset
        $asset->save();

        // Now delete the transaction
        $assetTransaction->delete();

        // Return back with success
        return back()->with('success', 'Liability Transaction deleted successfully!');
    }
}
