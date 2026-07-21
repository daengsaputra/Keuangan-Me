<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [FinanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/transaksi', [FinanceController::class, 'create'])->name('transactions.create');
    Route::post('/transaksi', [FinanceController::class, 'store'])->name('transactions.store');
    Route::delete('/transaksi/{transaction}', [FinanceController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/laporan/bulanan', [FinanceController::class, 'monthly'])->name('reports.monthly');
    Route::get('/laporan/tahunan', [FinanceController::class, 'yearly'])->name('reports.yearly');
});
