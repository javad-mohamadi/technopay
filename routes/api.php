<?php

use App\Http\Controllers\General\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
