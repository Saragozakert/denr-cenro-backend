<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\UserAuthenticationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FuelRequestController;
use App\Http\Controllers\RequestingPartyController;
use App\Http\Controllers\TripTicketController;

// Public routes
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Protected admin routes
Route::middleware(['auth:admin'])->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin/check-auth', [AdminAuthController::class, 'checkAuth']);

    Route::get('/admin/trip-tickets', [TripTicketController::class, 'getAll']);
    Route::get('/admin/trip-tickets/{fuelRequestId}', [TripTicketController::class, 'getByFuelRequest']);

    // User management routes
    Route::get('/admin/users', [UserAuthenticationController::class, 'index']);
    Route::delete('/admin/users/{id}', [UserAuthenticationController::class, 'destroy']);
    Route::put('/admin/users/{id}/status', [UserAuthenticationController::class, 'updateStatus']);

    // Unit management routes
    Route::get('/admin/units', [UnitController::class, 'index']);
    Route::post('/admin/units', [UnitController::class, 'store']);
    Route::delete('/admin/units/{id}', [UnitController::class, 'destroy']);
    Route::put('/admin/units/{id}', [UnitController::class, 'update']);

    // Employee management routes
    Route::get('/admin/employees', [EmployeeController::class, 'index']);
    Route::post('/admin/employees', [EmployeeController::class, 'store']);
    Route::put('/admin/employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('/admin/employees/{id}', [EmployeeController::class, 'destroy']);

    // Fuel request management routes (Admin)
    Route::get('/admin/fuel-requests', [FuelRequestController::class, 'index']);
    Route::put('/admin/fuel-requests/{id}/status', [FuelRequestController::class, 'updateStatus']);
    Route::put('/admin/fuel-requests/{id}/amount', [FuelRequestController::class, 'updateAmount']);

    // Requesting Party management routes
    Route::get('/admin/requesting-parties', [RequestingPartyController::class, 'index']);
    Route::post('/admin/requesting-parties', [RequestingPartyController::class, 'store']);
    Route::put('/admin/requesting-parties/{id}', [RequestingPartyController::class, 'update']);
    Route::delete('/admin/requesting-parties/{id}', [RequestingPartyController::class, 'destroy']);
    Route::get('/admin/trip-ticket-statuses', [TripTicketController::class, 'getAllStatuses']);

     Route::get('/admin/trip-tickets-admin', [TripTicketController::class, 'getAdminTripTickets']);
    Route::put('/admin/trip-tickets/{id}/status', [TripTicketController::class, 'updateStatus']);
});

// User routes
Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthenticationController::class, 'register']);
    Route::post('/login', [UserAuthenticationController::class, 'login']);

    Route::get('/units', [UnitController::class, 'index']);
    Route::get('/employees', [EmployeeController::class, 'indexForUsers']);
    Route::get('/requesting-parties', [RequestingPartyController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserAuthenticationController::class, 'logout']);
        Route::get('/check-auth', [UserAuthenticationController::class, 'checkAuth']);

        // Trip Ticket routes (User) - FIXED: Add /user prefix
        Route::post('/trip-tickets', [TripTicketController::class, 'store']);
        Route::get('/trip-tickets/{fuelRequestId}', [TripTicketController::class, 'getByFuelRequest']);

        Route::get('/user-trip-tickets', [TripTicketController::class, 'getUserTripTickets']);
         Route::get('/fuel-requests-with-trip-status', [TripTicketController::class, 'getFuelRequestsWithTripTicketStatus']);

        // Fuel request routes (User)
        Route::post('/fuel-requests', [FuelRequestController::class, 'store']);
        Route::get('/fuel-requests', [FuelRequestController::class, 'userRequests']);
        Route::delete('/fuel-requests/{id}', [FuelRequestController::class, 'destroy']);
    });
});