<?php

namespace App\Http\Controllers;

use App\Models\AssetTransaction;
use Illuminate\Http\Request;
use App\Models\Asset;

class AssetTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        // Check if the user has permission to view asset transactions
        if (auth()->user()->access->asset == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view asset transactions.');
        }

        // Fetch the asset by slug
        $asset = Asset::where('slug', $slug)->firstOrFail();

        // Fetch all transactions related to the asset
        $transactions = AssetTransaction::where('asset_id', $asset->id)->get();

        // Return the view with the asset and its transactions
        return view('admin.asset.transactions', compact('asset', 'transactions'));
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
        // Check if the user has permission to create asset transactions
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create asset transactions.');
        }
        // Validate the request data
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'amount' => 'required',
            'transaction_type' => 'required',
            'transaction_date' => 'required|date',
        ]);

        // Create a new asset sub-subcategory
        $assetTransaction = AssetTransaction::create($request->all());

        // Update the asset amount based on the transaction type
        $asset = Asset::findOrFail($request->asset_id);

        if ($request->transaction_type === 'Deposit') {
            $asset->amount += $request->amount;
        } elseif ($request->transaction_type === 'Withdraw') {
            $asset->amount -= $request->amount;
        }

        $asset->save();

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Asset Transaction created successfully!',
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
        // Check if the user has permission to update asset transactions
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update asset transactions.');
        }
        // Validate the request data
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'amount' => 'required|numeric|min:0',
            'transaction_type' => 'required|in:Deposit,Withdraw',
            'transaction_date' => 'required|date',
        ]);

        // Find the existing asset transaction
        $assetTransaction = AssetTransaction::findOrFail($id);
        
        // Save previous values before update
        $previousAmount = $assetTransaction->amount;
        $previousType = $assetTransaction->transaction_type;

        // Update the transaction record
        $assetTransaction->update($request->all());

        // Fetch the associated asset
        $asset = Asset::findOrFail($request->asset_id);

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
            'message' => 'Asset Transaction updated successfully!',
            'id' => $assetTransaction->id,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the user has permission to delete asset transactions
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete asset transactions.');
        }
        // Find the asset transaction
        $assetTransaction = AssetTransaction::findOrFail($id);

        // Get the associated asset
        $asset = Asset::findOrFail($assetTransaction->asset_id);

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
        return back()->with('success', 'Asset Transaction deleted successfully!');
    }

}
