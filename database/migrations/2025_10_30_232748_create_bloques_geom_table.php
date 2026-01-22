<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    
    public function up()
    {
        // 1. Habilitar PostGIS
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        // 2. Crear la tabla (SIN la columna geom todavía)
        Schema::create('bloques_geom', function (Blueprint $table) {
            $table->id(); 
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 255)->nullable();
            $table->integer('area')->nullable();
            $table->string('sector', 100)->nullable();
            $table->timestamps();
            
            // BORRA ESTA LÍNEA:
            // $table->specificType('geom', 'geometry(MULTIPOLYGON, 4326)');
        });

        // 3. Agregar la columna de geometría con SQL directo
        // Esto se ejecuta FUERA del Schema::create para evitar el error del Blueprint
        DB::statement('ALTER TABLE bloques_geom ADD COLUMN geom geometry(MULTIPOLYGON, 4326)');

        // 4. Índice Espacial
        DB::statement('CREATE INDEX IF NOT EXISTS idx_bloques_geom_geom ON bloques_geom USING GIST (geom);');
    }

    public function down()
    {
        Schema::dropIfExists('bloques_geom');
    }
};