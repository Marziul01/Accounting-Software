<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\BankTransaction;

class BankTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if the user has permission to access this page
        if (auth()->user()->access->bankbook == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all bank transactions from the database
        $bankTransactions = BankTransaction::all();

        if ($request->ajax()) {
            $transactions = BankTransaction::with('bankAccount')->orderByDesc('transaction_date');

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('bank_name', function ($row) {
                    return $row->bankAccount->bank_name ?? 'Bank Deleted';
                })
                ->addColumn('transaction_type', function ($row) {
                    if ($row->transaction_type == 'credit') {
                        return '<span class="badge bg-label-success">জমা</span>';
                    } elseif ($row->transaction_type == 'debit') {
                        return '<span class="badge bg-label-danger">উত্তোলন</span>';
                    }
                    return '<span class="badge bg-label-secondary">N/A</span>';
                })
                ->addColumn('action', function ($row) {
                        return view('admin.bank_accounts._actions', compact('row'))->render();
                    })
                ->rawColumns(['transaction_type', 'action'])
                ->make(true);
        }
        // Return the view with the bank transactions data
        return view('admin.bank_accounts.transactions', [
            'banktransactions' => $bankTransactions,
            'bankaccounts' => BankAccount::all(),
        ]);
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
        // Check if the user has permission to create bank transactions
        if (auth()->user()->access->bankbook != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create bank transactions.');
        }
        // Validate the request data
        $request->validate([
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_type' => 'required|in:credit,debit',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (BankTransaction::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);

        // Create a new bank transaction
        BankTransaction::create($request->all());

        // Redirect back with success message
        return response()->json([
            'success' => true,
             'message' => 'Bank transaction created successfully.',
            'id' => BankTransaction::latest()->first()->id,
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
        // Check if the user has permission to update bank transactions
        if (auth()->user()->access->bankbook != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update bank transactions.');
        }
        // Validate the request data
        $request->validate([
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_type' => 'required|in:credit,debit',
            'name' => 'required|string|max:255',

            'slug' => 'required|string|max:255',
        ]);

        $bankTransaction = BankTransaction::findOrFail($id);

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Only regenerate slug if it's already used by a different record
        while (
            BankTransaction::where('slug', $slug)->where('id', '!=', $bankTransaction->id)->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        // Merge the slug into the request data
        $request->merge(['slug' => $data['slug']]);
        // Find the bank transaction by ID and update it
        $bankTransaction = BankTransaction::findOrFail($id);
        $bankTransaction->update($request->all());

        // Redirect back with success message
        return response()->json([
            'success' => true, 
            'message' => 'Bank transaction updated successfully.',
            'id' => $bankTransaction->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the user has permission to delete bank transactions
        if (auth()->user()->access->bankbook != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete bank transactions.');
        }
        // Find the bank transaction by ID
        $bankTransaction = BankTransaction::findOrFail($id);

        // Delete the bank transaction
        $bankTransaction->delete();

        // Redirect back with success message
        return back()->with('success', 'Bank transaction deleted successfully.');
    }
}
