<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [TableController::class, 'index']);
Route::post('/tables/{id}/seat', [TableController::class, 'seatCustomer']);
Route::post('/tables/{id}/finish', [TableController::class, 'finishMeal']); // Added route
Route::post('/waitlist', [TableController::class, 'storeWaitlist']);
Route::post('/waitlist/{id}/seat', [TableController::class, 'seatWaitlistCustomer']);
Route::post('/tables/capacities', [TableController::class, 'updateCapacities']);
Route::post('/waitlist/{id}/cancel', [TableController::class, 'cancelWaitlist']);
Route::post('/tables/generate', [TableController::class, 'generateTables']);
Route::get('/database', [ReportController::class, 'index']);