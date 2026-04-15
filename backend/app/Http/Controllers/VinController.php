<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vin;
use App\Services\SAQService;
use App\Models\CellierVin;

class VinController extends Controller
{
    /**
     * Récupère les paramètres de pagination depuis la requête HTTP
     * page : numéro de la page actuelle (par défaut 1)
     * per_page : nombre de résultats par page (par défaut 12)
     * Recupere le input de la recherche
     * Récupère les filtres envoyés dans la requête, ou tableau vide si aucun
     * Initialise une requête Eloquent sur le modèle Vin
     * Filtre par un champ spécifié
     * @param Request $request
     * @return void
     */

    public function index(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 12);
        $recherche = $request->get('recherche', '');

        $filters = $request->get('filters', []);

        $query = Vin::query();

        if (!empty($recherche)) {
            $query->where('nom', 'like', "%{$recherche}%");
        }

        if (!empty($filters['countries'])) {
            $query->whereIn('pays', $filters['countries']);
        }

        if (!empty($filters['regions'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['regions'] as $region) {
                    $q->orWhere('region', 'like', "%$region%");
                }
            });
        }

        if (!empty($filters['cepages'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['cepages'] as $cepage) {
                    $q->orWhere('cepage', 'like', "%$cepage%");
                }
            });
        }

        if (!empty($filters['prix'])) {
            $min = min($filters['prix']);
            $max = max($filters['prix']);
            $query->whereBetween('prix', [(float)$min, (float)$max]);
        }

        if (!empty($filters['formats'])) {
            $query->whereIn('format', $filters['formats']);
        }

        if (!empty($filters['degres'])) {
            $query->whereIn('degre_alcool', $filters['degres']);
        }

        if (!empty($filters['millesimes'])) {
            $query->whereIn('annee', $filters['millesimes']);
        }

        if (!empty($filters['couleur'])) {
            $query->whereIn('couleur', $filters['couleur']);
        }


        $wines = $query->paginate($perPage, ['*'], 'page', $page);


        $toutLesFilters = [
            'countries' => Vin::distinct()->pluck('pays')->filter()->values(),
            'regions' => Vin::distinct()->pluck('region')->filter()->values(),
            'cepages' => Vin::distinct()->pluck('cepage')->filter()->values(),
            'prix' => Vin::distinct()->pluck('prix')->filter()->values(),
            'formats' => Vin::distinct()->pluck('format')->filter()->values(),
            'degres' => Vin::distinct()->pluck('degre_alcool')->filter()->values(),
            'millesimes' => Vin::distinct()->pluck('annee')->filter()->values(),
            'couleur' => Vin::distinct()->pluck('couleur')->filter()->values(),

        ];

        return response()->json([
            'data' => $wines->items(),
            'total' => $wines->total(),
            'filters' => $toutLesFilters,
        ]);
    }

    /**
     * Récupère les vins de la SAQ en utilisant le service SAQService
     * Récupère les produits d'une page de résultats de la SAQ
     * Filtre le résultat pour ne garder que les bouteilles de vin
     * Formate les attributs des bouteilles de vin filtrées
     * Retourne la liste des bouteilles de vin formatées
     * @param SAQService $service
     * @param int $page
     * @return array
     */
    public function getVinsSaq(SAQService $service, int $page = 1)
    {
        // Récupérer les produits d'une page de résultats de la SAQ'
        $resultat = $service->getWines($page);

        // Filtrer le resultat pour ne garder que les bouteilles de vin
        $bouteillesVinFiltrees = $service->filtrerVins($resultat['bouteilles_de_vin']);

        // Formater les attributs des bouteilles de vin filtrées
        $bouteillesVinFormattees = $service->formatterAttributsVins($bouteillesVinFiltrees);

        //  Retourner la liste des bouteilles de vin formatées
        return $bouteillesVinFormattees;
    }

    /**
     * Enregistre les données du SAQ en base de données.
     * @param SAQService $service
     * @return string
     */

    public function store(SAQService $service)
    {
        $total = $service->getWines()['total'];
        $pages =  (int)ceil($total / 100);
        set_time_limit(240); // Augmente le temps d'attente
        if ($pages || $pages !== 0) {
            for ($i = 1; $i <= $pages; $i++) {
                $bouteilles = $this->getVinsSaq($service, $i);
                foreach ($bouteilles as $bouteille) {
                    Vin::updateOrCreate(
                        ['sku' => $bouteille['saq_id']],
                        [
                            'nom' => $bouteille['nom'] ?? 'Nom inconnu',
                            'prix' => $bouteille['prix'] ?? 0,
                            'pays' => $bouteille['pays'] ?? null,
                            'region' => $bouteille['region'] ?? null,
                            'cepage' => $bouteille['cepage'] ?? null,
                            'degre_alcool' => $bouteille['degre_alcool'] ?? null,
                            'taux_sucre' => $bouteille['taux_sucre'] ?? null,
                            'format' => $bouteille['format'] ?? null,
                            'annee' => $bouteille['annee'] ?? null,
                            'image_url' => $bouteille['image_url'] ?? null,
                            'couleur' => $bouteille['couleur'] ?? null
                        ]
                    );
                }
            }
        } else {
            return "Données sont corrompues";
        }
        return "Importation est terminée";
    }
    public function creerBouteillePersonnalisee(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prix' => 'required|numeric',
            'pays' => 'nullable|string',
            'region' => 'nullable|string',
            'cepage' => 'nullable|string',
            'degre_alcool' => 'nullable|string',
            'taux_sucre' => 'nullable|string',
            'format' => 'nullable|string|max:6',
            'annee' => 'nullable|string|max:4',
            'couleur' => 'nullable|string',
            'quantite' => 'required|integer|min:1',
        ], [
            'nom.required' => 'Le nom de la bouteille est obligatoire.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'prix.required' => 'Le prix de la bouteille est obligatoire.',
            'format.max' => 'Le format ne peut pas dépasser 6 caractères.',
            'annee.max' => 'Année doit avoir 4 caractères.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.min' => 'La quantité doit être au moins 1.',
        ]);

        $vinPersonnalise = Vin::create([
            'sku' => 'PERSO-' . now()->format('YmdHis'),
            'nom' => $request->input('nom'),
            'prix' => $request->input('prix'),
            'pays' => $request->input('pays'),
            'region' => $request->input('region'),
            'cepage' => $request->input('cepage'),
            'degre_alcool' => $request->input('degre_alcool'),
            'taux_sucre' => $request->input('taux_sucre'),
            'format' => $request->input('format'),
            'annee' => $request->input('annee'),
            'image_url' => 'https://www.saq.com/media/catalog/product/placeholder/default_image.jpg',
            'couleur' => $request->input('couleur')
        ]);

        if ($vinPersonnalise) {
            $cellierVin = CellierVin::create([
                'cellier_id' => $request->cellier_id,
                'vin_id' => $vinPersonnalise->id,
                'quantite' => $request->quantite,
            ]);
            if ($cellierVin) {
                return response()->json([
                    'message' => 'Bouteille de vin personnalisée ajoutée avec succès au cellier',
                    'vin' => $vinPersonnalise,
                    'cellier_vin' => $cellierVin
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Erreur ajout de la bouteille au cellier'
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Erreur lors de la création de la bouteille de vin'
            ], 500);
        }
    }
}
