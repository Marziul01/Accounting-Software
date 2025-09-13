<?php

namespace App\Http\Controllers;

use App\Mail\LiabilityDepositInvoiceMail;
use App\Mail\LiabilityWithdrawInvoiceMail;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Liability;
use App\Models\LiabilityTransaction;
use App\Models\Notification;
use App\Models\SiteSetting;
use App\Models\SMSTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        $banks = BankAccount::all();
        $bankTransaction = BankTransaction::where('from', 'Liability')->get();
        // Return the view with the liability and its transactions
        return view('admin.liability.transactions', compact('liability', 'transactions', 'banks', 'bankTransaction'));
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

        if($request->transaction_type === 'Withdraw'){
            if ($request->has('bank_account_id') && $request->bank_account_id) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                $balance = $bankAccount->transactions()->where('transaction_type', 'credit')->sum('amount')
                        - $bankAccount->transactions()->where('transaction_type', 'debit')->sum('amount');

                if ($bankAccount && $request->amount > $balance) {
                    return response()->json([
                        'errors' => [
                            'amount' => ['Liability Withdraw amount cannot be greater than the bank balance.']
                        ]
                    ], 422);
                }
            }
        }

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
        

        // Recalculate current balance after creating transaction
        $totalDeposit = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount');
        $totalWithdraw = $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
        $currentBalance = $totalDeposit - $totalWithdraw;

        // Save updated balance to asset
        $asset->amount = $currentBalance;

        $asset->save();

        $message = '';
        $smsSent = 0;
        $emailSent = 0;

        if($asset->category_id == 3 ){
            if( $asset->send_sms == 1){
                if($request->transaction_type === 'Deposit'){
                    $body = SMSTemplate::find(5);
                    $templateText = $body?->body ?? '';
                    $site_name = SiteSetting::find(1);
                    $accountName = $asset->name;
                    $accountNumber = '#'.$asset->slug.$asset->id; // or $assetsfdf->id if you prefer
                    $amount = $this->engToBnNumber($request->amount);
                    $totalamount = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount') - $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
                    $totalamountBn = $this->engToBnNumber($totalamount);

$message="আসসালামু আলাইকুম,
প্রিয় {$accountName}, 
আপনার নিকট থেকে $amount টাকা  গ্রহন  করা হয়েছে । $templateText রাসেল এর নিকট আপনার প্রদত্ত মোট অবশিষ্ট পাওনা ঋণের পরিমাণ $totalamountBn টাকা।";
                }else{
                    $body = SMSTemplate::find(6);
                    $templateText = $body?->body ?? '';
                    $site_name = SiteSetting::find(1);
                    $accountName = $asset->name;
                    $accountNumber = '#'.$asset->slug.$asset->id; // or $assetsfdf->id if you prefer
                    $amount = $this->engToBnNumber($request->amount);
                    $totalamount = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount') - $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
                    $totalamountBn = $this->engToBnNumber($totalamount);

$message="আসসালামু আলাইকুম,
প্রিয় {$accountName}, 
আপনার নিকট $amount টাকা পরিশোধ করা হয়েছে । $templateText রাসেল এর নিকট আপনার প্রদত্ত মোট অবশিষ্ট পাওনা ঋণের পরিমাণ  $totalamountBn টাকা।";
                }
                $number = '88'.$asset->mobile;

                $response = sendSMS($number, $message);
                $smsSent = $asset->send_sms == 1 ? 1 : 0;

                // Optional: Map response code to readable message
                $errorMessages = [
                    '1001' => '❌ ভুল API কী প্রদান করা হয়েছে।',
                    '1002' => '❌ ভুল Sender ID ব্যবহার করা হয়েছে।',
                    '1003' => '❌ টাইপ অবশ্যই text অথবা unicode হতে হবে।',
                    '1004' => '❌ শুধুমাত্র GET বা POST মেথড অনুমোদিত।',
                    '1005' => '❌ এই prefix এ SMS পাঠানো সম্ভব নয় কারণ এটি নিষ্ক্রিয়।',
                    '1006' => '❌ অ্যাকাউন্টে পর্যাপ্ত ব্যালেন্স নেই।',
                    '1007' => '❌ মোবাইল নম্বর অবশ্যই country code (88) দিয়ে শুরু হতে হবে।',
                ];

                if (isset($errorMessages[$response])) {
                    session()->flash('error', $errorMessages[$response]);
                }
            }

            if ($asset->send_email == 1) {
                try {
                    if($request->transaction_type === 'Deposit'){
                        Mail::to($asset->email)->send(new LiabilityDepositInvoiceMail($asset, $request));
                    }else{
                        Mail::to($asset->email)->send(new LiabilityWithdrawInvoiceMail($asset, $request));
                    }

                    // If no exception, email was sent successfully
                    $emailSent = 1;

                } catch (\Exception $e) {
                    // Log error for debugging
                    \Log::error('Email sending failed: ' . $e->getMessage());
                    $emailSent = 0; // Remains 0 if failed
                }
            }

        }

        if($request->has('bank_account_id') && $request->bank_account_id) {
            $bankAccount = BankAccount::find($request->bank_account_id);
            if ($bankAccount) {
                $bankTransaction = new BankTransaction();
                $bankTransaction->bank_account_id = $bankAccount->id;
                $bankTransaction->transaction_date = $request->transaction_date;
                $bankTransaction->amount = $request->amount;
                if($request->transaction_type === 'Withdraw'){
                    $bankTransaction->transaction_type = 'debit';
                }else{
                    $bankTransaction->transaction_type = 'credit';
                }
                $bankTransaction->description = $request->bank_description;
                $bankTransaction->name = 'Liability: '.$asset->name;
                $bankTransaction->slug = 'Liability-' . $asset->slug;
                $bankTransaction->from = 'Liability';
                $bankTransaction->from_id = $assetTransaction->id;
                $bankTransaction->save();
            }
        }

        if($asset->category_id == 3){
            Notification::create([
                'sent_date' => now(),
                'sender_name' => $asset->name,
                'message' => $message,
                'sent_date' => now(),
                'email_sent' => $emailSent,
                'sms_sent' => $smsSent,
                'occasion_name' => Auth()->user()->name . ' created a new ' . $request->transaction_type . ' Transaction Short Term Liability: ' . $asset->name,
                'contact_id' => $asset->contact_id,
            ]);
        }else{
            Notification::create([
                'sent_date' => now(),
                'message' => Auth()->user()->name . ' created a new ' . $request->transaction_type . ' Transaction Long Term Liability: ' . $asset->name . '('. $request->amount .' BDT)' .'.',
            ]);
        }

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Liability Transaction created successfully!',
            'id' => $assetTransaction->id,
        ]);
    }

    public function engToBnNumber($number) {
        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn  = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($eng, $bn, $number);
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

        if($request->transaction_type === 'Withdraw'){
            if ($request->has('bank_account_id') && $request->bank_account_id) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                $currentTransaction = BankTransaction::where('from', 'Liability')->where('from_id', $id)->first();
                $balance = $bankAccount->transactions()->where('transaction_type', 'credit')->sum('amount')
                        - $bankAccount->transactions()->where('transaction_type', 'debit')->sum('amount') - ($currentTransaction ? $currentTransaction->amount : 0);

                if ($bankAccount && $request->amount > $balance) {
                    return response()->json([
                        'errors' => [
                            'amount' => ['Liability Withdraw amount cannot be greater than the bank balance.']
                        ]
                    ], 422);
                }
            }
        }

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

        // Update the transaction record
        $assetTransaction->update($request->all());    

        // Recalculate current balance after update
        $totalDeposit = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount');
        $totalWithdraw = $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
        $currentBalance = $totalDeposit - $totalWithdraw;

        // Save recalculated balance to asset
        $asset->amount = $currentBalance;
        $asset->save();

        // Update or create the bank transaction
        if($request->has('bank_account_id') && $request->bank_account_id) {
            $bankAccount = BankAccount::find($request->bank_account_id);
            if ($bankAccount) {
                $bankTransaction = BankTransaction::where('from', 'Liability')->where('from_id', $id)->first();
                if (!$bankTransaction) {
                    $bankTransaction = new BankTransaction();
                    $bankTransaction->bank_account_id = $bankAccount->id;
                    $bankTransaction->from = 'Liability';
                    $bankTransaction->from_id = $assetTransaction->id;
                    $bankTransaction->name = 'Liability: '.$asset->name; 
                    $bankTransaction->slug = 'Liability-' . $asset->slug;
                } else {
                    // If bank account changed, update it
                    if ($bankTransaction->bank_account_id != $bankAccount->id) {
                        $bankTransaction->bank_account_id = $bankAccount->id;
                    }
                }
                $bankTransaction->transaction_date = $request->transaction_date;
                $bankTransaction->amount = $request->amount;
                if($request->transaction_type === 'withdraw'){
                    $bankTransaction->transaction_type = 'debit';
                }else{
                    $bankTransaction->transaction_type = 'credit';
                }
                $bankTransaction->description = $request->bank_description;
                $bankTransaction->save();
            }
        } else {
            // If no bank account selected, delete existing bank transaction if any
            $bankTransaction = BankTransaction::where('from', 'Liability')->where('from_id', $id)->first();
            if ($bankTransaction) {
                $bankTransaction->delete();
            }
        }

        Notification::create([
            'message' => Auth()->user()->name . ' updated a ' . $request->transaction_type . ' Transaction Liability: ' . $asset->name .'('. $request->amount .' BDT)' .'.',
            'sent_date' => now(),
        ]);

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

        // Get the asset related to the transaction
        $asset = Liability::findOrFail($assetTransaction->liability_id);

        // Determine the transaction type and update the asset amount accordingly
        if ($assetTransaction->transaction_type === 'Deposit') {
            // Subtract the transaction amount from asset amount
            $asset->amount -= $assetTransaction->amount;
        } elseif ($assetTransaction->transaction_type === 'Withdraw') {
            // Add the transaction amount to asset amount
            $asset->amount += $assetTransaction->amount;
        }

        // Save the updated asset amount
        $asset->save();

        // Delete associated bank transaction if exists
        $bankTransaction = BankTransaction::where('from', 'Liability')->where('from_id', $id)->first();
        if ($bankTransaction) {
            $bankTransaction->delete();
        }
        
        Notification::create([
            'message' => Auth()->user()->name . ' deleted a ' . $assetTransaction->transaction_type . ' Transaction Liability: ' . $asset->name .'('. $assetTransaction->amount .' BDT)' .'.',
            'sent_date' => now(),
        ]);

        // Now delete the transaction
        $assetTransaction->delete();

        // Return back with success
        return back()->with('success', 'Liability Transaction deleted successfully!');
    }
}
