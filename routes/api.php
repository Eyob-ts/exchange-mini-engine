<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WalletController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'me']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
});
