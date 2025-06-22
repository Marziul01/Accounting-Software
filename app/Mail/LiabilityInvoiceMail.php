<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LiabilityInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageText;
    public $contact;

    public function __construct($asset, $request)
    {
        $emailTemplate = EmailTemplate::find(4);
        $siteSettings = SiteSetting::find(1);
        $amount = $request->amount;

        $contactName = $asset->name;
        $accountNumber = '#' . $asset->slug . $asset->id;

        // Default fallback template if not found
$templateBody="প্রিয় $contactName, $emailTemplate->body $accountNumber ।  রাসেল এর নিকট আপনার প্রদত্ত মোট অবশিষ্ট পাওনা ঋণের পরিমাণ $amount টাকা।";

        $footer = "
ধন্যবাদান্তে,

{$siteSettings->site_owner}

ঠিকানা: {$siteSettings->site_address}
ইমেইল: {$siteSettings->site_email}
ওয়েবসাইট : {$siteSettings->site_link}
        ";

        $this->messageText = $templateBody . "\n\n" . $footer;
        $this->contact = $request->email; // Not necessary but good if you want to use in view
    }

    public function build()
    {
        return $this->view('emails.liability.invoice')
            ->subject('Liability Created Invoice')
            ->with([
                'messageText' => $this->messageText,
            ]);
    }
}
