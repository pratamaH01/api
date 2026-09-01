<?php

use App\Http\Controllers\Api\CallbackController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\SaldoController;
use App\Http\Controllers\Api\TopupController;
use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Support\Facades\Route;

// Callback dari h2h, tidak butuh password customer
Route::any('/callback/topup', [CallbackController::class, 'handle']);

// Semua endpoint di bawah ini butuh header/field 'password' milik customer
Route::middleware('customer.password')->group(function () {
    Route::get('/saldo', [SaldoController::class, 'check']);
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/topup/{produk}', [TopupController::class, 'process']);
    Route::post('/ganti-password', [PasswordController::class, 'change']);
});
