<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SubscriptionController;

// Modul Service
Route::patch('services/{service}/activate', [ServiceController::class, 'activate']);
Route::patch('services/{service}/deactivate', [ServiceController::class, 'deactivate']);
Route::apiResource('services', ServiceController::class);

// Modul Customer
Route::patch('customers/{customer}/activate', [CustomerController::class, 'activate']);
Route::patch('customers/{customer}/deactivate', [CustomerController::class, 'deactivate']);
Route::apiResource('customers', CustomerController::class);

// Modul Subscription
Route::apiResource('subscriptions', SubscriptionController::class);