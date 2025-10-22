<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data Routes
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('waste-categories', \App\Http\Controllers\WasteCategoryController::class);
    Route::resource('waste-prices', \App\Http\Controllers\WastePriceController::class)->only(['store', 'destroy']);

    // Transaction Routes
    Route::resource('deposits', \App\Http\Controllers\DepositController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('deposits/{deposit}/download-pdf', [\App\Http\Controllers\DepositController::class, 'downloadPdf'])->name('deposits.download-pdf');

    Route::resource('withdrawals', \App\Http\Controllers\WithdrawalController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('withdrawals/{withdrawal}/download-pdf', [\App\Http\Controllers\WithdrawalController::class, 'downloadPdf'])->name('withdrawals.download-pdf');

    // Report & Ledger Routes
    Route::get('reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
    Route::get('reports/download-pdf', [ReportController::class, 'downloadPdf'])->name('reports.download-pdf');
    Route::get('customers/{customer}/ledger', [LedgerController::class, 'show'])->name('ledger.show');
});

require __DIR__.'/auth.php';
