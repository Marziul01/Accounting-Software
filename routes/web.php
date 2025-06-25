<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Homecontroller;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeSubCategoryController;
use App\Http\Controllers\LiabilityController;
use Illuminate\Routing\Route as RoutingRoute;
use App\Http\Controllers\LiabilityCategoryController;
use App\Http\Controllers\LiabilitySubCategoryController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseSubCategoryController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetSubCategoryController;
use App\Http\Controllers\LiabilitySubSubCategoryController;
use App\Http\Controllers\AssetSubSubCategoryController;
use App\Http\Controllers\AssetTransactionController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\CategoryTableSettings;
use App\Http\Controllers\DetailedFinancialStatement;
use App\Http\Controllers\ExportData;
use App\Http\Controllers\FinancialStatement;
use App\Http\Controllers\IncomeExpenseStatementController;
use App\Http\Controllers\InvestmentCategoryController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentExpenseController;
use App\Http\Controllers\InvestmentIncomeController;
use App\Http\Controllers\InvestmentSubCategoryController;
use App\Http\Controllers\InvestmentTransactionController;
use App\Http\Controllers\LiabilityTransactionController;
use App\Http\Controllers\OccassionController;
use App\Http\Controllers\UserController;
use App\Models\AssetSubSubCategory;
use App\Models\ExpenseCategory;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Util\Exporter;

Route::get('/', [Homecontroller::class, 'index'])->name('home');

Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('login');
Route::post('/admin/login/auth', [AdminAuthController::class, 'authenticate'])->name('admin.authenticate');

// User-only area
Route::middleware(['auth', 'user.only'])->group(function () {

    Route::get('/dashboard', [Homecontroller::class, 'dashboard'])->name('user.dashboard');

});

