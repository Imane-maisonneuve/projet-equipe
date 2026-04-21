<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VinNote extends Model
{
    use HasFactory;
    protected $table = 'vin_notes';
    // attributs pouvant être assignés en masse
    protected $fillable = [
        'note',
        'commentaire'
    ];

    // ajouter la relation avec Usager
    public function usager()
    {
        return $this->belongsTo(Usager::class);
    }

    // ajouter la relation avec CellierVin
    public function vins()
    {
        return $this->belongsTo(Vin::class);
    }
}
