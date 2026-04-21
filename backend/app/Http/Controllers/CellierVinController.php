<?php

namespace App\Http\Controllers;

use App\Models\CellierVin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Cellier;


class CellierVinController extends Controller
{
    // liste des messages d'avertissement
    protected $messages = [
        'cellier_id.required' => 'Le nom du cellier est obligatoire.',
        'cellier_id.exists' => 'Le cellier sélectionné est invalide.',
        'vin_id.required' => 'Le nom du vin est obligatoire.',
        'vin_id.exists' => 'Le vin sélectionné est invalide.',
        'quantite.required' => 'La quantité est obligatoire.',
        'quantite.min' => 'La quantité doit être au moins 1.',
        'quantite.integer' => 'Le nombre doit être entier',
    ];

    /**
     * Envoy le cellier avec les vins dans le cellier au frontend.
     * @param int $id du cellierVin dans la table
     * @return json
     */
    public function index($id)
    {
        // Va chercher dans la DB le cellier qui correspond a $id
        $cellier = Cellier::with(['cellierVins.vin'])->find($id);

        //Si trouve pas da cellier correspondant
        if ($cellier == false) {
            return response()->json([
                'message' => "Le details de ce cellier n'est pas trouvé"
            ], 404);
        }

        //retourne le cellier correspondant et les vins (incluant la quantite)
        return response()->json([
            'cellier' => $cellier,
            'vins' => $cellier->cellierVins
        ]);
    }

    /**
     * Place les données dans la table cellier_vins.
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $request->validate(
            [
                'cellier_id' => 'required|exists:celliers,id',
                'vin_id' =>     'required|exists:vins,id',
                'quantite' => 'required|integer|min:1',
            ],
            $this->messages
        );

        // Vérification de l'existence du vin dans le cellier
        if (CellierVin::where('cellier_id', $request->cellier_id)
            ->where('vin_id', $request->vin_id)
            ->exists()
        ) {
            // Si le vin existe déjà dans le cellier, retourner un message d'erreur
            return response()->json([
                'message' => 'Ce vin existe déjà dans ce cellier.'
            ], 422);
        } else {
            // Si le vin n'existe pas dans le cellier, inserer le vin dans le cellier
            $cellierVin = CellierVin::create([
                'cellier_id' => $request->cellier_id,
                'vin_id' => $request->vin_id,
                'quantite' => $request->quantite,
            ]);

            // Retourner une réponse de succès
            return response()->json([
                'message' => 'Bouteille ajouté dans le cellier avec succès',
                'data' => $cellierVin
            ], 201);
        }
    }

    /**
     * La méthode affiche les détails d'une bouteill spécifique appartenant a l'usager connecté
     * La méthode récupére une entrée de la table 'cellier_vins' en fonction de son id
     * en incluant les relations avec le la table vin et la table cellier
     * ELle vérifie également que la bouteille appartient bien a un cellier associé a l'uager actuellement
     * connecté
     * Elle retourne les informations détaillées de la bouteille sous forme de JSON
     * @param int  $id de la boouteille dans le cellier
     * @return JSON
     *
     */

    public function show($id)
    {
        $cellierVin = CellierVin::with(['vin', 'cellier'])
            ->where('cellier_vins.id', $id)
            ->whereHas('cellier', function ($query) {
                $query->where('usager_id', Auth::id());
            })
            ->first();
        if (!$cellierVin) {
            return response()->json([
                'error' => 'Bouteille non trouvée ou accès refusé'
            ], 404);
        }

        return response()->json([
            'id' => $cellierVin->id,
            'cellier_id' => $cellierVin->cellier->id,
            'nom' => $cellierVin->vin->nom,
            'prix' => $cellierVin->vin->prix,
            'pays' => $cellierVin->vin->pays,
            'region' => $cellierVin->vin->region,
            'format' => $cellierVin->vin->format,
            'annee' => $cellierVin->vin->annee,
            'image' => $cellierVin->vin->image_url,
            'couleur' => $cellierVin->vin->couleur,
            'quantite' => $cellierVin->quantite,
            'cellier_nom' => $cellierVin->cellier->nom,
            'cepage' => $cellierVin->vin->cepage,
            'degre_alcool' => $cellierVin->vin->degre_alcool,
            'taux_sucre' => $cellierVin->vin->taux_sucre,
            'sku' => $cellierVin->vin->sku,
        ]);
    }

    /**
     * Met à jour la table celllier_vins
     * @param Request $request
     * @param int $vin_id
     * @return json 
     */
    public function update(Request $request, $vin_id)
    {
        // Trouver cellier_vin
        $cellierVin = CellierVin::findOrFail($vin_id);

        // Validation
        $validator = Validator::make($request->all(), [
            'quantite' => 'required|integer|min:1',
        ], $this->messages);

        // retourne erreur
        if ($validator->fails()) {
            return response()->json([
                'erreurs' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Mise à jour
        $cellierVin->quantite = $validated['quantite'];
        $cellierVin->save();

        // Réponse
        return response()->json([
            'message' => 'Quantité est mis à jour avec succès',
            'data' => $cellierVin
        ]);
    }

    /**
     * Supprime un vin dans un cellier.
     * @param CellierVin id du cellierVin a supprimer
     * @return json
     */
    public function destroy(CellierVin $cellierVin)
    {
        $cellierVin->delete();
        return response()->json(['message' => 'Vin supprimé du cellier avec succès']);
    }
}
