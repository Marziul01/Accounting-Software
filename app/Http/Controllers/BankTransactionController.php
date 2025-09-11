<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\BankTransaction;
use App\Models\Notification;

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
            $transactions = BankTransaction::with('bankAccount')->orderByDesc('transaction_date')->whereNull('transfer_from')->get();

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
        $rules = [
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric',
        ];

        if($request->transfer_to){
            $rules['transfer_from'] = 'required|exists:bank_accounts,id';
            $rules['transfer_to'] = 'required|exists:bank_accounts,id';
        }else{
            // Exclude transfer_from from the request if exists
            $request->request->remove('transfer_from');
        }

        // Only require bank_account_id and transaction_type if not a transfer
        if (!($request->transfer_from && $request->transfer_to)) {
            $rules['bank_account_id'] = 'required|exists:bank_accounts,id';
            $rules['transaction_type'] = 'required|in:credit,debit';
            $rules['description'] = 'nullable|string|max:255';
            $rules['name'] = 'required|string|max:255';
            $rules['slug'] = 'required|string|max:255';
        }

        $request->validate($rules);

        if ($request->transfer_from && $request->transfer_to && $request->transfer_from == $request->transfer_to) {
            return response()->json([
                'success' => false,
                'errors' => ['transfer_from' => ['Cannot transfer to same account.']]
            ], 422);
        }

        if ($request->transfer_from && $request->transfer_to) {
            $fromAccount = BankAccount::find($request->transfer_from);
            
            $balance = $fromAccount->transactions()
                ->where('transaction_type', 'credit')
                ->sum('amount') - $fromAccount->transactions()
                ->where('transaction_type', 'debit')
                ->sum('amount');
            if ($fromAccount && $request->amount > $balance) {
                return response()->json([
                    'success' => false,
                    'errors' => ['amount' => ['Amount cannot be greater than the Transfer From bank\'s balance.']]
                ], 422);
            }
        }

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (BankTransaction::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);

        if($request->transfer_from && $request->transfer_to){

            $transferFromAccount = BankAccount::find($request->transfer_from);
            $transferToAccount = BankAccount::find($request->transfer_to);
            $mesgtoslug = $transferFromAccount->bank_name . '-to-' . $transferToAccount->bank_name;
            $toBaseSlug = \Str::slug($mesgtoslug);
            $toSlug = $toBaseSlug;
            $toCounter = 1;
            // Ensure unique slug for the transfer to account
            while (BankTransaction::where('slug', $toSlug)->exists()) {
                $toSlug = $toBaseSlug . '-' . $toCounter++;
            }
            $finalslug = $toSlug;

            // Create a debit transaction for the "transfer from" bank
            $from_id = BankTransaction::create([
                'transaction_date' => $request->transaction_date,
                'amount' => $request->amount,
                'description' => $request->description ? $request->description . ' (Transfer to ' . BankAccount::find($request->transfer_to)->bank_name . ')' : 'Transfer to ' . BankAccount::find($request->transfer_to)->bank_name,
                'bank_account_id' => $request->transfer_from,
                'transaction_type' => 'debit',
                'name' => $mesgtoslug,
                'slug' => $finalslug,
                'transfer_to' => $request->transfer_to,
            ]);

            // Create a credit transaction for the "transfer to" bank
            BankTransaction::create([
                'transaction_date' => $request->transaction_date,
                'amount' => $request->amount,
                'description' => $request->description ? $request->description . ' (Transfer from ' . BankAccount::find($request->transfer_from)->bank_name . ')' : 'Transfer from ' . BankAccount::find($request->transfer_from)->bank_name,
                'bank_account_id' => $request->transfer_to,
                'transaction_type' => 'credit',
                'name' => $mesgtoslug,
                'slug' => $finalslug,
                'transfer_from' => $request->transfer_from,
                'from_id' => $from_id->id,
            ]);

            Notification::create([
                'sent_date' => now(),
                'message' => auth()->user()->name . 'Transferred ' . number_format($request->amount, 2) . ' from ' . $transferFromAccount->bank_name . ' to ' . $transferToAccount->bank_name . '.',
            ]);

        }else{
            // Create a new bank transaction
            BankTransaction::create($request->all());
            $onlybank = BankAccount::find($request->bank_account_id);
            Notification::create([
                'sent_date' => now(),
                'message' => auth()->user()->name . ' added a new bank (' . $onlybank->bank_name . ') transaction of ' . number_format($request->amount, 2) . '.',
            ]);
        }
        

        // Redirect back with success message
        return response()->json([
            'success' => true,
             'message' => 'Bank transaction created successfully.',
            'id' => BankTransaction::latest()->first()->id,
            'bank_id' => $request->bank_account_id,
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
        $rules = [
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ];

        // Only require bank_account_id and transaction_type if not a transfer
        
        if($request->transfer_to){
            $rules['transfer_from'] = 'required|exists:bank_accounts,id';
            $rules['transfer_to'] = 'required|exists:bank_accounts,id';
        }

         // Only require bank_account_id and transaction_type if not a transfer
        if (!($request->transfer_from && $request->transfer_to)) {
            $rules['bank_account_id'] = 'required|exists:bank_accounts,id';
            $rules['transaction_type'] = 'required|in:credit,debit';
        }

        $request->validate($rules);

        if ($request->transfer_from && $request->transfer_to && $request->transfer_from == $request->transfer_to) {
            return response()->json([
            'success' => false,
            'errors' => ['transfer_from' => ['Cannot transfer to same account.']]
            ], 422);
        }

        



        $bankTransaction = BankTransaction::findOrFail($id);

        if ($request->transfer_from && $request->transfer_to) {
            $fromAccount = BankAccount::find($request->transfer_from);
            if($bankTransaction->bank_account_id != $fromAccount->id){
                $balance = $fromAccount->transactions()
                ->where('transaction_type', 'credit')
                ->sum('amount') - $fromAccount->transactions()
                ->where('transaction_type', 'debit')
                ->sum('amount') + $bankTransaction->amount;
            }else{
                $balance = $fromAccount->transactions()
                ->where('transaction_type', 'credit')
                ->sum('amount') - $fromAccount->transactions()
                ->where('transaction_type', 'debit')
                ->sum('amount') + $bankTransaction->amount;
            }
            if ($fromAccount && $request->amount > $balance) {
            return response()->json([
                'success' => false,
                'errors' => ['amount' => ['Amount cannot be greater than the Transfer From bank\'s balance.']]
            ], 422);
            }
        }

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
        
        // Update the bank transaction with the validated data

        if($request->transfer_from && $request->transfer_to){

            $bankTransaction->delete();
            $transfertobefore = BankTransaction::where('transfer_from', $bankTransaction->bank_account_id )->where('from_id', $id)->first();
            if($transfertobefore){
                $transfertobefore->delete();
            }

            $transferFromAccount = BankAccount::find($request->transfer_from);
            $transferToAccount = BankAccount::find($request->transfer_to);
            $mesgtoslug = $transferFromAccount->bank_name . '-to-' . $transferToAccount->bank_name;
            $toBaseSlug = \Str::slug($mesgtoslug);
            $toSlug = $toBaseSlug;
            $toCounter = 1;
            // Ensure unique slug for the transfer to account
            while (BankTransaction::where('slug', $toSlug)->exists()) {
                $toSlug = $toBaseSlug . '-' . $toCounter++;
            }
            $finalslug = $toSlug;

            // Create a debit transaction for the "transfer from" bank
            $from_id = BankTransaction::create([
                'transaction_date' => $request->transaction_date,
                'amount' => $request->amount,
                'description' => $request->description ? $request->description . ' (Transfer to ' . BankAccount::find($request->transfer_to)->bank_name . ')' : 'Transfer to ' . BankAccount::find($request->transfer_to)->bank_name,
                'bank_account_id' => $request->transfer_from,
                'transaction_type' => 'debit',
                'name' => $mesgtoslug,
                'slug' => $finalslug,
                'transfer_to' => $request->transfer_to,
            ]);

            // Create a credit transaction for the "transfer to" bank
            BankTransaction::create([
                'transaction_date' => $request->transaction_date,
                'amount' => $request->amount,
                'description' => $request->description ? $request->description . ' (Transfer from ' . BankAccount::find($request->transfer_from)->bank_name . ')' : 'Transfer from ' . BankAccount::find($request->transfer_from)->bank_name,
                'bank_account_id' => $request->transfer_to,
                'transaction_type' => 'credit',
                'name' => $mesgtoslug,
                'slug' => $finalslug,
                'transfer_from' => $request->transfer_from,
                'from_id' => $from_id->id,
            ]);

            Notification::create([
                'sent_date' => now(),
                'message' => auth()->user()->name . 'updated a Bank Transaction ' . number_format($request->amount, 2) . ' from ' . $transferFromAccount->bank_name . ' to ' . $transferToAccount->bank_name . '.',
            ]);
        }else{

            $transfertobefore = BankTransaction::where('transfer_from', $bankTransaction->bank_account_id )->where('from_id', $id)->first();
            if($transfertobefore){
                $transfertobefore->delete();
            }

            $bankTransaction->update($request->all());
            
            $onlybank = BankAccount::find($request->bank_account_id);
            Notification::create([
                'sent_date' => now(),
                'message' => auth()->user()->name . ' updated a bank (' . $onlybank->bank_name . ') transaction of ' . number_format($request->amount, 2) . '.',
            ]);
        }


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

        if($bankTransaction->transfer_to){
            $transfertobefore = BankTransaction::where('transfer_from', $bankTransaction->bank_account_id )->where('from_id', $id)->first();
            if($transfertobefore){
                $transfertobefore->delete();
            }
        }

        // Delete the bank transaction
        $bankTransaction->delete();

        // Redirect back with success message
        return back()->with('success', 'Bank transaction deleted successfully.');
    }
}
