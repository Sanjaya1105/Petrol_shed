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

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/dev/home', [RoleDashboardController::class, 'dev'])->name('dev.dashboard');
});

Route::middleware(['auth', 'role:2'])->group(function () {
    Route::get('/admin/home', [RoleDashboardController::class, 'admin'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:3'])->group(function () {
    Route::get('/data-entry/home', [RoleDashboardController::class, 'dataEntry'])->name('data-entry.dashboard');
});
