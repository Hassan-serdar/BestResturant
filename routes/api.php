<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\UserMenuController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminManageUserController;

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


    });
    Route::get('/showusers',[AdminManageUserController::class,'index'])->middleware('auth.admin');
    Route::get('/showuser/{id}',[AdminManageUserController::class,'show'])->middleware('auth.admin');
    Route::delete('/showuser/{id}',[AdminManageUserController::class,'destroy'])->middleware('auth.admin');

});

// User Routes
Route::prefix('user')->group(function () {
    // Login and Register for User (Guest only)
    Route::middleware('guest')->group(function () {
        Route::post('/login', [UserController::class, 'login']);
        Route::post('/register', [UserController::class, 'register']);
    });

    // Protected User Routes (Authenticated only)
    Route::middleware('auth.user')->group(function () {
        // Logout and Profile
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/profile', [UserController::class, 'profile']);
    });
});
    // Menu Controller (Public access)
    Route::get('/category/{categoryname}', [UserMenuController::class, 'showcategory']); // To show specific category 
    Route::prefix('menu')->controller(UserMenuController::class)->group(function () {
        Route::get('/show', 'index'); // Show all menu items
        Route::get('/show/{id}', 'show'); // Show a specific menu item
    }); 
    // Menu controller by admin
    Route::prefix('menu')->middleware('auth.admin')->controller(AdminMenuController::class)->group(function () {
        Route::get('/edit/{id}', 'edit'); //Show a page for edit a specific menu item
        Route::post('/create', 'store'); // Create a new menu item
        Route::patch('/update/{id}', 'update'); // Update a specific menu item
        Route::delete('/delete/{id}', 'destroy'); // Delete a specific menu item
    });