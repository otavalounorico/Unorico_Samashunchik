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
        Schema::create('nichos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('bloque_id')->constrained('bloques')->cascadeOnUpdate()->restrictOnDelete();
            $t->string('codigo', 40);                 // único por bloque
            $t->unsignedInteger('capacidad')->default(1);
            $t->string('estado', 20)->default('DISPONIBLE'); // catálogo simple
            $t->uuid('qr_uuid')->nullable()->unique();
            $t->boolean('disponible')->default(true);
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();
            $t->unique(['bloque_id', 'codigo']);
            $t->index(['estado', 'disponible']);
        });

        // Punto/centroide del nicho (PostGIS)
        DB::statement("ALTER TABLE nichos ADD COLUMN geom geometry(POINT, 4326)");
        DB::statement("CREATE INDEX nichos_geom_gix ON nichos USING GIST (geom)");
    }
    public function down(): void
    {
        Schema::dropIfExists('nichos');
    }
};
