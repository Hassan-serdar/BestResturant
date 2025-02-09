<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

Route::get('/', function () {
    return response()->json(['msg=>hi']);
});

Route::prefix('menu')->controller(MenuController::class)->group(function () {
    Route::get('/show', 'index');
    Route::get('/show/{id}', 'show');
    Route::get('/edit/{id}', 'edit');

    Route::post('/create', 'store');
    Route::patch('/update/{id}', 'update');
    Route::delete('/delete/{id}', 'destroy');
});

