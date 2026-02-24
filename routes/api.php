<?php

use App\Http\Controllers\Car\CarController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\RentalRequest\RentalRequestController;
use App\Http\Controllers\RentHistory\RentHistoryController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('jwt.auth')->group(function () {
        Route::get('/get-user', [AuthController::class, 'getUser']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Route::middleware('jwt')->group(function () {

 Route::middleware(['jwt.auth', CheckRole::class . ':admin'])->group(function () {
    Route::patch('cars/{car}/status', [CarController::class, 'changeStatus']);
    Route::patch('cars/{car}/license_plate', [CarController::class, 'changeLicensePlate']);
    Route::patch('cars/{car}/renter', [CarController::class, 'changeRenter']);
    Route::patch('cars/{car}/car_class', [CarController::class, 'changeCarClassAndRate']);
    Route::delete('cars/{car}', [CarController::class, 'destroy']);
 });

Route::middleware(['jwt.auth', CheckRole::class . ':user'])->group(function () {
    Route::post('clients', [ClientController::class, 'store']);
    Route::put('clients/{id}', [ClientController::class, 'update']);
    Route::get('clients/{id}', [ClientController::class, 'show']);

    Route::post('clients/{client}/license_scan', [ClientController::class, 'updateLicenseScan']);

    Route::patch('cars/{car}/renter', [CarController::class, 'changeRenter']);

    Route::get('cars/available', [CarController::class, 'available']);
});

 Route::middleware(['jwt.auth', CheckRole::class . ':admin'])->group(function () {
    Route::post('cars', [CarController::class, 'store']);
    Route::get('cars', [CarController::class, 'index']);
    Route::get('cars/{car}', [CarController::class, 'show']);

     Route::get('cars/available', [CarController::class, 'available']);

     Route::get('clients/export', [ClientController::class, 'export']);

     Route::post('clients', [ClientController::class, 'store']);
     Route::put('clients/{id}', [ClientController::class, 'update']);
     Route::get('clients/{id}', [ClientController::class, 'show']);
     Route::get('clients', [ClientController::class, 'index']);
     Route::delete('clients/{id}', [ClientController::class, 'destroy']);
     Route::post('clients/export', [ClientController::class, 'export']);

     Route::post('clients/{client}/license_scan', [ClientController::class, 'updateLicenseScan']);

     Route::apiResource('rent_histories', RentHistoryController::class)->except(['store', 'update']);

     Route::apiResource('rental_requests', RentalRequestController::class)->except(['update']);

     Route::post('rental_requests/{id}/approve', [RentalRequestController::class, 'approve']);
     Route::post('rental_requests/{id}/reject', [RentalRequestController::class, 'reject']);
     Route::post('rental_requests/{id}/complete', [RentalRequestController::class, 'complete']);
 });

Route::post('cars/{car}/photo', [CarController::class, 'uploadPhoto']);
Route::get('cars/{car}/photos', [CarController::class, 'getPhotos']);

Route::middleware(['jwt.auth', CheckRole::class . ':admin'])->group(function () {
    Route::put('cars/{car}', [CarController::class, 'update']);
});

Route::get('export/{type}', [ExportController::class, 'export']);

// });



