<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::group(['prefix' => '/api/auth'], function () {

    Route::post('/login', [UserController::class, 'login1']);
    Route::post('/register', [UserController::class, 'register1']);
    Route::get('/me', [UserController::class, 'me1']);
    Route::post('/out', [UserController::class, 'out1']);
    Route::get('/tokens', [UserController::class, 'tokens1']);
    Route::post('/out_all', [UserController::class, 'out_all1']);

});

