<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\AuthController,
    TransactionController,
    TransactionTokenController,
};

Route::get('me', [AuthController::class, 'me'])->name('me');
Route::post('transactions/token', TransactionTokenController::class)->name('transactions.token');
Route::put('transactions/cancel/{transaction}', [TransactionController::class, 'cancel'])->name('transactions.cancel');
Route::apiResource('transactions', TransactionController::class)->only('index', 'store');
