<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Protected Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [TableController::class, 'index']);
    Route::post('/tables/{id}/seat', [TableController::class, 'seatCustomer']);
    Route::post('/tables/{id}/finish', [TableController::class, 'finishMeal']);
    Route::post('/waitlist', [TableController::class, 'storeWaitlist']);
    Route::post('/waitlist/{id}/seat', [TableController::class, 'seatWaitlistCustomer']);
    Route::post('/tables/capacities', [TableController::class, 'updateCapacities']);
    Route::post('/waitlist/{id}/cancel', [TableController::class, 'cancelWaitlist']);
    Route::get('/database', [ReportController::class, 'index']);
    Route::post('/tables/generate', [TableController::class, 'generateTables']);
});