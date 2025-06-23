<?php

namespace App\Mail;

use App\Models\Asset;
use App\Models\EmailTemplate;
use App\Models\Liability;
use App\Models\SiteSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class LiabilityMonthlyInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageText;
    public $contact;
    public $liability;
    public $totalAmount;

    public function __construct(Liability $liability)
    {
        $this->liability = $liability;

        $this->totalAmount = $liability->transactions()->where('transaction_type', 'Deposit')->sum('amount') -
                             $liability->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
        
    }

    public function build()
    {
        $body = EmailTemplate::find(8);
        // Convert to Bangla inside build (safe for queued mails)
        $totalAmountBn = $this->engToBnNumber(number_format($this->totalAmount, 2));
        
        $templateText = $body->body ?? '';

        $html = view('pdf.liability_monthly_invoice', [
            'liability' => $this->liability,
            'totalAmount' => $totalAmountBn,
        ])->render();

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $customFontDir = storage_path('fonts');

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'fontDir' => array_merge($fontDirs, [$customFontDir]),
            'fontdata' => $fontData + [
                'solaimanlipi' => [
                    'R' => 'SolaimanLipi.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'solaimanlipi',
            'tempDir' => storage_path('app/tmp'),
        ]);

        $mpdf->WriteHTML($html);
        $pdf = $mpdf->Output('', 'S');

        return $this->subject('Monthly Liability Report')
                    ->view('emails.liability.monthlyInvoice')
                    ->with([
                        'liability' => $this->liability,
                        'totalAmountBn' => $totalAmountBn,
                        'templateText' => $templateText,
                    ])
                    ->attachData($pdf, 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
    private function engToBnNumber($number)
    {
        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn  = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($eng, $bn, $number);
    }
}
