<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeAchat extends Model
{
    use HasFactory;

    protected $table = 'liste_achats';

    protected $fillable = [
        'usager_id',
        'vin_id'
    ];

    //ajouter la relation avec Vin
    public function vin()
    {
        return $this->belongsTo(Vin::class);
    }

     // ajouter la relation avec Usager
     public function usager()
     {
        return $this->belongsTo(Usager::class);
     }
}
