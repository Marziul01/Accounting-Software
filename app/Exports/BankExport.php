<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class BankExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public $bank;

    public function __construct($bank)
    {
        $this->bank = $bank;
    }

    public function collection()
    {
        return $this->bank->transactions;
    }

    public function headings(): array
    {
        // These are column headers, starting from row 2 because row 1 will have the investment name
        return ['ID', 'Transaction Type', 'Amount', 'Date'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->transaction_type,
            $transaction->amount,
            $transaction->transaction_date,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                // Set the investment name at A1
                $event->sheet->setCellValue('A1',
                    "Bank: " . $this->bank->bank_name . "\n" .
                    "Account Holder: " . ($this->bank->account_holder_name ?? '') . "\n" .
                    "Account Number: " . ($this->bank->account_number  ?? '') . "\n" .
                    "Account Type: " . ($this->bank->account_type ?? '').
                    "Branch Name: " . ($this->bank->branch_name ?? '').
                    "Nominee Name: " . ($this->bank->nominee_name ?? '')
                );

                // Enable text wrapping for cell A1
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setWrapText(true);

                // Push all data down by 1 row
                $event->sheet->getDelegate()->insertNewRowBefore(2, 1);
            },
        ];
    }
}
