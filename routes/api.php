<?php

use Illuminate\Support\Facades\{Route, Artisan};
use App\Http\Controllers\{
    UserController,
    Auth\AuthController,
};

Route::get('/', function () {
    Artisan::call('inspire');
    return Artisan::output();
});

Route::post('login', [AuthController::class, 'login'])->name('user.login');
Route::apiResource('users', UserController::class)->only('store');
