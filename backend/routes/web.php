<?php

// use App\Http\Controllers\UsagerController;
use App\Http\Controllers\VinController;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
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

//Vin
Route::get('/vins', [VinController::class, 'index']);
Route::get('/vins_saq', [VinController::class, 'getVinsSaq']);
Route::get('/import-vins', [VinController::class, 'store']);

//Usager
// Route::post('/usagers', [UsagerController::class, 'store']);
