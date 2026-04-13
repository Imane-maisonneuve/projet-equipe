<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cellier extends Model
{
    use HasFactory;

    protected $fillable = [
        'usager_id',
        'nom',
    ];

    // ajouter la relation avec Usager
    public function usager()
    {
        return $this->belongsTo(Usager::class);
    }

    public function cellierVins()
    {
        return $this->hasMany(CellierVin::class);
    }

    // ajouter la relation avec CellierVin
    // public function cellier_vin()
    // {
    //     return $this->hasMany(CellierVin::class);
    // }
}
