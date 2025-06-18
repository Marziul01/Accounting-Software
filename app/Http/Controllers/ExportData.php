<?php

namespace App\Http\Controllers;

use App\Exports\AssetExport;
use App\Exports\BankExport;
use App\Exports\ExpenseExport;
use App\Exports\IncomeExport;
use App\Models\Investment;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use File;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InvestmentTransactionExport;
use App\Exports\LiabilityExport;
use App\Models\Asset;
use App\Models\BankAccount;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\IncomeCategory;
use App\Models\IncomeSubCategory;
use App\Models\Liability;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class ExportData extends Controller
{
    public static function ExportData(){
        return view('admin.accounts.export',[
            
        ]);
    }

    
    
    public function exportInvestment($format)
    {
        try {
            $investments = Investment::with(['investmentSubCategory.investmentCategory', 'transactions'])->get();

            $exportPath = storage_path('app/exports');
            // Clean old export directory and recreate
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0777, true);

            if ($format !== 'pdf') {
                $exportedFiles = [];

                foreach ($investments as $investment) {
                    if ($investment->transactions->count() > 0) {
                        $filename = Str::slug($investment->name) . "_transactions.$format";
                        $path = "exports/$filename";

                        // Store normally using default config (which uses 'private' disk)
                        $stored = Excel::store(new InvestmentTransactionExport($investment), $path);

                        if ($stored) {
                            $exportedFiles[] = Storage::path($path); // Get actual stored file path
                        }
                    }
                }

                if (empty($exportedFiles)) {
                    Session::flash('error', 'No transactions found to export.');
                    return redirect()->back();
                }

                // Zip all exported files
                $zipFile = storage_path("app/investment_exports.zip");
                $zip = new ZipArchive;

                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = $exportedFiles; 

                    if (empty($files)) {
                        Session::flash('error', 'No export files found to zip.');
                        return redirect()->back();
                    }

                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }

                    $zip->close();
                } else {
                    Session::flash('error', 'Failed to create zip file.');
                    return redirect()->back();
                }
            }
            else {
                // PDF Export using mPDF with Bengali font support

                $html = view('admin.exports.investments-pdf', compact('investments'))->render();

                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $customFontDir = storage_path('fonts'); // your SolaimanLipi font path

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

                $pdfPath = "$exportPath/investments.pdf";
                $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

                // Zip all exported files
                $zipFile = storage_path("app/investment_exports.zip");
                $zip = new ZipArchive;
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = File::files($exportPath);
                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }
                    $zip->close();
                }

                Session::flash('success', 'Export completed successfully!');
                return response()->download($zipFile)->deleteFileAfterSend(true);
            }

            
            

            Session::flash('success', 'Export completed successfully!');
            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Export failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }


    public function exportincome($format)
    {
        try {
            $categories = IncomeCategory::with(['incomeSubCategories.incomes'])->get();
            $subcategories = IncomeSubCategory::with(['incomeCategory','incomes'])->get();

            $exportPath = storage_path('app/exports');
            // Clean old export directory and recreate
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0777, true);

            if ($format !== 'pdf') {
                $exportedFiles = [];

                foreach ($subcategories as $subcategory) {
                    if ($subcategory->incomes->count() > 0) {
                        $filename = Str::slug($subcategory->slug) . "_incomes.$format";
                        $path = "exports/$filename";

                        // Store normally using default config (which uses 'private' disk)
                        $stored = Excel::store(new IncomeExport($subcategory), $path);

                        if ($stored) {
                            $exportedFiles[] = Storage::path($path); // Get actual stored file path
                        }
                    }
                }

                if (empty($exportedFiles)) {
                    Session::flash('error', 'No incomes found to export.');
                    return redirect()->back();
                }

                // Zip all exported files
                $zipFile = storage_path("app/incomes_exports.zip");
                $zip = new ZipArchive;

                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = $exportedFiles; 

                    if (empty($files)) {
                        Session::flash('error', 'No export files found to zip.');
                        return redirect()->back();
                    }

                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }

                    $zip->close();
                } else {
                    Session::flash('error', 'Failed to create zip file.');
                    return redirect()->back();
                }
            }
            else {
                // PDF Export using mPDF with Bengali font support

                $html = view('admin.exports.incomes-pdf', compact('categories'))->render();

                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $customFontDir = storage_path('fonts'); // your SolaimanLipi font path

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

                $pdfPath = "$exportPath/incomes.pdf";
                $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

                // Zip all exported files
                $zipFile = storage_path("app/incomes_exports.zip");
                $zip = new ZipArchive;
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = File::files($exportPath);
                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }
                    $zip->close();
                }

                Session::flash('success', 'Export completed successfully!');
                return response()->download($zipFile)->deleteFileAfterSend(true);
            }

            
            

            Session::flash('success', 'Export completed successfully!');
            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Export failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }


    public function exportexpense($format)
    {
        try {
            $categories = ExpenseCategory::with(['expenseSubCategories.expenses'])->get();
            $subcategories = ExpenseSubCategory::with(['expenseCategory','expenses'])->get();

            $exportPath = storage_path('app/exports');
            // Clean old export directory and recreate
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0777, true);

            if ($format !== 'pdf') {
                $exportedFiles = [];

                foreach ($subcategories as $subcategory) {
                    if ($subcategory->expenses->count() > 0) {
                        $filename = Str::slug($subcategory->slug) . "_expenses.$format";
                        $path = "exports/$filename";

                        // Store normally using default config (which uses 'private' disk)
                        $stored = Excel::store(new ExpenseExport($subcategory), $path);

                        if ($stored) {
                            $exportedFiles[] = Storage::path($path); // Get actual stored file path
                        }
                    }
                }

                if (empty($exportedFiles)) {
                    Session::flash('error', 'No expenses found to export.');
                    return redirect()->back();
                }

                // Zip all exported files
                $zipFile = storage_path("app/expenses_exports.zip");
                $zip = new ZipArchive;

                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = $exportedFiles; 

                    if (empty($files)) {
                        Session::flash('error', 'No export files found to zip.');
                        return redirect()->back();
                    }

                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }

                    $zip->close();
                } else {
                    Session::flash('error', 'Failed to create zip file.');
                    return redirect()->back();
                }
            }
            else {
                // PDF Export using mPDF with Bengali font support

                $html = view('admin.exports.expenses-pdf', compact('categories'))->render();

                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $customFontDir = storage_path('fonts'); // your SolaimanLipi font path

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

                $pdfPath = "$exportPath/expenses.pdf";
                $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

                // Zip all exported files
                $zipFile = storage_path("app/expenses_exports.zip");
                $zip = new ZipArchive;
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = File::files($exportPath);
                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }
                    $zip->close();
                }

                Session::flash('success', 'Export completed successfully!');
                return response()->download($zipFile)->deleteFileAfterSend(true);
            }

            
            

            Session::flash('success', 'Export completed successfully!');
            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Export failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function exportasset($format)
    {
        try {
            $assets = Asset::with(['subcategory.assetCategory' ,'transactions'])->get();

            $exportPath = storage_path('app/exports');
            // Clean old export directory and recreate
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0777, true);

            if ($format !== 'pdf') {
                $exportedFiles = [];

                foreach ($assets as $asset) {
                    if ($asset->transactions->count() > 0) {
                        $filename = Str::slug($asset->slug) . "_assets.$format";
                        $path = "exports/$filename";

                        // Store normally using default config (which uses 'private' disk)
                        $stored = Excel::store(new AssetExport($asset), $path);

                        if ($stored) {
                            $exportedFiles[] = Storage::path($path); // Get actual stored file path
                        }
                    }
                }

                if (empty($exportedFiles)) {
                    Session::flash('error', 'No assets found to export.');
                    return redirect()->back();
                }

                // Zip all exported files
                $zipFile = storage_path("app/assets_exports.zip");
                $zip = new ZipArchive;

                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = $exportedFiles; 

                    if (empty($files)) {
                        Session::flash('error', 'No export files found to zip.');
                        return redirect()->back();
                    }

                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }

                    $zip->close();
                } else {
                    Session::flash('error', 'Failed to create zip file.');
                    return redirect()->back();
                }
            }
            else {
                // PDF Export using mPDF with Bengali font support

                $html = view('admin.exports.asset-pdf', compact('assets'))->render();

                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $customFontDir = storage_path('fonts'); // your SolaimanLipi font path

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

                $pdfPath = "$exportPath/asset.pdf";
                $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

                // Zip all exported files
                $zipFile = storage_path("app/asset_exports.zip");
                $zip = new ZipArchive;
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = File::files($exportPath);
                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }
                    $zip->close();
                }

                Session::flash('success', 'Export completed successfully!');
                return response()->download($zipFile)->deleteFileAfterSend(true);
            }

            
            

            Session::flash('success', 'Export completed successfully!');
            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Export failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }


    public function exportliability($format)
    {
        try {
            $liabilities = Liability::with(['subcategory.liabilityCategory' ,'transactions'])->get();

            $exportPath = storage_path('app/exports');
            // Clean old export directory and recreate
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0777, true);

            if ($format !== 'pdf') {
                $exportedFiles = [];

                foreach ($liabilities as $liability) {
                    if ($liability->transactions->count() > 0) {
                        $filename = Str::slug($liability->slug) . "_liability.$format";
                        $path = "exports/$filename";

                        // Store normally using default config (which uses 'private' disk)
                        $stored = Excel::store(new LiabilityExport($liability), $path);

                        if ($stored) {
                            $exportedFiles[] = Storage::path($path); // Get actual stored file path
                        }
                    }
                }

                if (empty($exportedFiles)) {
                    Session::flash('error', 'No liability found to export.');
                    return redirect()->back();
                }

                // Zip all exported files
                $zipFile = storage_path("app/liability_exports.zip");
                $zip = new ZipArchive;

                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = $exportedFiles; 

                    if (empty($files)) {
                        Session::flash('error', 'No export files found to zip.');
                        return redirect()->back();
                    }

                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }

                    $zip->close();
                } else {
                    Session::flash('error', 'Failed to create zip file.');
                    return redirect()->back();
                }
            }
            else {
                // PDF Export using mPDF with Bengali font support

                $html = view('admin.exports.liability-pdf', compact('liabilities'))->render();

                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $customFontDir = storage_path('fonts'); // your SolaimanLipi font path

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

                $pdfPath = "$exportPath/liability.pdf";
                $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

                // Zip all exported files
                $zipFile = storage_path("app/liability_exports.zip");
                $zip = new ZipArchive;
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = File::files($exportPath);
                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }
                    $zip->close();
                }

                Session::flash('success', 'Export completed successfully!');
                return response()->download($zipFile)->deleteFileAfterSend(true);
            }

            
            

            Session::flash('success', 'Export completed successfully!');
            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Export failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function exportbank($format)
    {
        try {
            $banks = BankAccount::with(['transactions'])->get();

            $exportPath = storage_path('app/exports');
            // Clean old export directory and recreate
            if (File::exists($exportPath)) {
                File::deleteDirectory($exportPath);
            }
            File::makeDirectory($exportPath, 0777, true);

            if ($format !== 'pdf') {
                $exportedFiles = [];

                foreach ($banks as $bank) {
                    if ($bank->transactions->count() > 0) {
                        $filename = Str::slug($bank->bank_name) . "_transactions.$format";
                        $path = "exports/$filename";

                        // Store normally using default config (which uses 'private' disk)
                        $stored = Excel::store(new BankExport($bank), $path);

                        if ($stored) {
                            $exportedFiles[] = Storage::path($path); // Get actual stored file path
                        }
                    }
                }

                if (empty($exportedFiles)) {
                    Session::flash('error', 'No transactions found to export.');
                    return redirect()->back();
                }

                // Zip all exported files
                $zipFile = storage_path("app/bank_exports.zip");
                $zip = new ZipArchive;

                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = $exportedFiles; 

                    if (empty($files)) {
                        Session::flash('error', 'No export files found to zip.');
                        return redirect()->back();
                    }

                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }

                    $zip->close();
                } else {
                    Session::flash('error', 'Failed to create zip file.');
                    return redirect()->back();
                }
            }
            else {
                // PDF Export using mPDF with Bengali font support

                $html = view('admin.exports.bank-pdf', compact('banks'))->render();

                $defaultConfig = (new ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $customFontDir = storage_path('fonts'); // your SolaimanLipi font path

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

                $pdfPath = "$exportPath/banks.pdf";
                $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

                // Zip all exported files
                $zipFile = storage_path("app/bank_exports.zip");
                $zip = new ZipArchive;
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = File::files($exportPath);
                    foreach ($files as $file) {
                        $zip->addFile($file, basename($file));
                    }
                    $zip->close();
                }

                Session::flash('success', 'Export completed successfully!');
                return response()->download($zipFile)->deleteFileAfterSend(true);
            }

            
            

            Session::flash('success', 'Export completed successfully!');
            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Export failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

}
