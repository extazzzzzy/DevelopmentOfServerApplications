<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/api/auth'], function () {

    Route::post('/login', [RENAME::class, '']);
    Route::post('/register', [RENAME::class, '']);
    Route::get('/me', [RENAME::class, '']);
    Route::post('/out', [RENAME::class, '']);
    Route::get('/tokens', [RENAME::class, '']);
    Route::post('/out_all', [RENAME::class, '']);

});

