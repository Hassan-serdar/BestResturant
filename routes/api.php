<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\user\CartController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\GuestContactController;
use App\Http\Controllers\User\UserMenuController;
use App\Http\Controllers\user\UserOfferController;
use App\Http\Controllers\User\UserOrderController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\admin\AdminOfferController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\User\UserContactController;
use App\Http\Controllers\user\UserEmailVerification;
use App\Http\Controllers\admin\AdminContactusController;
use App\Http\Controllers\Admin\AdminManageUserController;
use App\Http\Controllers\user\UserDiscountCodeController;
use App\Http\Controllers\admin\AdminDiscountCodeController;

// Admin Routes
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
        Route::get('/profile/edit', [AdminController::class, 'profile']);
        Route::post('/profile/edit', [AdminController::class, 'editprofile']);

        
    });

    // Manage Users (Authenticated Admin only)
    Route::middleware('auth.admin')->group(function () {
        Route::get('/showusers', [AdminManageUserController::class, 'index']);
        Route::get('/showuser/{id}', [AdminManageUserController::class, 'show']);
        Route::delete('/showuser/{id}', [AdminManageUserController::class, 'destroy']);
    });

    // Manage Discount Codes (Authenticated Admin only)
    Route::middleware('auth.admin')->group(function () {
        Route::get('/showcode', [AdminDiscountCodeController::class, 'index']);
        Route::Post('/createcode', [AdminDiscountCodeController::class, 'store']);
        Route::get('/editcode/{id}', [AdminDiscountCodeController::class, 'show']);
        Route::patch('/editcode/{id}', [AdminDiscountCodeController::class, 'update']);
        Route::delete('/editcode/{id}', [AdminDiscountCodeController::class, 'destroy']);
    });

    // Manage Contact Us Messages (Authenticated Admin only)
    Route::get('/showcontactus', [AdminContactusController::class, 'index'])->middleware('auth.admin');

    // Manage Orders (Authenticated Admin only)
    Route::middleware('auth.admin')->group(function () {
        Route::get('/showorder', [AdminOrderController::class, 'index']);
        Route::patch('/orders/{id}', [AdminOrderController::class, 'update']);
    });
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

// User Routes

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

    // Contact Us Routes (Authenticated User only)
    Route::middleware('auth.user')->group(function () {
        Route::get('/contactus', [UserContactController::class, 'index']); // Show Contact us page
        Route::Post('/contactus', [UserContactController::class, 'store']); // Send a message from Contact us page
    });

    // Discount Code Routes (Authenticated User only)
    Route::get('/showcode', [UserDiscountCodeController::class, 'index'])->middleware('auth.user');

    // Cart Management Routes (Authenticated User only)
    Route::middleware('auth.user')->group(function () {
        Route::post('/cart/add-item/{id}', [CartController::class, 'addItem']);
        Route::post('/cart/add-offer/{id}', [CartController::class, 'addOffer']);
        Route::post('/cart/apply-discount', [CartController::class, 'applyDiscount']);
        Route::post('/cart/confirm-order', [CartController::class, 'confirmOrder']); // Confirm cart
        Route::get('/cart', [CartController::class, 'getCart']);
        Route::post('/cart/delete-item/{id}', [CartController::class, 'removeItem']);
        Route::delete('/cart', [CartController::class, 'clearCart']);
    });

    // Order Management Routes (Authenticated User only)
    Route::get('/my-orders', [UserOrderController::class, 'myOrders'])->middleware('auth.user');
});

// ------------------------------
// Public Routes (No Authentication Required)
// ------------------------------

// Menu Routes (Public access)
Route::get('/category/{categoryname}', [UserMenuController::class, 'showcategory']); // Show specific category
Route::prefix('menu')->controller(UserMenuController::class)->group(function () {
    Route::get('/show', 'index'); // Show all menu items
    Route::get('/show/{id}', 'show'); // Show a specific menu item
});

// Contact Us Routes (Public access)
Route::get('/contactus', [GuestContactController::class, 'index']); // Show Contact us page
Route::Post('/contactus', [GuestContactController::class, 'store']); // Send a message from Contact us page

// Offer Routes (Public access)
Route::prefix('offers')->controller(UserOfferController::class)->group(function () {
    Route::get('/show', 'index'); // Show all offer items
    Route::get('/show/{id}', 'show'); // Show a specific offer item
});

// Home and About Us Routes (Public access)
Route::get('/home', [MainController::class, 'index']); // Show Home page
Route::get('/aboutus', [MainController::class, 'about']); // Show About us page