<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CellierController;
use App\Http\Controllers\CellierVinController;
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

// route publique pour la création d'usager
Route::middleware('web')->group(function () {
    //Route pour la connexion et deconnexion
    Route::post('/', [AuthController::class, 'store'])->name('connexion-usager');
    Route::post('/deconnexion', [AuthController::class, 'destroy'])->name('deconnexion');
    Route::get('/csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });

    // Route pour verifier si un usager est connecter
    Route::get('/usagerConnecter', function () {
        $usager = auth()->user();
        return $usager ? response()->json($usager) : response()->json(null);
    });
});

// Routes protégées par l'authentification
Route::middleware(['web', 'auth'])->group(function () {
    // Routes pour la gestion des usagers
    Route::get('/afficher-usager', [UsagerController::class, 'afficherUsager']);
    Route::delete('/supprimer-usager', [UsagerController::class, 'supprimerUsager']);
    Route::put('/usagers/{id}', [UsagerController::class, 'update']);
    Route::get('/usagers/{id}', [UsagerController::class, 'show']);
    // Routes pour la gestion des celliers
    Route::post('/creer-cellier', [CellierController::class, 'store']);
    Route::put('/modifier-cellier/{id}', [CellierController::class, 'update']);
    Route::get('/celliers', [CellierController::class, 'index']);
    Route::delete('/supprimer-cellier/{cellier}', [CellierController::class, 'destroy']);
    Route::get('/cellier-vin/{id}', [CellierVinController::class, 'show']);
});
