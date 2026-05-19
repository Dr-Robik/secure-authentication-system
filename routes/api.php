<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::get('/verify-email', [AuthController::class, 'verifyEmail']);

    Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail']);

    Route::middleware('auth:api')->group(function () {

        Route::get('/me', [AuthController::class, 'me']);

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

Route::middleware('auth:api')->group(function () {

    Route::prefix('orders')->group(function () {

        Route::post('/', [OrderController::class, 'store']);

        Route::get('/', [OrderController::class, 'index']);

        Route::get('/{id}', [OrderController::class, 'show']);

        Route::patch('/{id}/cancel', [OrderController::class, 'cancel']);
    });

    Route::prefix('customer')
        ->middleware('role:customer')
        ->group(function () {

            Route::get('/dashboard', [CustomerController::class, 'dashboard']);

            Route::get('/orders', [CustomerController::class, 'myOrders']);
        });

    Route::prefix('driver')
        ->middleware('role:driver')
        ->group(function () {

            Route::get('/dashboard', [DriverController::class, 'dashboard']);

            Route::get('/orders', [DriverController::class, 'myOrders']);

            Route::patch('/orders/{id}/status', [DriverController::class, 'updateOrderStatus']);
        });

    Route::prefix('admin')
        ->middleware('role:admin')
        ->group(function () {

            Route::get('/dashboard', [AdminController::class, 'dashboard']);

            Route::get('/orders', [AdminController::class, 'orders']);

            Route::get('/orders/unassigned', [AdminController::class, 'unassignedOrders']);

            Route::patch('/orders/{id}/assign-driver', [AdminController::class, 'assignDriver']);

            Route::get('/drivers', [AdminController::class, 'drivers']);

            Route::post('/drivers', [AdminController::class, 'createDriver']);

            Route::delete('/drivers/{id}', [AdminController::class, 'deleteDriver']);
        });
});