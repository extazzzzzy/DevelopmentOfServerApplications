<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::middleware('guest:sanctum') -> post('/register', [UserController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/out', [UserController::class, 'out']);
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/tokens', [UserController::class, 'tokens']);
        Route::post('/out_all', [UserController::class, 'out_all']);
    });
});


