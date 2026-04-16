<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vin extends Model
{
    use HasFactory;
    protected $fillable = [
        'sku',
        'nom',
        'prix',
        'pays',
        'region',
        'cepage',
        'degre_alcool',
        'taux_sucre',
        'format',
        'annee',
        'image_url',
        'couleur'
    ];

    public function cellierVins()
    {
        return $this->hasMany(CellierVin::class);
    }

    public function listeAchats()
    {
        return $this->hasMany(listeAchat::class);
    }
}
