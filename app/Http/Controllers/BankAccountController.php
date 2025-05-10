<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if the user has permission to access this page
        if (auth()->user()->access->bankbook == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all bank accounts from the database
        $bankAccounts = BankAccount::all();

        // Return the view with the bank accounts data
        return view('admin.bank_accounts.index',[
            'bankbooks' => $bankAccounts,
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
        // Check if the user has permission to create bank accounts
        if (auth()->user()->access->bankbook != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create bank accounts.');
        }
        // Validate the request data
        $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:bank_accounts,account_number',
            'bank_name' => 'required|string|max:255',
            'account_type' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'nominee_name' => 'nullable|string|max:255',
        ]);

        // Create a new bank account
        BankAccount::create($request->all());

        // Redirect back with success message
        return response()->json(['success' => true, 'message' => 'Bank account created successfully.']);
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
        // Check if the user has permission to update bank accounts
        if (auth()->user()->access->bankbook != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update bank accounts.');
        }
        // Validate the request data
        $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:bank_accounts,account_number,' . $id,
            'bank_name' => 'required|string|max:255',
            'account_type' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'nominee_name' => 'nullable|string|max:255',
        ]);

        // Find the bank account by ID and update it
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->update($request->all());

        // Redirect back with success message
        return response()->json([
            'success' => true, 'message' => 'Bank account updated successfully.',
            'id' => $bankAccount->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if the user has permission to delete bank accounts
        if (auth()->user()->access->bankbook != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete bank accounts.');
        }
        // Find the bank account by ID and delete it
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->delete();

        // Redirect back with success message
        return back()->with('success', 'Bank account deleted successfully.');
    }
}
