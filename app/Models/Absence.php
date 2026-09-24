<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    /** @use HasFactory<\Database\Factories\AbsenceFactory> */
    use HasFactory;

    protected $fillable = [
        'date_debut',
        'date_fin',
        'motif_id',
        'joueur_id',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
        ];
    }

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }

    public function motif()
    {
        return $this->belongsTo(Motif::class);
    }
}