// Admin-only area
Route::prefix('admin')->middleware(['auth:admin', 'admin.only'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('contact', ContactController::class);
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::resource('income', IncomeController::class);
    Route::resource('incomecategory', IncomeCategoryController::class);
    Route::resource('incomesubcategory', IncomeSubCategoryController::class);
    Route::resource('expense', ExpenseController::class);
    Route::resource('expensecategory', ExpenseCategoryController::class);
    Route::resource('expensesubcategory', ExpenseSubCategoryController::class);
    Route::resource('asset', AssetController::class);
    Route::resource('assetcategory', AssetCategoryController::class);
    Route::resource('assetsubcategory', AssetSubCategoryController::class);
    Route::resource('assetsubsubcategory', AssetSubSubCategoryController::class);
    Route::resource('liabilitysubcategory', LiabilitySubCategoryController::class);
    Route::resource('liabilitysubsubcategory', LiabilitySubSubCategoryController::class);
    Route::resource('liabilitycategory', LiabilityCategoryController::class);
    Route::resource('liability', LiabilityController::class);
    Route::resource('investment', InvestmentController::class);
    Route::resource('investmentcategory', InvestmentCategoryController::class);
    Route::resource('investmentsubcategory', InvestmentSubCategoryController::class);
    Route::resource('bankbook', BankAccountController::class);
    Route::get('/get-incomesubcategories/{category_id}', [IncomeSubCategoryController::class, 'getByCategory'])->name('get.incomesubcategories');
    Route::get('/get-expensesubcategories/{category_id}', [ExpenseSubCategoryController::class, 'getByCategory'])->name('get.expensesubcategories');
    Route::get('/get-investmentsubcategories/{category_id}', [InvestmentSubCategoryController::class, 'getByCategory'])->name('get.investmentsubcategories');
    Route::get('/get-assetsubcategories/{category_id}', [AssetSubCategoryController::class, 'getByCategory'])->name('get.assetsubcategories');
    Route::get('/get-liabilitysubcategories/{category_id}', [LiabilitySubCategoryController::class, 'getByCategory'])->name('get.liabilitysubcategories');
    Route::get('/get-currentassetsubcategories/{category_id}', [AssetSubSubCategoryController::class, 'getByCategory'])->name('get.currentassetsubcategories');
    Route::get('/get-currentliabilitysubcategories/{category_id}', [LiabilitySubSubCategoryController::class, 'getByCategory'])->name('get.currentliabilitysubcategories');
    Route::get('/assets/fixed-assets', [AssetController::class, 'fixed'])->name('assetFixed');
    Route::get('/liabilities/fixed-liability/', [LiabilityController::class, 'fixed'])->name('liabilityFixed');
    Route::resource('assettransaction', AssetTransactionController::class);
    Route::resource('liabilitytransaction', LiabilityTransactionController::class);
    Route::resource('banktransaction', BankTransactionController::class);
    Route::get('/admin/users/', [UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/admin/users/update/{user}', [UserController::class, 'update'])->name('user.update');
    Route::post('/admin/users/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::get('/incomes/report', [IncomeController::class, 'report'])->name('income.report');
    Route::get('/incomes/category/report', [IncomeController::class, 'IncomecategoryReport'])->name('admin.IncomecategoryReport');
    Route::get('/incomes/subcategory/report/{slug}', [IncomeController::class, 'IncomesubcategoryReport'])->name('admin.IncomesubcategoryReport');
    Route::get('/admin/income-report/filter/', [IncomeController::class, 'filter'])->name('admin.incomeReport.filter');
    Route::get('/full-income-report', [IncomeController::class, 'fullreport'])->name('admin.fullreport');

    Route::get('/expenses/report', [ExpenseController::class, 'report'])->name('expense.report');
    Route::get('/expenses/category/report', [ExpenseController::class, 'expensecategoryReport'])->name('admin.expensecategoryReport');
    Route::get('/expenses/subcategory/report/{slug}', [ExpenseController::class, 'expensesubcategoryReport'])->name('admin.expensesubcategoryReport');
    Route::get('/admin/expense-report/filter/', [ExpenseController::class, 'filter'])->name('admin.expenseReport.filter');
    Route::get('/full-expense-report', [ExpenseController::class, 'fullreport'])->name('admin.fullreport.exponse');
    
    Route::resource('investmenttransaction', InvestmentTransactionController::class);
    Route::get('/investments/report', [InvestmentController::class, 'report'])->name('investment.report');
    Route::get('/admin/investments/filter', [InvestmentController::class, 'filterInvestments'])->name('admin.filteredInvestments');
    Route::get('/admin/investment/subcategories/{category_id}', [InvestmentController::class, 'getSubcategories']);
    Route::get('/admin/report/category/{slug}', [InvestmentController::class, 'categoryReport'])->name('admin.report.category');
    Route::get('/admin/report/subcategory/{slug}', [InvestmentController::class, 'subcategoryReport'])->name('admin.report.subcategory');
    Route::get('/admin/report/investment/{slug}', [InvestmentController::class, 'singleInvestmentReport'])->name('admin.report.investment');
    Route::get('/admin/report/fullinvestment', [InvestmentController::class, 'fullReport'])->name('admin.report.fullinvestment');


    Route::get('/assets/report', [AssetController::class, 'report'])->name('assets.report');
    Route::get('/assets/get-subcategories/{id}', [AssetController::class, 'getSubcategories'])->name('subcategories.byCategory');
    Route::get('/assets/get-subsubcategories/{id}', [AssetController::class, 'getSubSubcategories'])->name('subsubcategories.bySubCategory');
    Route::get('/admin/assets/filter', [AssetController::class, 'filterasset'])->name('admin.filteredAssets');
    Route::get('/admin/report/assetcategory/{slug}', [AssetController::class, 'categoryReport'])->name('admin.asset.categoryReport');
    Route::get('/admin/report/assetsubcategory/{slug}', [AssetController::class, 'subcategoryReport'])->name('admin.asset.subcategoryReport');
    Route::get('/admin/report/assetsubsubcategory/{slug}', [AssetController::class, 'subsubcategoryReport'])->name('admin.asset.subsubcategoryReport');
    Route::get('/admin/report/assetreport/{slug}', [AssetController::class, 'singleassetReport'])->name('admin.asset.assetreport');
    Route::get('/admin/report/fullasset', [AssetController::class, 'fullAssetReport'])->name('admin.asset.fullreport');


    Route::get('/liabilities/report', [LiabilityController::class, 'report'])->name('liability.report');
    Route::get('/liabilities/get-subcategories/{id}', [LiabilityController::class, 'getSubcategories'])->name('liabilitysubcategories.byCategory');
    Route::get('/liabilities/get-subsubcategories/{id}', [LiabilityController::class, 'getSubSubcategories'])->name('liabilitysubsubcategories.bySubCategory');
    Route::get('/admin/liabilities/filter', [LiabilityController::class, 'filterliability'])->name('admin.filteredLiabilities');
    Route::get('/admin/report/liabilitycategory/{slug}', [LiabilityController::class, 'categoryReport'])->name('admin.liability.categoryReport');
    Route::get('/admin/report/liabilitysubcategory/{slug}', [LiabilityController::class, 'subcategoryReport'])->name('admin.liability.subcategoryReport');
    Route::get('/admin/report/liabilitysubsubcategory/{slug}', [LiabilityController::class, 'subsubcategoryReport'])->name('admin.liability.subsubcategoryReport');
    Route::get('/admin/report/liabilityreport/{slug}', [LiabilityController::class, 'singleliabilityReport'])->name('admin.liability.liabilityreport');
    Route::get('/admin/report/fullliability', [LiabilityController::class, 'fullLiabilityReport'])->name('admin.liability.fullreport');


    Route::get('/bankbooks/report', [BankAccountController::class, 'report'])->name('bankbook.report');
    Route::get('/bankbooks/filter', [BankAccountController::class, 'filter'])->name('admin.filteredBankTransactions');
    Route::get('/admin/bankbook/fullreport', [BankAccountController::class, 'bankbookreport'])->name('admin.report.bankaccount');

    Route::get('/admin/category/table/settings', [CategoryTableSettings::class, 'categoryTableSettings'])->name('admin.categoryTableSettings');
    Route::post('/admin/settings/updateCategoryField', [CategoryTableSettings::class, 'updateCategoryField'])->name('admin.settings.updateCategoryField');

    Route::get('/see/Asset/Trans/{slug}', [AssetTransactionController::class, 'index'])->name('seeAssetTrans');
    Route::get('/see/Liability/Trans/{slug}', [LiabilityTransactionController::class, 'index'])->name('seeLiabilityTrans');
    Route::get('/see/Investment/trans/{slug}', [InvestmentTransactionController::class, 'index'])->name('seeInvestmentTrans');
    Route::get('/see/Investment/income-expenses/{slug}', [InvestmentIncomeController::class, 'index'])->name('seeInvestmentsinex');

    Route::post('/investment-income', [InvestmentIncomeController::class, 'store'])->name('investment-income.store');
    Route::post('/investment-income/update/{id}', [InvestmentIncomeController::class, 'update'])->name('investment-income.update');
    Route::post('/investment-income/delete/{id}', [InvestmentIncomeController::class, 'destroy'])->name('investment-income.destroy');

    Route::post('/investment-expense', [InvestmentExpenseController::class, 'store'])->name('investment-expense.store');
    Route::post('/investment-expense/update/{id}', [InvestmentExpenseController::class, 'update'])->name('investment-expense.update');
    Route::post('/investment-expense/delete/{id}', [InvestmentExpenseController::class, 'destroy'])->name('investment-expense.destroy');


    Route::get('/Cash/flow/statement', [AccountsController::class, 'Cashflowstatement'])->name('Cash-flow-statement');
    Route::get('/income/expense/statement', [IncomeExpenseStatementController::class, 'incomeExpenseStatement'])->name('income-expense-statement');
    Route::get('/financial/statement', [FinancialStatement::class, 'financialStatement'])->name('financial-statement');
    Route::get('/detailed/financial/statement', [DetailedFinancialStatement::class, 'detailedfinancialStatement'])->name('detailed-financial-statement');

    Route::get('/export/data', [ExportData::class, 'ExportData'])->name('Export-Data');
    Route::get('/investments/export/{format}', [ExportData::class, 'exportInvestment'])->name('investments.export');
    Route::get('/income/export/{format}', [ExportData::class, 'exportincome'])->name('income.export');
    Route::get('/expense/export/{format}', [ExportData::class, 'exportexpense'])->name('expense.export');
    Route::get('/asset/export/{format}', [ExportData::class, 'exportasset'])->name('asset.export');
    Route::get('/liability/export/{format}', [ExportData::class, 'exportliability'])->name('liability.export');
    Route::get('/bank/export/{format}', [ExportData::class, 'exportbank'])->name('bank.export');
    Route::get('/expense/${id}/edit', [ExpenseController::class, 'editMdals'])->name('expenseeditMdals');


    Route::get('/occassions', [OccassionController::class, 'occassion'])->name('occassion');
    Route::post('/occassion/store/', [OccassionController::class, 'store'])->name('occassion.store');
    Route::post('/occassion/update/{id}', [OccassionController::class, 'update'])->name('occassion.update');
    Route::post('/occassion/delete/{id}', [OccassionController::class, 'destroy'])->name('occassion.destroy');

});

