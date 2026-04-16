<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usager extends Authenticatable
{
    use HasFactory;

    protected $table = 'usagers';

    protected $fillable = [
        'nom',
        'courriel',
        'mot_de_passe',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    // public function getAuthIdentifierName()
    // {
    //     return 'courriel';
    // }

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // ajouter la relation avec Cellier
    public function celliers()
    {
        return $this->hasMany(Cellier::class, 'usager_id');
    }

    // ajouter la relation avec ListeAchat
    public function listeAchats()
    {
        return $this->hasMany(listeAchat::class, 'usager_id');
    }
}
