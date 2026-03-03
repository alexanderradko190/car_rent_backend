<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Car\CarController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\RentalRequest\RentalRequestController;
use App\Http\Controllers\RentHistory\RentHistoryController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('jwt.auth')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::get('/get-user', [AuthController::class, 'getUser']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

//    Admin
    Route::middleware(CheckRole::class . ':admin')->group(function () {

        Route::apiResource('rent_histories', RentHistoryController::class)
            ->except(['store', 'update']);

        Route::get('export/{type}', [ExportController::class, 'export']);
    });

//    Admin, Manager
    Route::middleware(CheckRole::class . ':admin|manager')->group(function () {

        Route::get('cars', [CarController::class, 'index']);
        Route::post('cars', [CarController::class, 'store']);
        Route::get('cars/{car}', [CarController::class, 'show']);
        Route::put('cars/{car}', [CarController::class, 'update']);
        Route::delete('cars/{car}', [CarController::class, 'destroy']);

        Route::patch('cars/{car}/status', [CarController::class, 'changeStatus']);
        Route::patch('cars/{car}/license_plate', [CarController::class, 'changeLicensePlate']);
        Route::patch('cars/{car}/car_class', [CarController::class, 'changeCarClassAndRate']);

        Route::get('clients', [ClientController::class, 'index']);
        Route::delete('clients/{id}', [ClientController::class, 'destroy']);
        Route::post('clients/export', [ClientController::class, 'export']);
        Route::get('clients/export', [ClientController::class, 'export']);

        Route::get('rental-requests', [RentalRequestController::class, 'index']);
        Route::post('rental-requests', [RentalRequestController::class, 'store']);
        Route::get('rental-requests/{id}', [RentalRequestController::class, 'show']);
        Route::post('rental-requests/{id}/approve', [RentalRequestController::class, 'approve']);
        Route::post('rental-requests/{id}/reject', [RentalRequestController::class, 'reject']);
        Route::post('rental-requests/{id}/complete', [RentalRequestController::class, 'complete']);
        Route::delete('rental-requests/{id}', [RentalRequestController::class, 'destroy']);

        Route::apiResource('rent_histories', RentHistoryController::class)
            ->except(['store', 'update']);
    });

//    Admin, Manager, User
    Route::middleware(CheckRole::class . ':admin|manager|user')->group(function () {

        Route::post('clients', [ClientController::class, 'store']);
        Route::put('clients/{id}', [ClientController::class, 'update']);
        Route::get('clients/{id}', [ClientController::class, 'show']);
        Route::post('clients/{id}/license_scan', [ClientController::class, 'updateLicenseScan']);

        Route::patch('cars/{car}/renter', [CarController::class, 'changeRenter']);
        Route::get('cars-available', [CarController::class, 'available']);

    });
});
