<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/register', [UserController::class, 'register1']);
    Route::post('/login', [UserController::class, 'login1']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/out', [UserController::class, 'out1']);
        Route::get('/me', [UserController::class, 'me1']);
        Route::get('/tokens', [UserController::class, 'tokens1']);
        Route::post('/out_all', [UserController::class, 'out_all1']);
    });
});
