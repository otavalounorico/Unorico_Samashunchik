<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bloques', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 64)->unique();
            $t->string('nombre', 255);
            $t->text('descripcion')->nullable();
            $t->json('gis_id')->nullable();       // si tienes id externo GIS
            $t->unsignedInteger('area')->nullable(); // m2 aprox
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();
        });

        // Geometría (polígono): requiere PostGIS
        DB::statement("ALTER TABLE bloques ADD COLUMN geom geometry(POLYGON, 4326)");
        DB::statement("CREATE INDEX bloques_geom_gix ON bloques USING GIST (geom)");
    }
    public function down(): void
    {
        Schema::dropIfExists('bloques');
    }
};
