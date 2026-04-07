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
Route::put('/usagers/{id}', [UsagerController::class, 'update']);
Route::get('/usagers/{id}', [UsagerController::class, 'show']);

Route::middleware('web')->group(function () {

    Route::post('/', [AuthController::class, 'store'])->name('connexion-usager');
    Route::post('/deconnexion', [AuthController::class, 'destroy'])->name('deconnexion');

    Route::get('/csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });

    Route::get('/afficher-usager', [UsagerController::class, 'afficherUsager']);
    Route::delete('/supprimer-usager', [UsagerController::class, 'supprimerUsager']);

    Route::get('/usager', function () {
        $user = auth()->user();
        return $user ? response()->json($user) : response()->json(null);
    });
});
