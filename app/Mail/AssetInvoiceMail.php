<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;
use App\Models\SiteSetting;

class AssetInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageText;
    public $contact;

    public function __construct($asset, $request)
    {
        $emailTemplate = EmailTemplate::find(1);
        $siteSettings = SiteSetting::find(1);
        $amount = $request->amount;

        $contactName = $asset->name;
        $accountNumber = '#' . $asset->slug . $asset->id;

        // Default fallback template if not found
$templateBody="প্রিয় $contactName, $emailTemplate->body $accountNumber । গৃহীত ঋণের পরিমাণ $amount টাকা ।";

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
        return $this->view('emails.asset.invoice')
            ->subject('Asset Created Invoice')
            ->with([
                'messageText' => $this->messageText,
            ]);
    }
}
