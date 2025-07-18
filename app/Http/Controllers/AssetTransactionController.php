<?php

namespace App\Http\Controllers;

use App\Mail\AssetDepositInvoiceMail;
use App\Mail\AssetWithdrawInvoiceMail;
use App\Models\AssetTransaction;
use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\SiteSetting;
use App\Models\SMSTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

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
        $transactions = AssetTransaction::where('asset_id', $asset->id)->orderBy('transaction_date' , 'asc' )->get();

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

        // Recalculate current balance after creating transaction
        $totalDeposit = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount');
        $totalWithdraw = $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
        $currentBalance = $totalDeposit - $totalWithdraw;

        // Save updated balance to asset
        $asset->amount = $currentBalance;

        $asset->save();

        if($asset->category_id == 4 ){
            if( $asset->send_sms == 1){
                if($request->transaction_type === 'Deposit'){
                    $body = SMSTemplate::find(2);
                    $templateText = $body?->body ?? '';
                    $site_name = SiteSetting::find(1);
                    $accountName = $asset->name;
                    $accountNumber = '#'.$asset->slug.$asset->id; // or $assetsfdf->id if you prefer
                    $amount = $this->engToBnNumber($request->amount);
                    $totalamount = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount') - $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
                    $totalamountBn = $this->engToBnNumber($totalamount);

$message="আসসালামু আলাইকুম,
প্রিয় {$accountName}, 
আপনার নিকট $amount টাকা প্রদান করা হয়েছে । $templateText রাসেল এর নিকট থেকে আপনার গৃহীত মোট ঋণের পরিমাণ $totalamountBn টাকা।";
                }else{
                    $body = SMSTemplate::find(3);
                    $templateText = $body?->body ?? '';
                    $site_name = SiteSetting::find(1);
                    $accountName = $asset->name;
                    $accountNumber = '#'.$asset->slug.$asset->id; // or $assetsfdf->id if you prefer
                    $amount = $this->engToBnNumber($request->amount);
                    $totalamount = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount') - $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
                    $totalamountBn = $this->engToBnNumber($totalamount);

$message="আসসালামু আলাইকুম,
প্রিয় {$accountName}, 
আপনার নিকট থেকে $amount টাকা আদায় হয়েছে । $templateText রাসেল এর নিকট থেকে আপনার গৃহীত মোট অবশিষ্ট অপরিশোধিত ঋণের পরিমাণ  $totalamountBn টাকা।";
                }
                
                $number = '88'.$asset->mobile;
                $response = sendSMS($number, $message);

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
                if($request->transaction_type === 'Deposit'){
                    Mail::to($asset->email)->send(new AssetDepositInvoiceMail($asset, $request));
                }else{
                    Mail::to($asset->email)->send(new AssetWithdrawInvoiceMail($asset, $request));
                }
                
            }

        }

        // Redirect back to the index with a success message
        return response()->json([
            'message' => 'Asset Transaction created successfully!',
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

        // Recalculate current balance after update
        $totalDeposit = $asset->transactions()->where('transaction_type', 'Deposit')->sum('amount');
        $totalWithdraw = $asset->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
        $currentBalance = $totalDeposit - $totalWithdraw;

        // Save recalculated balance to asset
        $asset->amount = $currentBalance;
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
        
        // Get the asset related to the transaction
        $asset = Asset::findOrFail($assetTransaction->asset_id);

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
        

        // Now delete the transaction
        $assetTransaction->delete();

        // Return back with success
        return back()->with('success', 'Asset Transaction deleted successfully!');
    }

}
