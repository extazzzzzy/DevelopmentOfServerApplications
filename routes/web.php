<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::group(['prefix' => '/api/auth'], function () {

    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/out', [UserController::class, 'out']);
    Route::get('/tokens', [UserController::class, 'tokens']);
    Route::post('/out_all', [UserController::class, 'out_all']);

});

