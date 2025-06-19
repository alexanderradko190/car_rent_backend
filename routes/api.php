<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;

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

//Route::middleware(['jwt.auth', 'role:user'])->group(function () {
    Route::put('cars/{car}', [CarController::class, 'update']);
//});

Route::apiResource('clients', ClientController::class);

Route::post('clients/{client}/license_scan', [ClientController::class, 'updateLicenseScan']);

