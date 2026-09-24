<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Motif;

class MotifSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Congé Payé',
            'Congé Paternité',
            'Congé Maternité',
        ] as $description) {
            Motif::firstOrCreate(
                ['description' => $description],
                ['date' => now()->toDateString()]
            );
        }
    }
}
