<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use App\Models\Usager;
use Illuminate\Http\Request;
use App\Models\CellierVin;
use App\Models\Vin;

class CellierController extends Controller
{
    /**
     * Recherche le celier par id du usager.
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $celliers = Cellier::where('usager_id', $request->user()->id)->get();
        return response()->json([
            'data' => $celliers
        ]);
    }

    /**
     * Place le nom dans celliers.
     * @param Request $request
     */
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $request->validate(
            [
                'nom' => 'required|string|min:2|max:50',
            ],
            [
                'nom.required' => 'Le nom est obligatoire.',
                'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
                'nom.max' => 'Le nom ne peut pas dépasser 50 caractères.',
                'nom.unique' => 'Ce nom existe déjà.',
            ]
        );

        if (Cellier::where('usager_id', $request->user()->id)->where('nom', $request->nom)->exists()) {
            return response()->json([
                'message' => 'Vous avez déjà un cellier avec ce nom.'
            ], 400);
        }

        // Création du cellier associé à l'usager connecté
        $cellier = Cellier::create([
            'nom' => $request->nom,
            'usager_id' => $request->user()->id,
        ]);

        // Retour de la réponse JSON avec le cellier créé
        return response()->json([
            'message' => 'Cellier créé avec succès',
            'data' => $cellier
        ], 201);
    }

    /**
     * Mise a jour du nom dans la table celliers.
     * @param Request $request
     * @param int #id
     */
    public function update(Request $request, $id)
    {

        $cellier = Cellier::findOrFail($id);
        $usager = Usager::findOrFail($cellier->usager_id);

        // Vérifier si l'usager connecté est le propriétaire du cellier
        if ($usager->id !== auth()->id()) {
            return response()->json([
                'message' => 'Non autorisé à modifier ce cellier'
            ], 403);
        }

        // Validation des données d'entrée
        $request->validate(
            [
                'nom' => 'required|string|min:2|max:50',
            ],
            [
                'nom.required' => 'Le nom est obligatoire.',
                'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
                'nom.max' => 'Le nom ne peut pas dépasser 50 caractères.',
                'nom.unique' => 'Ce nom existe déjà.',
            ]
        );

        // Mise à jour du cellier
        $cellier->update([
            'nom' => $request->nom,
        ]);
    }

    /**
     * Methode qui permet de récupérer toutes bouteilles 
     * des celliers de l'usager connecté,
     * avec possibilité de recherche et de filtres dynamiques
     * @param Request $request
     * @return json
     */
    public function bouteillesUsager(Request $request)
    {
        //Récupère l'usager actuellement connecté
        $usager = auth()->user();

        //Récupère les filtres envoyés ou un tableau vide par defaut
        $filters = $request->get('filters', []);
        $search = $request->get('recherche', '');
        $tri = $request->get('tri', '');

        //Construit la requete principale
        $query = CellierVin::with(['vin', 'cellier'])
            ->whereHas('cellier', function ($q) use ($usager) {
                $q->where('usager_id', $usager->id);
            })
            ->join('vins', 'cellier_vins.vin_id', '=', 'vins.id')
            ->select('cellier_vins.*');

        // Trie les bouteilles selon num passé par requete    
        switch ($tri) {
            case 1:
                $query->orderBy('vins.nom', 'asc');
                break;
            case 2:
                $query->orderBy('vins.nom', 'desc');
                break;
            case 3:
                $query->orderBy('prix', 'asc');
                break;
            case 4:
                $query->orderBy('prix', 'desc');
                break;
            case 5:
                $query->orderBy('annee', 'asc');
                break;
            case 6:
                $query->orderBy('annee', 'desc');
                break;
            default:
                break;
        }

        //Filtrer par nom de vin
        if (!empty($search)) {
            $query->whereHas('vin', function ($q) use ($search) {
                $q->where('nom', 'like', "%$search%");
            });
        }

        //Filtrer par pays
        if (!empty($filters['countries'])) {
            $query->whereHas('vin', fn($q) => $q->whereIn('pays', $filters['countries']));
        }

        //Filtrer par region
        if (!empty($filters['regions'])) {
            $query->whereHas('vin', function ($q) use ($filters) {
                foreach ($filters['regions'] as $region) {
                    $q->orWhere('region', 'like', "%$region%");
                }
            });
        }

        //Filtrer par cepage
        if (!empty($filters['cepages'])) {
            $query->whereHas('vin', function ($q) use ($filters) {
                foreach ($filters['cepages'] as $cepage) {
                    $q->orWhere('cepage', 'like', "%$cepage%");
                }
            });
        }

        //Filtrer par prix
        if (!empty($filters['prix'])) {
            $min = min($filters['prix']);
            $max = max($filters['prix']);

            $query->whereHas(
                'vin',
                fn($q) =>
                $q->whereBetween('prix', [$min, $max])
            );
        }

        //Filtrer par format 
        if (!empty($filters['formats'])) {
            $query->whereHas(
                'vin',
                fn($q) =>
                $q->whereIn('format', $filters['formats'])
            );
        }

        //filtrer par degre alcool
        if (!empty($filters['degres'])) {
            $query->whereHas(
                'vin',
                fn($q) =>
                $q->whereIn('degre_alcool', $filters['degres'])
            );
        }

        //filtrer par millesimes
        if (!empty($filters['millesimes'])) {
            $query->whereHas(
                'vin',
                fn($q) =>
                $q->whereIn('annee', $filters['millesimes'])
            );
        }


        //filter par couleur
        if (!empty($filters['couleur'])) {
            $query->whereHas(
                'vin',
                fn($q) =>
                $q->whereIn('couleur', $filters['couleur'])
            );
        }

        //Exécution de la requête pour récupérer les bouteilles filtrées
        $bouteilles = $query->get();

        //Construction d'une requête pour récupérer tous les filtres disponibles
        $vinQuery = Vin::whereHas('cellierVins', function ($q) use ($usager) {
            $q->whereHas(
                'cellier',
                fn($q2) =>
                $q2->where('usager_id', $usager->id)
            );
        });

        //Génération des valeurs possibles pour chaque filtres qui doivent être distnct et non null
        $toutLesFilters = [
            'countries' => (clone $vinQuery)->distinct()->pluck('pays')->filter()->values(),
            'regions' => (clone $vinQuery)->distinct()->pluck('region')->filter()->values(),
            'cepages' => (clone $vinQuery)->distinct()->pluck('cepage')->filter()->values(),
            'prix' => (clone $vinQuery)->distinct()->pluck('prix')->filter()->values(),
            'formats' => (clone $vinQuery)->distinct()->pluck('format')->filter()->values(),
            'degres' => (clone $vinQuery)->distinct()->pluck('degre_alcool')->filter()->values(),
            'millesimes' => (clone $vinQuery)->distinct()->pluck('annee')->filter()->values(),
            'couleur' => (clone $vinQuery)->distinct()->pluck('couleur')->filter()->values(),
        ];

        /**
         * Retourn de la réponse sous forme JSON
         * avec les bouteilles filtrées et les options de filtres disponibles
         * */
        return response()->json([
            'data' => $bouteilles,
            'filters' => $toutLesFilters
        ]);
    }

    /**
     * Supprime le cellier.
     * @param Cellier $cellier
     * @return json
     */
    public function destroy(Cellier $cellier)
    {
        $cellier->delete();
        return response()->json(['message' => 'Cellier supprimé avec succès']);
    }
}
