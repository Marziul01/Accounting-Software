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
use App\Http\Controllers\BankScheduleControllerController;
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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserController;
use App\Models\AssetSubSubCategory;
use App\Models\ExpenseCategory;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Util\Exporter;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SendSMSEmailController;
use App\Http\Controllers\TransactionHistory;

Route::get('/', [Homecontroller::class, 'index'])->name('home');


Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('login');
Route::get('/admin/forget/pass', [AdminAuthController::class, 'forgotPass'])->name('forgotPass');
Route::post('/admin/login/auth', [AdminAuthController::class, 'authenticate'])->name('admin.authenticate');

Route::post('/password/send-code', [AdminAuthController::class, 'sendResetCode'])->name('password.send.code');
Route::post('/password/verify-code', [AdminAuthController::class, 'verifyCode'])->name('password.verify.code');
Route::post('/password/reset', [AdminAuthController::class, 'updatePassword'])->name('password.reset.update');

Route::get('/guest/report/assetreport/{slug}', [AssetController::class, 'singleassetReport'])->name('admin.asset.assetreport.guest');
Route::get('/guest/report/liabilityreport/{slug}', [LiabilityController::class, 'singleliabilityReport'])->name('admin.liability.liabilityreport.guest');
// User-only area
Route::middleware(['auth', 'user.only'])->group(function () {

    Route::get('/dashboard', [Homecontroller::class, 'dashboard'])->name('user.dashboard');

});

Route::get('/get-time', function () {
    return response()->json([
        'time' => now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s')
    ]);
})->name('get.time');

Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

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

    Route::get('/emailsmsTemplate', [TemplateController::class, 'index'])->name('emailsmsTemplate');
    Route::post('/smsTemplate/update/{id}', [TemplateController::class, 'update'])->name('smsTemplate.update');
    Route::post('/emailTemplate/update/{id}', [TemplateController::class, 'emailTemplate'])->name('emailTemplate.update');

    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/site/settings', [ProfileController::class, 'siteSettings'])->name('site.settings');
    Route::get('/home/settings', [ProfileController::class, 'homesettings'])->name('home.settings');
    Route::post('/admin/profile/update', [ProfileController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/admin/site-settings/update', [ProfileController::class, 'update'])->name('admin.site-settings.update');
    Route::post('/admin/home-settings/update', [ProfileController::class, 'homeupdate'])->name('admin.home-settings.update');

    Route::get('/admin/income/modal', [DashboardController::class, 'Incomemodal'])->name('admin.income.modal');
    Route::get('/admin/expense/modal', [DashboardController::class, 'Expensemodal'])->name('admin.expense.modal');
    
    Route::get('/admin/currentasset/modal', [DashboardController::class, 'assetmodal'])->name('admin.currentasset.modal');
    Route::get('/admin/currentassettransaction/modal', [DashboardController::class, 'currentassettransaction'])->name('admin.currentassettransaction.modal');
    Route::get('/admin/fixedasset/modal', [DashboardController::class, 'fixedasset'])->name('admin.fixedasset.modal');
    Route::get('/admin/fixedassettransaction/modal', [DashboardController::class, 'fixedassettransaction'])->name('admin.fixedassettransaction.modal');

    Route::get('/admin/current/liability/modal', [DashboardController::class, 'currentliability'])->name('admin.currentliability.modal');
    Route::get('/admin/currentliabilitytransaction/modal', [DashboardController::class, 'currentliabilitytransaction'])->name('admin.currentliabilitytransaction.modal');
    Route::get('/admin/fixedliability/modal', [DashboardController::class, 'fixedliability'])->name('admin.fixedliability.modal');
    Route::get('/admin/fixedliabilitytransaction/modal', [DashboardController::class, 'fixedliabilitytransaction'])->name('admin.fixedliabilitytransaction.modal');

    Route::get('/admin/investment/modal', [DashboardController::class, 'investmentmodal'])->name('admin.investment.modal');
    Route::get('/admin/investmenttransaction/modal', [DashboardController::class, 'investmenttransaction'])->name('admin.investmenttransaction.modal');
    Route::get('/admin/investment/income/modal', [DashboardController::class, 'investmentincome'])->name('admin.investmentincome.modal');
    Route::get('/admin/investment/expense/modal', [DashboardController::class, 'investmentexpense'])->name('admin.investmentloss.modal');
    Route::get('/admin/bank/modal', [DashboardController::class, 'bankbook'])->name('admin.bankbook.modal');

    Route::get('/notifications/all', [NotificationController::class, 'all'])->name('notifications.all');
    Route::resource('bankschedule', BankScheduleControllerController::class);
    Route::get('/all/transactions/history', [TransactionHistory::class, 'index'])->name('transaction.history');
    Route::get('/send/SMS/Email', [SendSMSEmailController::class, 'sendSMSEmail'])->name('sendSMSEmail');

    Route::get('/admin/edit/investment/transaction/modal/{id}', [TransactionHistory::class, 'editinvestmenttransaction'])->name('admin.editinvestmenttransaction.modal');
    Route::get('/admin/edit/income/modal/{id}', [TransactionHistory::class, 'editincome'])->name('admin.editincome.modal');
    Route::get('/admin/edit/expense/modal/{id}', [TransactionHistory::class, 'editexpense'])->name('admin.editexpense.modal');
    Route::get('/admin/edit/asset/transaction/modal/{id}', [TransactionHistory::class, 'editassettransaction'])->name('admin.editassettransaction.modal');
    Route::get('/admin/edit/liability/transaction/modal/{id}', [TransactionHistory::class, 'editliabilitytransaction'])->name('admin.editliabilitytransaction.modal');
    Route::get('/admin/edit/bank/transaction/modal/{id}', [TransactionHistory::class, 'editbanktransaction'])->name('admin.editbanktransaction.modal');

    Route::post('/send/SMS', [SendSMSEmailController::class, 'sendSMS'])->name('admin.send.sms');
    Route::post('/send/Email', [SendSMSEmailController::class, 'sendEmail'])->name('admin.send.email');


});

