<?php

namespace App\Http\Controllers;

use App\Models\BankScheduleController;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankSchedule;
use App\Models\BankTransaction;
use Illuminate\Http\Request;

class BankScheduleControllerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.bank_accounts.schedule',[
            'schedules' => BankSchedule::all(),
            'banks' => BankAccount::all(),
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
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create bank schdules.');
        }
        $rules = [
            'from' => 'required|exists:bank_accounts,id',
            'to' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric',
            'start_date' => 'required|date',
            'description' => 'nullable',
        ];

        // Conditional rule for end_date
        if ($request->input('infinite') == 1) {
            $rules['end_date'] = 'nullable|date';
        } else {
            $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
        }

        $request->validate($rules);

        // Create a new bank account
        BankSchedule::create($request->all());

        // Redirect back with success message
        return response()->json(['success' => true, 'message' => 'Bank Schedule created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id )
    {
        // Check if the user has permission to update bank accounts
        if (auth()->user()->access->bankbook != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update bank Schedule.');
        }

        $rules = [
            'from' => 'required|exists:bank_accounts,id',
            'to' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric',
            'start_date' => 'required|date',
            'description' => 'nullable',
        ];

        // Conditional rule for end_date
        if ($request->input('infinite') == 1) {
            $rules['end_date'] = 'nullable|date';
        } else {
            $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
        }

        $request->validate($rules);

        // Find the bank account by ID and update it
        $Schedule = BankSchedule::findOrFail($id);
        $Schedule->update($request->all());

        // Redirect back with success message
        return response()->json([
            'success' => true, 'message' => 'Bank Schedule updated successfully.',
            'id' => $Schedule->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Check if the user has permission to delete bank accounts
        if (auth()->user()->access->bankbook != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete bank Schedule.');
        }
        // Find the bank account by ID and delete it
        $Schedule = BankSchedule::findOrFail($id);
        $Schedule->delete();

        // Redirect back with success message
        return back()->with('success', 'Bank Schedule deleted successfully.');
    }
}
