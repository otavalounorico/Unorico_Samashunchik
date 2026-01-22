<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- FALTABA ESTO

return new class extends Migration
{
    public function up()
    {
        Schema::create('nichos_geom', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->nullable();
            
            // Relación (Foreign Key) - Esto está correcto
            $table->foreignId('bloques_geom_id')
                  ->nullable()
                  ->constrained('bloques_geom')
                  ->onDelete('cascade');
            
            $table->string('estado', 50)->nullable();
            
            // BORRAMOS specificType DE AQUÍ
            
            $table->timestamps();
        });

        // AGREGAMOS LA GEOMETRÍA POR FUERA
        DB::statement('ALTER TABLE nichos_geom ADD COLUMN geom geometry(MULTIPOLYGON, 4326)');

        // Índice espacial
        DB::statement('CREATE INDEX idx_nichos_geom_geom ON nichos_geom USING GIST (geom);');
    }

    public function down()
    {
        Schema::dropIfExists('nichos_geom');
    }
};