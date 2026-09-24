<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('joueurs', 'genre')) {
            return;
        }

        Schema::table('joueurs', function (Blueprint $table) {
            $table->enum('genre', ['homme', 'femme', 'nonbinaire'])
                ->nullable()
                ->after('prenom');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('joueurs', 'genre')) {
            return;
        }

        Schema::table('joueurs', function (Blueprint $table) {
            $table->dropColumn('genre');
        });
    }
};
