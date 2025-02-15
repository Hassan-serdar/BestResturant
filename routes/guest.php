<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserMenuController;
use App\Http\Controllers\User\UserOfferController;

// Menu Routes (Public access)
Route::prefix('menu')->controller(UserMenuController::class)->group(function () {
    Route::get('/show', 'index'); // Show all menu items
    Route::get('/show/{id}', 'show'); // Show a specific menu item
    Route::get('/category/{categoryname}', 'showcategory'); // Show specific category
});

// Offer Routes (Public access)
Route::prefix('offers')->controller(UserOfferController::class)->group(function () {
    Route::get('/show', 'index'); // Show all offer items
    Route::get('/show/{id}', 'show'); // Show a specific offer item
});