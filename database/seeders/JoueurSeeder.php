<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Joueur;

class JoueurSeeder extends Seeder
{
    public function run(): void
    {
        Joueur::factory()
            ->count(5)
            ->create();
    }
}
