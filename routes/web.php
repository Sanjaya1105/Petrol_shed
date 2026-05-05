<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoleDashboardController;
use App\Http\Controllers\SLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/role', [RoleController::class, 'register'])->name('role.register');
Route::post('/role', [RoleController::class, 'store'])->name('role.store');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/s_login', [SLoginController::class, 'create'])->name('s_login');
Route::post('/s_login', [SLoginController::class, 'store'])->name('s_login.store');
Route::post('/s_logout', [SLoginController::class, 'destroy'])->middleware('auth')->name('s_logout');

Route::middleware(['auth', 'role:1'])->prefix('dev')->name('dev.')->group(function () {
    Route::get('/sales/staff-report.pdf', [RoleDashboardController::class, 'downloadAdminSalesStaffPdf'])->name('sales.staff.pdf');
    Route::get('/sales/pumps-report.pdf', [RoleDashboardController::class, 'downloadAdminSalesPumpsPdf'])->name('sales.pumps.pdf');
    Route::post('/categories', [RoleDashboardController::class, 'storeDevCategory'])->name('categories.store');
    Route::put('/categories/{category}', [RoleDashboardController::class, 'updateDevCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [RoleDashboardController::class, 'deleteDevCategory'])->name('categories.delete');
    Route::post('/tanks', [RoleDashboardController::class, 'storeDevTank'])->name('tanks.store');
    Route::put('/tanks/{tank}', [RoleDashboardController::class, 'updateDevTank'])->name('tanks.update');
    Route::delete('/tanks/{tank}', [RoleDashboardController::class, 'deleteDevTank'])->name('tanks.delete');
    Route::post('/pumps', [RoleDashboardController::class, 'storeDevPump'])->name('pumps.store');
    Route::put('/pumps/{pump}', [RoleDashboardController::class, 'updateDevPump'])->name('pumps.update');
    Route::delete('/pumps/{pump}', [RoleDashboardController::class, 'deleteDevPump'])->name('pumps.delete');
    Route::post('/prices', [RoleDashboardController::class, 'saveDevPrices'])->name('prices.save');
    Route::get('/{page}', [RoleDashboardController::class, 'showDev'])
        ->where('page', 'home|categories|pumps|tanks|price|sales|theme')
        ->name('show');
});

Route::middleware(['auth', 'role:2'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/sales/staff-report.pdf', [RoleDashboardController::class, 'downloadAdminSalesStaffPdf'])->name('sales.staff.pdf');
    Route::get('/sales/pumps-report.pdf', [RoleDashboardController::class, 'downloadAdminSalesPumpsPdf'])->name('sales.pumps.pdf');
    Route::post('/prices', [RoleDashboardController::class, 'saveAdminPrices'])->name('prices.save');
    Route::post('/staff', [RoleDashboardController::class, 'storeAdminStaff'])->name('staff.store');
    Route::delete('/staff/{staff}', [RoleDashboardController::class, 'deleteAdminStaff'])->name('staff.delete');
    Route::post('/pumps/{pump}/sale', [RoleDashboardController::class, 'saveAdminPumpSale'])->name('pumps.sale.save');
    Route::post('/pumps/sales/bulk', [RoleDashboardController::class, 'saveAdminPumpSalesBulk'])->name('pumps.sales.bulk');
    Route::get('/pumps/{pump}/sale-prefill', [RoleDashboardController::class, 'prefillAdminPumpSale'])->name('pumps.sale.prefill');
    Route::post('/tanks/{tank}/restock', [RoleDashboardController::class, 'restockAdminTank'])->name('tanks.restock');
    Route::post('/cash-rec', [RoleDashboardController::class, 'saveAdminCashRec'])->name('cash-rec.save');
    Route::post('/checks', [RoleDashboardController::class, 'saveAdminChecks'])->name('checks.save');
    Route::delete('/checks/{check}', [RoleDashboardController::class, 'deleteAdminChecks'])->name('checks.delete');
    Route::delete('/cash-rec/{cashCollection}', [RoleDashboardController::class, 'deleteAdminCashRec'])->name('cash-rec.delete');
    Route::post('/bill/company', [RoleDashboardController::class, 'saveAdminCompany'])->name('bill.company.save');
    Route::put('/bill/company/{company}', [RoleDashboardController::class, 'updateAdminCompany'])->name('bill.company.update');
    Route::delete('/bill/company/{company}', [RoleDashboardController::class, 'deleteAdminCompany'])->name('bill.company.delete');
    Route::get('/bill/company/{company}/report.pdf', [RoleDashboardController::class, 'downloadAdminCompanyBillsPdf'])->name('bill.company.report');
    Route::post('/bill/company/{company}/settle', [RoleDashboardController::class, 'settleAdminCompany'])->name('bill.company.settle');
    Route::post('/bill', [RoleDashboardController::class, 'saveAdminBill'])->name('bill.save');
    Route::get('/bill/category-price', [RoleDashboardController::class, 'getAdminBillCategoryPrice'])->name('bill.category-price');
    Route::post('/gas/details', [RoleDashboardController::class, 'saveAdminGasDetails'])->name('gas.details.save');
    Route::post('/oil/record', [RoleDashboardController::class, 'saveAdminOilRecord'])->name('oil.record.save');
    Route::put('/oil/record/{oilRecord}', [RoleDashboardController::class, 'updateAdminOilRecord'])->name('oil.record.update');
    Route::delete('/oil/record/{oilRecord}', [RoleDashboardController::class, 'deleteAdminOilRecord'])->name('oil.record.delete');
    Route::post('/slary', [RoleDashboardController::class, 'saveAdminSalaryAmount'])->name('slary.save');
    Route::post('/expense', [RoleDashboardController::class, 'saveAdminExpenseRecord'])->name('expense.save');
    Route::put('/expense/{expense}', [RoleDashboardController::class, 'updateAdminExpenseRecord'])->name('expense.update');
    Route::delete('/expense/{expense}', [RoleDashboardController::class, 'deleteAdminExpenseRecord'])->name('expense.delete');
    Route::get('/{page}', [RoleDashboardController::class, 'showAdmin'])
        ->where('page', 'home|categories|pumps|tanks|price|staff|sales|cash-rec|bill|gas|oil|slary|expense')
        ->name('show');
});

Route::middleware(['auth', 'role:3'])->prefix('data-entry')->name('data-entry.')->group(function () {
    Route::get('/sales/staff-report.pdf', [RoleDashboardController::class, 'downloadAdminSalesStaffPdf'])->name('sales.staff.pdf');
    Route::get('/sales/pumps-report.pdf', [RoleDashboardController::class, 'downloadAdminSalesPumpsPdf'])->name('sales.pumps.pdf');
    Route::post('/pumps/{pump}/sale', [RoleDashboardController::class, 'saveDataEntryPumpSale'])->name('pumps.sale.save');
    Route::post('/pumps/sales/bulk', [RoleDashboardController::class, 'saveDataEntryPumpSalesBulk'])->name('pumps.sales.bulk');
    Route::get('/pumps/{pump}/sale-prefill', [RoleDashboardController::class, 'prefillDataEntryPumpSale'])->name('pumps.sale.prefill');
    Route::post('/cash-rec', [RoleDashboardController::class, 'saveDataEntryCashRec'])->name('cash-rec.save');
    Route::post('/checks', [RoleDashboardController::class, 'saveDataEntryChecks'])->name('checks.save');
    Route::delete('/checks/{check}', [RoleDashboardController::class, 'deleteDataEntryChecks'])->name('checks.delete');
    Route::delete('/cash-rec/{cashCollection}', [RoleDashboardController::class, 'deleteDataEntryCashRec'])->name('cash-rec.delete');
    Route::post('/bill/company', [RoleDashboardController::class, 'saveDataEntryCompany'])->name('bill.company.save');
    Route::put('/bill/company/{company}', [RoleDashboardController::class, 'updateDataEntryCompany'])->name('bill.company.update');
    Route::delete('/bill/company/{company}', [RoleDashboardController::class, 'deleteDataEntryCompany'])->name('bill.company.delete');
    Route::get('/bill/company/{company}/report.pdf', [RoleDashboardController::class, 'downloadDataEntryCompanyBillsPdf'])->name('bill.company.report');
    Route::post('/bill/company/{company}/settle', [RoleDashboardController::class, 'settleDataEntryCompany'])->name('bill.company.settle');
    Route::post('/bill', [RoleDashboardController::class, 'saveDataEntryBill'])->name('bill.save');
    Route::get('/bill/category-price', [RoleDashboardController::class, 'getDataEntryBillCategoryPrice'])->name('bill.category-price');
    Route::get('/{page}', [RoleDashboardController::class, 'showDataEntry'])
        ->where('page', 'home|categories|pumps|tanks|price|sales|cash-rec|bill')
        ->name('show');
});
