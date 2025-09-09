<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {

        $generos = ['Masculino', 'Femenino', 'Otro'];
        foreach ($generos as $nombre) {
            DB::table('generos')->updateOrInsert(
                ['nombre' => $nombre],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }

        // ==============================
        // Estados civiles
        // ==============================
        $estados = ['Soltero', 'Casado', 'Divorciado', 'Viudo'];
        foreach ($estados as $nombre) {
            DB::table('estados_civiles')->updateOrInsert(
                ['nombre' => $nombre],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
