<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class LiabilityExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public $liability;

    public function __construct($liability)
    {
        $this->liability = $liability;
    }

    public function collection()
    {
        return $this->liability->transactions;
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
                    "Liability: " . $this->liability->name . "\n" .
                    "Category: " . ($this->liability->subcategory->liabilityCategory->name ?? '') . "\n" .
                    "Description: " . ($this->liability->description ?? '').
                    "Date: " . ($this->liability->entry_date ?? '').
                    "Mobile Number: " . ($this->liability->mobile ?? '')
                    
                );

                // Enable text wrapping for cell A1
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setWrapText(true);

                // Push all data down by 1 row
                $event->sheet->getDelegate()->insertNewRowBefore(2, 1);
            },
        ];
    }
}
