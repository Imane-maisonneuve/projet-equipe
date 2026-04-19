<?php

namespace App\Http\Controllers;

use App\Models\ListeAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListeAchatController extends Controller
{
    /**
     * Affiche la liste d'achats.
     */
    public function index()
    {
        $usager = auth()->user();

        // Va chercher dans la DB le cellier qui correspond a $id
        $listeAchat = ListeAchat::with('vin')
            ->where('usager_id', $usager->id)
            ->get();

        //Si il trouve pas le correspondant
        if (!$listeAchat) {
            return response()->json([
                'message' => "Le details de cet achat n'est pas trouvé"
            ], 404);
        }

        //retourne la liste d'achats correspondant au usager
        return response()->json([
            'liste_achats' => $listeAchat
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Ajoute une bouteille de vin dans la liste d'achat
     */
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $request->validate(
            [
                'usager_id' => 'required|exists:usagers,id',
                'vin_id' =>     'required|exists:vins,id'
            ],
        );

        // Vérification de l'existence du vin dans la liste d'achat
        if (ListeAchat::where('vin_id', $request->vin_id)
            ->where('usager_id', $request->usager_id)
            ->exists()
        ) {
            // Si le vin existe déjà dans la liste d'achat, retourner un message d'erreur
            return response()->json([
                'message' => "Ce vin existe déjà la liste d'achat."
            ], 422);
        } else {
            // Si le vin n'existe pas dans la liste d'achat, créer une nouvelle entrée
            $ListeAchat = ListeAchat::create([
                'usager_id' => $request->usager_id,
                'vin_id' => $request->vin_id,
            ]);

            // Retourner une réponse de succès
            return response()->json([
                'message' => "Bouteille ajoutée dans la liste d'achat avec succès",
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $listeAchat = ListeAchat::with(['vin', 'usager'])
        ->where('liste_achats.id', $id)
        ->whereHas('usager', function ($query) {
            $query->where('id', Auth::id());
        })
        ->first();

        if (!$listeAchat) {
            return response()->json([
                'error' => 'Bouteille non trouvée ou accès refusé'
            ], 404);
        }

        return response()->json([
            'id' => $listeAchat->id,
            'nom' => $listeAchat->vin->nom,
            'prix' => $listeAchat->vin->prix,
            'pays' => $listeAchat->vin->pays,
            'region' => $listeAchat->vin->region,
            'format' => $listeAchat->vin->format,
            'annee' => $listeAchat->vin->annee,
            'image' => $listeAchat->vin->image_url,
            'couleur' => $listeAchat->vin->couleur,
            'cepage' => $listeAchat->vin->cepage,
            'degre_alcool' => $listeAchat->vin->degre_alcool,
            'taux_sucre' => $listeAchat->vin->taux_sucre,
            'sku' => $listeAchat->vin->sku,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ListeAchat $listeAchat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ListeAchat $listeAchat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // Recuperer la bouteille de vin dans la liste d'achat
        $listeAchat = ListeAchat::where('id', $request->id);

        if (!$listeAchat) {
            return response()->json([
                'message' => "La bouteille de vin n'existe pas dans la liste d'achat."
            ], 404);
        }

        // suppression de la bouteille de vin dans la liste d'achat
        $listeAchat->delete();

        // Retourner une réponse de succès
        return response()->json([
            'message' => "Bouteille supprimée de la liste d'achat avec succès."
        ]);
    }
}
