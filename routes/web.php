<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InformationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['prefix' => '/info'], function () {

Route::get('/server', [InformationController::class, 'serverInfo']);
Route::get('/client', [InformationController::class, 'clientInfo']);
Route::get('/database', [InformationController::class, 'databaseInfo']);

});

