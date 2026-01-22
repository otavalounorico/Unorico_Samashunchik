<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- FALTABA ESTO

return new class extends Migration
{
    public function up()
    {
        Schema::create('infraestructura', function (Blueprint $table) {
            $table->id(); 
            $table->string('tipo', 100)->nullable(); 
            $table->string('nombre', 255)->nullable();
            
            // BORRAMOS specificType DE AQUÍ
            
            $table->timestamps();
        });

        // AGREGAMOS LA GEOMETRÍA POR FUERA
        DB::statement('ALTER TABLE infraestructura ADD COLUMN geom geometry(MULTIPOLYGON, 4326)');

        // Índice espacial
        DB::statement('CREATE INDEX idx_infraestructura_geom ON infraestructura USING GIST (geom);');
    }

    public function down()
    {
        Schema::dropIfExists('infraestructura');
    }
};