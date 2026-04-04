<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vin;
use App\Services\SAQService;

class VinController extends Controller
{
    /**
     * Récupère les paramètres de pagination depuis la requête HTTP
     * page : numéro de la page actuelle (par défaut 1)
     * per_page : nombre de résultats par page (par défaut 12)
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
        $filters = $request->get('filters', []);

        $query = Vin::query();

        if (!empty($filters['countries'])) {
            $query->whereIn('country', $filters['countries']);
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
                    $q->orWhere('grape', 'like', "%$cepage%");
                }
            });
        }

        if (!empty($filters['prix'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['prix'] as $prix) {
                    if (is_string($prix) && str_contains($prix, '-')) {
                        [$min, $max] = explode('-', $prix);
                        $q->orWhereBetween('price', [(float)$min, (float)$max]);
                    } else {
                        $q->orWhere('price', (float)$prix);
                    }
                }
            });
        }

        if (!empty($filters['formats'])) {
            $query->whereIn('litre', $filters['formats']);
        }

        if (!empty($filters['degres'])) {
            $query->whereIn('alcohol', array_map('floatval', $filters['degres']));
        }

        if (!empty($filters['producteurs'])) {
            $query->whereIn('producer', $filters['producteurs']);
        }

        if (!empty($filters['millesimes'])) {
            $query->whereIn('millesime', $filters['millesimes']);
        }

        if (!empty($filters['couleur'])) {
            $query->whereIn('couleur', $filters['couleur']);
        }

        $wines = $query->paginate($perPage, ['*'], 'page', $page);

        $allFilters = [
            'countries' => Vin::distinct()->pluck('country')->filter()->values(),
            'regions' => Vin::distinct()->pluck('region')->filter()->values(),
            'cepages' => Vin::distinct()->pluck('grape')->filter()->values(),
            'prix' => Vin::distinct()->pluck('price')->filter()->values(),

            'formats' => Vin::distinct()->pluck('litre')->filter()->values(),
            'degres' => Vin::distinct()->pluck('alcohol')->filter()->values(),
            'producteurs' => Vin::distinct()->pluck('producer')->filter()->values(),
            'millesimes' => Vin::distinct()->pluck('millesime')->filter()->values(),
            'couleur' => Vin::distinct()->pluck('couleur')->filter()->values(),
        ];

        return response()->json([
            'data' => $wines->items(),
            'total' => $wines->total(),
            'filters' => $allFilters,
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
        $pages =  (int)ceil($total/100);
        set_time_limit(240); // Augmente le temps d'attente
        if($pages || $pages !== 0){        
            for($i = 1; $i <= $pages; $i++){
                $bouteilles = $this->getVinsSaq($service, $i);
                foreach ($bouteilles as $bouteille) {
                    Vin::updateOrCreate(
                        ['sku' => $bouteille['saq_id']],
                        [
                        'nom' => $bouteille['nom'],
                        'prix' => $bouteille['prix'],
                        'pays' => $bouteille['pays'],
                        'region' => $bouteille['region'],
                        'cepage' => $bouteille['cepage'], 
                        'degre_alcool' => $bouteille['degre_alcool'],
                        'taux_sucr' => $bouteille['taux_sucre'],
                        'format' => $bouteille['format'],
                        'annee' => $bouteille['annee'],
                        'image_url' => $bouteille['image_url'],
                        'couleur' => $bouteille['couleur']
                        ]
                    );
                } 
            }
        }else{
            return "Données sont corrompues";
        }       
        return "Importation est terminée";
    }
}
