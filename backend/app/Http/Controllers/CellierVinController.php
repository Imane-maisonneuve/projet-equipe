<?php

namespace App\Http\Controllers;

use App\Models\CellierVin;
use Illuminate\Http\Request;
use App\Models\Cellier;


class CellierVinController extends Controller
{
    /**
     * Envoy le cellier avec les vins dans le cellier au frontend
     */
    public function index($id)
    {
        $cellier = Cellier::with(['cellierVins.vin'])->find($id);

        if ($cellier == false) {
            return response()->json([
                'message' => "Le details de ce cellier n'est pas trouvé"
            ], 404);
        }

        return response()->json([
            'cellier' => $cellier,
            'vins' => $cellier->cellierVins
        ]);
    }

    /**
     *
     */
    public function create()
    {
        //
    }

    /**
     *
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
            [
                'cellier_id.required' => 'Le nom du cellier est obligatoire.',
                'cellier_id.exists' => 'Le cellier sélectionné est invalide.',
                'vin_id.required' => 'Le nom du vin est obligatoire.',
                'vin_id.exists' => 'Le vin sélectionné est invalide.',
                'quantite.required' => 'La quantité est obligatoire.',
                'quantite.min' => 'La quantité doit être au moins 1.',
            ]
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
     *
     */
    public function show(CellierVin $cellierVin)
    {
        //
    }

    /**
     *
     */
    public function edit(CellierVin $cellierVin)
    {
        //
    }

    /**
     *
     */
    public function update(Request $request, CellierVin $cellierVin)
    {
        //
    }

    /**
     *
     */
    public function destroy(CellierVin $cellierVin)
    {
        //
    }
}
