<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- FALTABA ESTO

return new class extends Migration
{
    public function up()
    {
        Schema::create('cementerio_general', function (Blueprint $table) {
            $table->id(); 
            $table->string('nombre', 255)->nullable();
            $table->text('observacion')->nullable();
            $table->decimal('area_total_m2', 12, 2)->nullable();
            
            // BORRAMOS specificType DE AQUÍ PARA EVITAR EL ERROR
            
            $table->timestamps();
        });

        // AGREGAMOS LA GEOMETRÍA POR FUERA
        DB::statement('ALTER TABLE cementerio_general ADD COLUMN geom geometry(MULTIPOLYGON, 4326)');

        // Índice espacial
        DB::statement('CREATE INDEX idx_cementerio_geom ON cementerio_general USING GIST (geom);');
    }

    public function down()
    {
        Schema::dropIfExists('cementerio_general');
    }
};