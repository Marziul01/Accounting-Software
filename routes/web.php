<?php

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
use App\Http\Controllers\InvestmentCategoryController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentSubCategoryController;
use App\Http\Controllers\LiabilityTransactionController;
use App\Models\AssetSubSubCategory;
use App\Models\Investment;

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

});

