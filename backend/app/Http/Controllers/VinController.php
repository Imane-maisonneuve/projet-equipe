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
        $tri = $request->get('tri');

        switch ($tri) {
            case 1:
                $query = Vin::query()->orderBy('nom', 'asc');
                break;
            case 2:
                $query = Vin::query()->orderBy('nom', 'desc');
                break;
            case 3:
                $query = Vin::query()->orderBy('prix', 'asc');
                break;
            case 4:
                $query = Vin::query()->orderBy('prix', 'desc');
                break;
            case 5:
                $query = Vin::query()->orderBy('annee', 'asc');
                break;
            case 6:
                $query = Vin::query()->orderBy('annee', 'desc');
                break;
            default:
                $query = Vin::query();
                break;
        }

        $filters = $request->get('filters', []);

        if (!empty($recherche)) {
            $query->where('nom', 'like', "%{$recherche}%");
        }

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

    // Crée une bouteille de vin non listée dans le catalogue et l'ajouter au cellier
    public function creerBouteillePersonnalisee(Request $request)
    {
        // Valider les données de la requête
        $request->validate([
            'nom' => 'required|string|max:100',
            'prix' => 'required|numeric|min:1',
            'pays' => 'nullable|string',
            'region' => 'nullable|string|not_regex:/[0-9]/',
            'cepage' => 'nullable|string|not_regex:/[0-9]/',
            'degre_alcool' => 'nullable|numeric',
            'taux_sucre' => 'nullable|numeric',
            'format' => 'nullable|integer|min:50|max:10000',
            'annee' => 'nullable|digits:4',
            'couleur' => 'nullable|string',
            'quantite' => 'required|integer|min:1',
        ], [
            'nom.required' => 'Le nom de la bouteille est obligatoire.',
            'nom.max' => 'Le nom de la bouteille ne peut pas dépasser 100 caractères.',

            'prix.required' => 'Le prix de la bouteille est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être supérieur ou égal à 1.',

            'pays.string' => 'Le pays doit être une chaîne de caractères.',

            'region.string' => 'La région doit être une chaîne de caractères.',
            'region.not_regex' => 'La région ne doit pas contenir de chiffres.',

            'cepage.string' => 'Le cépage doit être une chaîne de caractères.',
            'cepage.not_regex' => 'Le cépage ne doit pas contenir de chiffres.',

            'degre_alcool.numeric' => 'Le degré d alcool doit être un nombre.',

            'taux_sucre.numeric' => 'Le taux de sucre doit être un nombre.',

            'format.integer' => 'Le format doit être un nombre.',
            'format.min' => 'Le format doit être au moins de 50 ml.',
            'format.max' => 'Le format ne peut pas dépasser 10000 ml.',

            'annee.digits' => 'L année doit contenir exactement 4 chiffres.',

            'couleur.string' => 'La couleur doit être une chaîne de caractères.',

            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité doit être au moins 1.',
        ]);

        // Créer une nouvelle bouteille de vin personnalisée
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

        // Ajouter la bouteille personnalisée au cellier avec la quantité spécifiée par l'usager
        if ($vinPersonnalise) {
            $cellierVin = CellierVin::create([
                'cellier_id' => $request->cellier_id,
                'vin_id' => $vinPersonnalise->id,
                'quantite' => $request->quantite,
            ]);
            // Retourner une réponse JSON avec les détails de la bouteille personnalisée et du cellier
            if ($cellierVin) {
                return response()->json([
                    'message' => 'Bouteille de vin personnalisée ajoutée avec succès au cellier',
                    'vin' => $vinPersonnalise,
                    'cellier_vin' => $cellierVin
                ], 201);
            } else {
                // Si echec de  l'ajout au cellier, supprimer la bouteille personnalisée créée pour éviter les doublons
                $vinPersonnalise->delete();
                return response()->json([
                    'message' => 'Ajout de la bouteille personnalisée au cellier a échoué veuillez réessayer'
                ], 500);
            }
        } else {
            // Si échec de la création de la bouteille personnalisée, retourner une réponse d'erreur
            return response()->json([
                'message' => 'Erreur lors de la création de la bouteille de vin'
            ], 500);
        }
    }
    // Récupérer la liste des pays disponibles dans la base de données pour les vins
    public function recupererPays()
    {
        $listePays = Vin::query()
            ->select('pays')
            ->whereNotNull('pays')
            ->where('pays', '!=', '')
            ->distinct()
            ->orderBy('pays')
            ->pluck('pays');
        // Retourner la liste des pays au format JSON
        return response()->json([
            'listePays' => $listePays
        ]);
    }
}
