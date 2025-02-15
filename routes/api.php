<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\GuestContactController;
use App\Http\Controllers\User\UserMenuController;
use App\Http\Controllers\user\UserOfferController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\admin\AdminOfferController;
use App\Http\Controllers\User\UserContactController;
use App\Http\Controllers\user\UserEmailVerification;
use App\Http\Controllers\Admin\AdminManageUserController;

Route::prefix('admin')->group(function () {

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
    });

    // Manage Users (Authenticated Admin only)
    Route::middleware('auth.admin')->group(function () {
        Route::get('/showusers', [AdminManageUserController::class, 'index']);
        Route::get('/showuser/{id}', [AdminManageUserController::class, 'show']);
        Route::delete('/showuser/{id}', [AdminManageUserController::class, 'destroy']);
    });
});


Route::prefix('user')->group(function () {

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
    });

    // Email Verification Routes
    Route::middleware('auth:user-api')->group(function () {
        Route::get('/email/verify', [UserEmailVerification::class, 'verifyNotice'])
            ->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', [UserEmailVerification::class, 'verifyEmail'])
            ->middleware(['signed'])
            ->name('verification.verify');

        Route::post('/email/verification-notification', [UserEmailVerification::class, 'verifyHandler'])
            ->middleware(['throttle:6,1'])
            ->name('verification.send');
    });
    Route::middleware('auth:user-api')->group(function () {
        Route::get('/contactus', [UserContactController::class,'index']); // Show Contact us page
        Route::Post('/contactus', [UserContactController::class,'store']); // send a message from Contact us page
    });
});

// Menu Routes (Public access)
Route::get('/category/{categoryname}', [UserMenuController::class, 'showcategory']); // To show specific category
Route::prefix('menu')->controller(UserMenuController::class)->group(function () {
    Route::get('/show', 'index'); // Show all menu items
    Route::get('/show/{id}', 'show'); // Show a specific menu item
});
// Contact us Routes (Public access)
Route::get('/contactus', [GuestContactController::class,'index']); // Show Contact us page
Route::Post('/contactus', [GuestContactController::class,'store']); // send a message from Contact us page

// Offer Routes (Public access)
Route::prefix('offers')->controller(UserOfferController::class)->group(function () {
    Route::get('/show', 'index'); // Show all offer items
    Route::get('/show/{id}', 'show'); // Show a specific offer item
});

// Menu Management Routes (Admin only)
Route::prefix('menu')->middleware('auth.admin')->controller(AdminMenuController::class)->group(function () {
    Route::get('/edit/{id}', 'edit'); // Show a page for edit a specific menu item
    Route::post('/create', 'store'); // Create a new menu item
    Route::patch('/update/{id}', 'update'); // Update a specific menu item
    Route::delete('/delete/{id}', 'destroy'); // Delete a specific menu item
});

// Offer Management Routes (Admin only)
Route::prefix('offers')->middleware('auth.admin')->controller(AdminOfferController::class)->group(function () {
    Route::get('/edit/{id}', 'edit'); // Show a page for edit a specific offer item
    Route::post('/create', 'store'); // Create a new offer item
    Route::patch('/update/{id}', 'update'); // Update a specific offer item
    Route::delete('/delete/{id}', 'destroy'); // Delete a specific offer item
});