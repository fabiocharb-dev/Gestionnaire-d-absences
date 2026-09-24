<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Motif extends Model
{
    use HasFactory;
/*
    public function getToutAvecEloquent()
    {
        return Motif::all();
    }
*/
    public function getToutAvecQuery()
    {
        return DB::table('motifs')->get();
    }

    public function abscences()
    {
        return $this->hasMany(Absence::class);
    }
}
