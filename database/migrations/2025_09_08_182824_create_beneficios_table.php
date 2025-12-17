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
        Schema::create('beneficios', function (Blueprint $t) {
            $t->id();
            
            // --- AQUÍ ESTÁ LA COLUMNA QUE FALTABA ---
            $t->string('codigo', 20)->unique(); // Código único (ej: BEN001)
            // ----------------------------------------

            $t->string('nombre', 255);
            $t->text('descripcion')->nullable();
            $t->string('tipo', 30);           // p.ej: 'INSCRIPCION','ANUALIDAD'
            $t->decimal('valor', 7, 2)->nullable();
            $t->timestampsTz();
            
            $t->index(['tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficios');
    }
};