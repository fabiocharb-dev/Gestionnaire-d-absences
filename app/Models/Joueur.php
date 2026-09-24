<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Joueur extends Model
{
    /** @use HasFactory<\Database\Factories\JoueurFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'genre',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEloquent()
    {
        return Joueur::all();
    }

    public function abscences()
    {
        return $this->hasMany(Absence::class);
    }
}
