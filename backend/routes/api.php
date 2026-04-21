<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CellierController;
use App\Http\Controllers\CellierVinController;
use App\Http\Controllers\UsagerController;
use App\Http\Controllers\VinController;
use App\Http\Controllers\ListeAchatController;
use App\Http\Controllers\ReviewController;

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

    // Routes pour gerer les bouteilles dans les celliers
    Route::post('/ajouter-bouteille', [CellierVinController::class, 'store']);
    Route::put('/modifier-quantite/{id}', [CellierVinController::class, 'update']);
    Route::get('/detail-cellier/{id}', [CellierVinController::class, 'index']);
    Route::delete('/cellier-vins/{cellierVin}', [CellierVinController::class, 'destroy']);
    Route::get('/cellier-vin/{id}', [CellierVinController::class, 'show']);
    Route::post('/creer-bouteille-perso', [VinController::class, 'creerBouteillePersonnalisee']);

    //Recherche bouteille dans les celliers
    Route::get('/bouteilles', [CellierController::class, 'bouteillesUsager']);

    //Filtrer les bouteilles du catalogue
    Route::get('/vins', [VinController::class, 'index']);

    // Route pour obtenir la liste des pays disponibles dans le catalogue de vins
    Route::get('/pays', [VinController::class, 'recupererPays']);

    // Route pour supprimer une bouteille personnalisée de la table des vins
    Route::delete('/supprimer-bouteille/{bouteilleSKU}', [VinController::class, 'supprimerVinPersonnalise']);

    //Routes pour gerer la liste d'achats
    Route::get('/liste-achats', [ListeAchatController::class, 'index']);
    Route::post('/ajouter-bouteille-liste', [ListeAchatController::class, 'store']);
    Route::get('/liste-achats-vin/{id}', [ListeAchatController::class, 'show']);
    Route::delete('/liste-achats/{id}', [ListeAchatController::class, 'destroy']);

    // Route pour récupérer les détails d'un vin à partir de son SKU
    Route::get('/vin/{sku}', [VinController::class, 'chercherVinPersonnalise']);

    // Route pour modifier une bouteille personnalisée dans la table des vins
    Route::put('/modifier-bouteille-perso/{sku}', [VinController::class, 'modifierBouteillePersonnalisee']);

    // Routes pour reviews des bouteilles
    Route::post('/reviews', [ReviewController::class, 'storeOrUpdate']);
    Route::get('/reviews/{vin_id}/{usager_id}', [ReviewController::class, 'show']);    
});
