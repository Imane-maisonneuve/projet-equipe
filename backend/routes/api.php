<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsagerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/usagers', [UsagerController::class, 'store']);

Route::middleware('web')->group(function () {
    Route::post('/connexion-usager', [AuthController::class, 'store'])->name('connexion-usager');
    Route::post('/deconnexion', [AuthController::class, 'destroy'])->name('deconnexion');

    Route::get('/csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });
});


