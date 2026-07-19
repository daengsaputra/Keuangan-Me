<?php

use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FinanceController::class, 'dashboard'])->name('dashboard');
Route::get('/transaksi', [FinanceController::class, 'create'])->name('transactions.create');
Route::post('/transaksi', [FinanceController::class, 'store'])->name('transactions.store');
Route::delete('/transaksi/{transaction}', [FinanceController::class, 'destroy'])->name('transactions.destroy');
Route::get('/laporan/bulanan', [FinanceController::class, 'monthly'])->name('reports.monthly');
Route::get('/laporan/tahunan', [FinanceController::class, 'yearly'])->name('reports.yearly');
