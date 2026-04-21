<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Usager;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * Connexion d'un Usager
     * faire la validation des données du formulaire
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Validation des données du formulaire de connexion
        $request->validate([
            'courriel' => 'required|email|exists:usagers',
            'mot_de_passe' => 'required|min:6|string'
        ]);

        // Trouve le premier usager avec ce courriel
        $usager = Usager::where('courriel', $request->courriel)->first();

        // Si on a trouver un usager avec le courriel et le mot de passe entrer match celui de la BD
        if ($usager && Hash::check($request->mot_de_passe, $usager->mot_de_passe)) {
            Auth::login($usager);
            return response()->json([
                'usager' => Auth::user(),
                'message' => 'Connexion réussie'
            ]);
        } else {
            return response()->json(['message' => 'Les informations de connexion ne sont pas valid'], 401);
        }
    }

    /**
     * Deconnexion de l'usager
     * @return json
     */
    public function destroy()
    {
        Auth::logout();
        return response()->json(['message' => 'Deconnexion']);
    }
}
