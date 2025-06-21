<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\RentalRequestController;
use App\Http\Controllers\RentHistoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;

Route::get('cars/export', [CarController::class, 'export']);

Route::get('cars/available', [CarController::class, 'available']);

//Route::middleware(['jwt.auth', 'role:admin'])->group(function () {
    Route::patch('cars/{car}/status', [CarController::class, 'changeStatus']);
    Route::patch('cars/{car}/license_plate', [CarController::class, 'changeLicensePlate']);
    Route::patch('cars/{car}/renter', [CarController::class, 'changeRenter']);
    Route::patch('cars/{car}/car_class', [CarController::class, 'changeCarClassAndRate']);
    Route::delete('cars/{car}', [CarController::class, 'destroy']);
//});

//Route::middleware(['jwt.auth', 'role:manager'])->group(function () {
    Route::post('cars', [CarController::class, 'store']);
    Route::get('cars', [CarController::class, 'index']);
    Route::get('cars/{car}', [CarController::class, 'show']);
//});

Route::post('cars/{car}/photo', [CarController::class, 'uploadPhoto']);
Route::get('cars/{car}/photos', [CarController::class, 'getPhotos']);

//Route::middleware(['jwt.auth', 'role:user'])->group(function () {
    Route::put('cars/{car}', [CarController::class, 'update']);
//});

Route::get('clients/export', [ClientController::class, 'export']);

Route::apiResource('clients', ClientController::class);

Route::post('clients/{client}/license_scan', [ClientController::class, 'updateLicenseScan']);

Route::get('rent_histories/export', [RentHistoryController::class, 'export']);

//Route::get('rent_histories/export', [RentHistoryController::class, 'export']);
//Route::post('rent_histories/import', [RentHistoryController::class, 'import']);
Route::apiResource('rent_histories', RentHistoryController::class)->except(['store', 'update']);

Route::apiResource('rental_requests', RentalRequestController::class)->except(['update']);

Route::post('rental_requests/{id}/approve', [RentalRequestController::class, 'approve']);
Route::post('rental_requests/{id}/reject', [RentalRequestController::class, 'reject']);
Route::post('rental_requests/{id}/complete', [RentalRequestController::class, 'complete']);



