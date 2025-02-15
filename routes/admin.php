<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminOfferController;
use App\Http\Controllers\Admin\AdminManageUserController;

// Login for Admin (Guest only)
Route::middleware('guest')->group(function () {
    Route::post('/login', [AdminController::class, 'login']);
});

// Protected Admin Routes (Authenticated only)
Route::middleware('auth:admin-api')->group(function () {
    // Profile and Logout
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::get('/profile', [AdminController::class, 'profile']);
    Route::post('/register', [AdminController::class, 'register']);
    Route::get('/profile/edit', [AdminController::class, 'profile']);
    Route::post('/profile/edit', [AdminController::class, 'editprofile']);


    // Manage Users
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminManageUserController::class, 'index']);
        Route::get('/{id}', [AdminManageUserController::class, 'show']);
        Route::delete('/{id}', [AdminManageUserController::class, 'destroy']);
    });

    // Manage Menu
    Route::prefix('menu')->group(function () {
        Route::post('/create', [AdminMenuController::class, 'store']);
        Route::patch('/update/{id}', [AdminMenuController::class, 'update']);
        Route::delete('/delete/{id}', [AdminMenuController::class, 'destroy']);
    });

    // Manage Offers
    Route::prefix('offers')->group(function () {
        Route::post('/create', [AdminOfferController::class, 'store']);
        Route::patch('/update/{id}', [AdminOfferController::class, 'update']);
        Route::delete('/delete/{id}', [AdminOfferController::class, 'destroy']);
    });
});