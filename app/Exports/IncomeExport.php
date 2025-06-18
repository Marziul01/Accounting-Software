<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class IncomeExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public $subcategory;

    public function __construct($subcategory)
    {
        $this->subcategory = $subcategory;
    }

    public function collection()
    {
        return $this->subcategory->incomes;
    }

    public function headings(): array
    {
        // These are column headers, starting from row 2 because row 1 will have the investment name
        return ['ID', 'Name', 'Amount', 'Date'];
    }

    public function map($income): array
    {
        return [
            $income->id,
            $income->name,
            $income->amount,
            $income->date,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                // Set the investment name at A1
                $event->sheet->setCellValue('A1',
                    "Category: " . ($this->subcategory->incomeCategory->name ?? '') . "\n" .
                    "Subcategory Name: " . ($this->subcategory->name ?? '')
                );

                // Enable text wrapping for cell A1
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setWrapText(true);

                // Push all data down by 1 row
                $event->sheet->getDelegate()->insertNewRowBefore(2, 1);
            },
        ];
    }
}
