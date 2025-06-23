<?php

namespace App\Console\Commands;

use App\Mail\LiabilityMonthlyInvoiceMail;
use App\Models\Liability;
use App\Models\SiteSetting;
use App\Models\SMSTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMonthlyLiabilityNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-monthly-liability-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $liabilities = Liability::with('contact')
            ->where('category_id', 3)
            ->get();

        $smsTemplate = SMSTemplate::find(8);
        $site = SiteSetting::find(1);

        foreach ($liabilities as $liability) {
            $contact = $liability->contact;
            $liabilitytotal = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount') - $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount') ;
            if (!$contact) continue;

            // ✅ Send SMS
            if ($contact->sms_option == 1 && $contact->mobile_number  && $liabilitytotal > 0  ) {
                $number = '88' . $contact->mobile_number;
                $accountName = $liability->name;
                $accountNumber = '#' . $liability->slug . $liability->id;
                $total = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount') - $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount') ;// create this helper if needed
                $amount = $this->engToBnNumber($total);

$message="প্রিয় {$accountName}, {$smsTemplate->body} {$amount} টাকা ।

ধন্যবাদান্তে,

{$site->site_owner}";

                $response = sendSMS($number, $message); // your helper

                \Log::info("SMS sent to {$number}: {$response}");
            }

            // ✅ Send Email
            if ($contact->send_email == 1 && $contact->email && $liabilitytotal > 0) {
                Mail::to($contact->email)->send(new LiabilityMonthlyInvoiceMail($liability));
                \Log::info("Email sent to {$contact->email}");
            }
        }

        return 0;
    }

    public function engToBnNumber($number) {
        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn  = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($eng, $bn, $number);
    }
}
