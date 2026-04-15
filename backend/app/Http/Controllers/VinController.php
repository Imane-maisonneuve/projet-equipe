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
        $recherche = $request->get('recherche', '');

        $query = Vin::query();

        if (empty($recherche) == false) {
            $query->where('nom', 'like', "%{$recherche}%");
        }

        $wines = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $wines->items(),
            'total' => $wines->total(),
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
}
