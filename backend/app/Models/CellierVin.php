<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CellierVin extends Model
{
    use HasFactory;

    protected $table = 'cellier_vins';

    protected $fillable = [
        'cellier_id',
        'vin_id',
        'quantite'
    ];

    public function cellier()
    {
        return $this->belongsTo(Cellier::class);
    }

    public function vin()
    {
        return $this->belongsTo(Vin::class);
    }
}
