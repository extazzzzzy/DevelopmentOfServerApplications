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

Route::get('/info/server', [InformationController::class, 'serverInfo']);
Route::get('/info/client', [InformationController::class, 'clientInfo']);
Route::get('/info/database', [InformationController::class, 'databaseInfo']);
