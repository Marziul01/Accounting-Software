<?php

namespace App\Console\Commands;

use App\Exports\BankExport;
use App\Models\Asset;
use App\Models\BankAccount;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\IncomeCategory;
use App\Models\IncomeSubCategory;
use Illuminate\Console\Command;
use App\Models\Investment;
use App\Models\Liability;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Illuminate\Support\Str;

class AutoMonthlyExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-monthly-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports monthly reports as PDFs and emails to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $exportPath = storage_path('app/exports');

            // Clean export folder only ONCE at the beginning
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0777, true);

            $fontConfig = $this->mpdfConfig();

            $files = [];

            // 1. Investment
            $investments = Investment::with(['investmentSubCategory.investmentCategory', 'transactions'])->get();
            $files[] = $this->exportAsPdf(
                'investments_' . now()->format('Y_m') . '.pdf',
                'admin.exports.investments-pdf',
                compact('investments'),
                $fontConfig
            );

            // 2. Income
            $categories = IncomeCategory::with(['incomeSubCategories.incomes'])->get();
            $files[] = $this->exportAsPdf(
                'incomes_' . now()->format('Y_m') . '.pdf',
                'admin.exports.incomes-pdf',
                compact('categories'),
                $fontConfig
            );

            // 3. Expense
            $categories = ExpenseCategory::with(['expenseSubCategories.expenses'])->get();
            $files[] = $this->exportAsPdf(
                'expenses_' . now()->format('Y_m') . '.pdf',
                'admin.exports.expenses-pdf',
                compact('categories'),
                $fontConfig
            );

            // 4. Asset
            $assets = Asset::with(['subcategory.assetCategory', 'transactions'])->get();
            $files[] = $this->exportAsPdf(
                'assets_' . now()->format('Y_m') . '.pdf',
                'admin.exports.asset-pdf',
                compact('assets'),
                $fontConfig
            );

            // 5. Liability
            $liabilities = Liability::with(['subcategory.liabilityCategory', 'transactions'])->get();
            $files[] = $this->exportAsPdf(
                'liabilities_' . now()->format('Y_m') . '.pdf',
                'admin.exports.liability-pdf',
                compact('liabilities'),
                $fontConfig
            );

            // 6. Bank
            $banks = BankAccount::with(['transactions'])->get();
            $files[] = $this->exportAsPdf(
                'banks_' . now()->format('Y_m') . '.pdf',
                'admin.exports.bank-pdf',
                compact('banks'),
                $fontConfig
            );

            // Send email
            Mail::raw('Attached are the monthly reports.', function ($message) use ($files) {
                $message->to('marziulhaque08@gmail.com') // ✅ Replace with real admin email
                        ->subject('Monthly Reports');
                foreach ($files as $file) {
                    $message->attach($file, [
                        'as' => basename($file),
                        'mime' => 'application/pdf',
                    ]);
                }
            });

            $this->info("✅ Monthly reports generated and emailed successfully.");
        } catch (\Exception $e) {
            \Log::error('❌ Auto export failed: ' . $e->getMessage());
            $this->error('Export failed: ' . $e->getMessage());
        }
    }

    protected function exportAsPdf($filename, $view, $data, $config)
    {
        $html = view($view, $data)->render();
        $path = storage_path("app/exports/$filename");

        $mpdf = new Mpdf($config);
        $mpdf->WriteHTML($html);
        $mpdf->Output($path, \Mpdf\Output\Destination::FILE);

        return $path;
    }

    protected function mpdfConfig()
    {
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        return [
            'mode' => 'utf-8',
            'format' => 'A4',
            'fontDir' => array_merge($fontDirs, [storage_path('fonts')]),
            'fontdata' => $fontData + [
                'solaimanlipi' => [
                    'R' => 'SolaimanLipi.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'solaimanlipi',
            'tempDir' => storage_path('app/tmp'),
        ];
    }


}
