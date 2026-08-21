<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/medications/search', [MedicationController::class, 'search']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/export', [OrderController::class, 'export']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::get('/alerts', [AlertController::class, 'index']);
    Route::post('/alerts/send', [AlertController::class, 'send']);
});
