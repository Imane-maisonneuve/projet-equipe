<?php

namespace App\Http\Controllers;

use App\Models\Usager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AuthController;

class UsagerController extends Controller
{
    // liste des messages d'avertissement
    protected $messages = [
        'nom.required' => 'Le nom est obligatoire.',
        'nom.string' => 'Le nom doit être une chaîne de caractères.',
        'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
        'courriel.required' => 'Le courriel est obligatoire.',
        'courriel.email' => 'Le courriel doit être une adresse email valide.',
        'courriel.unique' => 'Ce courriel est déjà utilisé.',
        'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
        'mot_de_passe.string' => 'Le mot de passe doit être une chaîne de caractères.',
        'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
    ];

    /**
     * Récupération des usagers
     *
     * @return void
     */
    public function index()
    {
        $usagers = Usager::all();
        return view('usagers.index', compact('usagers'));
    }


    /**
     * Enregistrement d'usager 
     * personnalisation des messages d'erreur
     * faire la validation des données formulaire
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'courriel' => 'required|email|unique:usagers,courriel',
            'mot_de_passe' => 'required|string|min:6',
        ], $this->messages);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'erreurs' => $validator->errors()
            ], 422));
        }

        $validated = $validator->validated();

        $usager = Usager::create([
            'nom' => $validated['nom'],
            'courriel' => $validated['courriel'],
            'mot_de_passe' => Hash::make($validated['mot_de_passe']),
        ]);

        return response()->json([
            'message' => 'Usager créé avec succès',
            'data' => $usager
        ], 201);
    }

    // Affiche les informations de l'usager connecté
    public function afficherUsager(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Met à jour les infos pesonnelles d'usager.
     * @param Request $request
     * @param int $id
     * @return json reponse()
     */
    public function update(Request $request, $id)
    {
        // Trouver l'usager
        $usager = Usager::findOrFail($id);

        // Validation
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'courriel' => 'required|email|unique:usagers,courriel,' . $id,
        ], $this->messages);

        // retourne erreur
        if ($validator->fails()) {
            return response()->json([
                'erreurs' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Mise à jour
        $usager->nom = $validated['nom'];
        $usager->courriel = $validated['courriel'];

        // Mot de passe seulement s'il est fourni
        if (!empty($validated['mot_de_passe'])) {
            $usager->mot_de_passe = Hash::make($validated['mot_de_passe']);
        }

        $usager->save();

        // Réponse
        return response()->json([
            'message' => 'Usager mis à jour avec succès',
            'data' => $usager
        ]);
    }

    /**
     * Affiche les informations d’un usager spécifique.
     * @param int $id 
     * @return JsonResponse 
     */
    public function show($id)
    {
        try {
            $usager = Usager::findOrFail($id);

            return response()->json([
                'data' => $usager
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur',
                'erreur' => $e->getMessage()
            ], 500);
        }
    }


    // Supprimer le compte de l'usager connecté
    public function supprimerUsager(Request $request)
    {
        $usager = $request->user();

        $usager->delete();

        return response()->json(['message' => 'Votre Compte est supprimé']);
    }
}
