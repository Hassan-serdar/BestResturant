<?php

use Illuminate\Support\Facades\Route;

// Include Admin Routes
Route::prefix('admin')->group(function () {
    require __DIR__ . '/admin.php';
});

// Include User Routes
Route::prefix('user')->group(function () {
    require __DIR__ . '/user.php';
});

// Include Public Routes
require __DIR__ . '/guest.php';