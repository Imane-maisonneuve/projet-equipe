<?php

namespace App\Http\Controllers;

use App\Models\Usager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class UsagerController extends Controller
{

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
        $messages = [
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

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'courriel' => 'required|email|unique:usagers,courriel',
            'mot_de_passe' => 'required|string|min:6',
        ], $messages);

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
}
