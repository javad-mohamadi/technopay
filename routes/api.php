<?php

use App\Http\Controllers\General\AuthController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::middleware('auth:api')->group(function () {
            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/user', [AuthController::class, 'user'])->name('user');
        });
    });

    Route::middleware('auth:api')->prefix('invoice')->group(function () {
        Route::post('{invoice}/request-payment', [InvoiceController::class, 'requestPayment'])
            ->name('invoice.request-invoice-payment');
        Route::post('pay', [InvoiceController::class, 'payInvoice'])->name('invoice.invoice-pay');
    });
});
