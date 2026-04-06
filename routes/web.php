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
        ->where('page', 'home|categories|pumps|tanks|price')
        ->name('show');
});

Route::middleware(['auth', 'role:2'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/prices', [RoleDashboardController::class, 'saveAdminPrices'])->name('prices.save');
    Route::post('/staff', [RoleDashboardController::class, 'storeAdminStaff'])->name('staff.store');
    Route::delete('/staff/{staff}', [RoleDashboardController::class, 'deleteAdminStaff'])->name('staff.delete');
    Route::get('/{page}', [RoleDashboardController::class, 'showAdmin'])
        ->where('page', 'home|categories|pumps|tanks|price|staff')
        ->name('show');
});

Route::middleware(['auth', 'role:3'])->prefix('data-entry')->name('data-entry.')->group(function () {
    Route::get('/{page}', [RoleDashboardController::class, 'showDataEntry'])
        ->where('page', 'home|categories|pumps|tanks|price')
        ->name('show');
});
