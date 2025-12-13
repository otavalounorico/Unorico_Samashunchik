<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nichos', function (Blueprint $t) {
            $t->id();
            
            // UBICACIÓN
            $t->foreignId('bloque_id')
              ->constrained('bloques')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

            // IDENTIFICACIÓN VISUAL (Lo que se pinta en la pared: N001)
            $t->string('codigo', 10)->unique(); 

            // IDENTIFICACIÓN DIGITAL (Para el QR, único y eterno)
            $t->uuid('qr_uuid')->nullable()->unique();

            // CAPACIDAD Y ESTADO RÁPIDO
            $t->unsignedInteger('capacidad')->default(1);
            $t->string('estado', 20)->default('DISPONIBLE'); 
            $t->boolean('disponible')->default(true);
            
            // AUDITORÍA
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();

            $t->index(['estado', 'disponible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nichos');
    }
};