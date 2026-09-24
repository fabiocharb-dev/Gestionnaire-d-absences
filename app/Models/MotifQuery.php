<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MotifQuery extends Model
{
    public function getAvecFiltreMultiple($clauseWhere)
    {
        return DB::table('motifs')
        ->where([
            'colonne1' => $clauseWhere[1],
            'colonne2' => $clauseWhere[2],
        ])->get();
    }
}
