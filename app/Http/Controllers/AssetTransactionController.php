<?php

namespace App\Http\Controllers;

use App\Models\AssetTransaction;
use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        

        // Update the asset amount based on the transaction type
        $asset = Asset::findOrFail($request->asset_id);

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
        $assetTransaction = AssetTransaction::create($request->all());

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
        // Check permission
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update asset transactions.');
        }

        // Validate input
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:Deposit,Withdraw',
            'transaction_date' => 'required|date',
        ]);

        // Get the transaction and asset
        $assetTransaction = AssetTransaction::findOrFail($id);
        $asset = Asset::findOrFail($request->asset_id);

        // Reverse previous transaction effect
        if ($assetTransaction->transaction_type === 'Deposit') {
            $asset->amount -= $assetTransaction->amount;
        } elseif ($assetTransaction->transaction_type === 'Withdraw') {
            $asset->amount += $assetTransaction->amount;
        }

        // 🔒 Check for over-withdrawal BEFORE updating transaction
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

        // Apply new transaction values
        $assetTransaction->update($request->all());

        if ($request->transaction_type === 'Deposit') {
            $asset->amount += $request->amount;
        } elseif ($request->transaction_type === 'Withdraw') {
            $asset->amount -= $request->amount;
        }

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

        // Now delete the transaction
        $assetTransaction->delete();

        // Return back with success
        return back()->with('success', 'Asset Transaction deleted successfully!');
    }

}
