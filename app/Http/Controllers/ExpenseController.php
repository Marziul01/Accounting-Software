<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (Auth::user()->access->expense == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all expenses from the database
        $expenses = Expense::all();

        if ($request->ajax()) {
            $expenses = Expense::with(['expenseCategory', 'expenseSubCategory'])->orderByDesc('date');

            return DataTables::of($expenses)
                ->addIndexColumn()
                ->editColumn('expense_category', function ($row) {
                    return $row->expenseCategory->name ?? 'Not Assigned';
                })
                ->editColumn('expense_sub_category', function ($row) {
                    return $row->expenseSubCategory->name ?? 'Not Assigned';
                })
                ->editColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->date)->format('d M, Y');
                })
                ->addColumn('action', function ($row) {
                    return view('admin.expense._actions', compact('row'))->render();
                })
                ->rawColumns(['action']) // so buttons don’t get escaped
                ->make(true);
        }


        return view('admin.expense.expense', [
            'expenses' => $expenses,
            'expenseCategories' => ExpenseCategory::where('status', 1)->where('id', '!=', 7)->get(),
            'expenseSubCategories' => ExpenseSubCategory::where('status', 1)->get(),
            'banks' => BankAccount::all(),
            'bankTransaction' => BankTransaction::where('from', 'Expense')->get(),
        ]);
    }

    public static function report()
    {
        if (Auth::user()->access->expense == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        $expense = Expense::all();
        $firstDate = $expense->min('date');
        $lastDate = $expense->max('date');
        $expenseCategory = ExpenseCategory::where('status', 1)->first();
        return view('admin.expense.report', [
            'expenses' => Expense::all(),
            'expenseCategories' => ExpenseCategory::where('status', 1)->get(),
            'expenseSubCategories' => ExpenseSubCategory::where('status', 1)->get(),
            'firstDate' => $firstDate,
            'lastDate' => $lastDate,    
            'expenseCategory' => $expenseCategory,
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
        if (Auth::user()->access->expense != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create .');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'expense_sub_category_id' => 'required|exists:expense_sub_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        if ($request->has('bank_account_id') && $request->bank_account_id) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                
                $balance = $bankAccount->transactions()->where('transaction_type', 'credit')->sum('amount')
                        - $bankAccount->transactions()->where('transaction_type', 'debit')->sum('amount') ;

                if ($bankAccount && $request->amount > $balance) {
                    return response()->json([
                        'errors' => [
                            'amount' => ['Expense amount cannot be greater than the bank balance.']
                        ]
                    ], 422);
                }
            }

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (Expense::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        // Create a new expense record
        $newepense = Expense::create($request->all());

        if($request->has('bank_account_id') && $request->bank_account_id) {
            $bankAccount = BankAccount::find($request->bank_account_id);
            if ($bankAccount) {
                $bankTransaction = new BankTransaction();
                $bankTransaction->bank_account_id = $bankAccount->id;
                $bankTransaction->transaction_date = $request->date;
                $bankTransaction->amount = $request->amount;
                $bankTransaction->transaction_type = 'debit';
                $bankTransaction->description = $request->bank_description;
                $bankTransaction->name = 'Expense: '.$newepense->name;
                $bankTransaction->slug = 'Expense-' . $newepense->slug;
                $bankTransaction->from = 'Expense';
                $bankTransaction->from_id = $newepense->id;
                $bankTransaction->save();
            }
        }

        Notification::create([
            'message' => Auth()->user()->name . ' created a new Expense: ' . $newepense->name .'('. $request->amount .' BDT)' .'.',
            'sent_date' => now(),
        ]);

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Expense record created successfully!',
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
        if (Auth::user()->access->expense != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update .');
        }
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'expense_sub_category_id' => 'required|exists:expense_sub_categories,id',
            'slug' => 'required|string|max:255',
        ]);

        if ($request->has('bank_account_id') && $request->bank_account_id) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                $currentTransaction = BankTransaction::where('from', 'Expense')->where('from_id', $id)->first();
                $balance = $bankAccount->transactions()->where('transaction_type', 'credit')->sum('amount')
                        - $bankAccount->transactions()->where('transaction_type', 'debit')->sum('amount') + ($currentTransaction ? $currentTransaction->amount : 0);

                if ($bankAccount && $request->amount > $balance) {
                    return response()->json([
                        'errors' => [
                            'amount' => ['Expense amount cannot be greater than the bank balance.']
                        ]
                    ], 422);
                }
            }

        // Update the expense record
        $expense = Expense::findOrFail($id);
        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the contacts table
        while (Expense::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;
        $request->merge(['slug' => $data['slug']]);
        $expense->update($request->all());

        if($request->has('bank_account_id') && $request->bank_account_id) {
            $bankAccount = BankAccount::find($request->bank_account_id);
            if ($bankAccount) {
                $bankTransaction = BankTransaction::where('from', 'Expense')->where('from_id', $id)->first();
                if (!$bankTransaction) {
                    $bankTransaction = new BankTransaction();
                    $bankTransaction->bank_account_id = $bankAccount->id;
                    $bankTransaction->from = 'Expense';
                    $bankTransaction->from_id = $bankTransaction->id;
                    $bankTransaction->name = 'Expense: '.$expense->name; 
                    $bankTransaction->slug = 'Expense-' . $expense->slug;
                } else {
                    // If bank account changed, update it
                    if ($bankTransaction->bank_account_id != $bankAccount->id) {
                        $bankTransaction->bank_account_id = $bankAccount->id;
                    }
                }
                $bankTransaction->transaction_date = $request->date;
                $bankTransaction->amount = $request->amount;
                $bankTransaction->transaction_type = 'debit';
                $bankTransaction->description = $request->bank_description;
                $bankTransaction->save();
            }
        } else {
            // If no bank account selected, delete existing bank transaction if any
            $bankTransaction = BankTransaction::where('from', 'Expense')->where('from_id', $id)->first();
            if ($bankTransaction) {
                $bankTransaction->delete();
            }
        }

        Notification::create([
            'message' => Auth()->user()->name . ' updated a Expense: ' . $expense->name .'('. $request->amount .' BDT)' .'.',
            'sent_date' => now(),
        ]);

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Expense record updated successfully!',
            'id' => $expense->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->access->expense != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete .');
        }
        // Find the expense record and delete it
        $expense = Expense::findOrFail($id);

        $bankTransaction = BankTransaction::where('from', 'Expense')->where('from_id', $expense->id)->first();
        if($bankTransaction) {
            $bankTransaction->delete();
        }

        if($expense->expense_category_id == 7) {
            return back()->with('error', 'You cannot delete this expense record.');
        }

        $expense->delete();

        Notification::create([
            'message' => Auth()->user()->name . ' deleted a Expense: ' . $expense->name .'('. $expense->amount .' BDT)' .'.',
            'sent_date' => now(),
        ]);

        // Redirect back to the index with a success message
        return back()->with('success', 'Expense record deleted successfully!');
    }

    public function expensecategoryReport(Request $request)
    {
        $category = ExpenseCategory::where('slug', $request->slug)->firstOrFail();

        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();

        $expenses = Expense::where('expense_category_id', $category->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return view('admin.expense.category-report', compact('category', 'expenses', 'startDate', 'endDate'));
    }

    public function expensesubcategoryReport($slug, Request $request)
    {
        $subcategory = ExpenseSubCategory::where('slug', $slug)->firstOrFail();

        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();

        $expenses = Expense::where('expense_sub_category_id', $subcategory->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return view('admin.expense.subcategory-report', compact('subcategory', 'expenses', 'startDate', 'endDate'));
    }

    public function filter(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $categoryId = $request->category;
        $expenseCategory = ExpenseCategory::findOrFail($categoryId);

        $expenseCategories = ExpenseCategory::all();
        $expenses = Expense::whereBetween('date', [$startDate, $endDate])->get();
        $expenses = $expenses->where('expense_category_id', $request->category);

        return response()->json([
            'html' => view('admin.expense.partial-table', compact('expenseCategories', 'expenses', 'startDate', 'endDate', 'expenseCategory'))->render()
        ]);
    }

    public function fullReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();

        $expenses = Expense::whereBetween('date', [$startDate, $endDate])->get();
        $expenseCategories = ExpenseCategory::where('status', 1)->get();

        return view('admin.expense.full-report', compact('expenses', 'startDate', 'endDate' , 'expenseCategories'));
    }

    public function editMdals($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::all();

        return view('admin.expense.edit-form', compact('expense', 'categories'));
    }
}
