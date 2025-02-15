<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserEmailVerification;

// Login and Register for User (Guest only)
Route::middleware('guest')->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::post('/register', [UserController::class, 'register']);
});

// Protected User Routes (Authenticated only)
Route::middleware('auth:user-api')->group(function () {
    // Logout and Profile
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::get('/profile/edit', [UserController::class, 'profile']);
    Route::post('/profile/edit', [UserController::class, 'editprofile']);

    // Email Verification
    Route::prefix('email')->group(function () {
        Route::get('/verify', [UserEmailVerification::class, 'verifyNotice'])
            ->name('verification.notice');
        Route::get('/verify/{id}/{hash}', [UserEmailVerification::class, 'verifyEmail'])
            ->middleware('signed')
            ->name('verification.verify');
        Route::post('/verification-notification', [UserEmailVerification::class, 'verifyHandler'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
    });
});