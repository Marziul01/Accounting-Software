<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use App\Models\Asset;
use App\Models\EmailTemplate;
use App\Models\Liability;
use Illuminate\Http\Request;

class LiabilityWithdrawInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $liability;
    public $request;
    public $totalAmount;
    public $previousAmount;

    public function __construct(Liability $liability, Request $request)
    {
        $this->liability = $liability;
        $this->request = $request;

        $this->totalAmount = $liability->transactions()->where('transaction_type', 'Deposit')->sum('amount') -
                             $liability->transactions()->where('transaction_type', 'Withdraw')->sum('amount');
    }

    public function build()
    {
        // Convert to Bangla inside build (safe for queued mails)
        $requestASmount = $this->engToBnNumber(number_format($this->request->amount, 2));
        $totalAmountBn = $this->engToBnNumber(number_format($this->totalAmount, 2));
        $previousAmountBn = $this->engToBnNumber(number_format($this->totalAmount + $this->request->amount, 2));
        $transDate = $this->engToBnNumber(\Carbon\Carbon::parse($this->request->transaction_date)->format('d-m-Y'));
        $startDate = $this->liability->transactions->min('transaction_date');
        $endDate = $this->liability->transactions->max('transaction_date');
        $body = EmailTemplate::find(3);
        $templateText = $body->body ?? '';

        $html = view('pdf.liability_withdraw_invoice', [
            'liability' => $this->liability,
            'request' => $this->request,
            'totalAmount' => $totalAmountBn,
            'previousAmount' => $previousAmountBn,
            'requestASmount' => $requestASmount,
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

        return $this->subject('ঋণ পরিশোধের রসিদ')
                    ->view('emails.liability.withdraw')
                    ->with([
                        'liability' => $this->liability,
                        'request' => $this->request,
                        'totalAmountBn' => $totalAmountBn,
                        'previousAmountBn' => $previousAmountBn,
                        'requestASmount' => $requestASmount,
                        'transDate' => $transDate,
                        'templateText' => $templateText,
                        'startDate' => $startDate,
                        'endDate' => $endDate
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
