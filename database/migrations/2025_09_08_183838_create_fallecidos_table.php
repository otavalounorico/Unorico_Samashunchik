<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fallecidos', function (Blueprint $t) {
            $t->id();
            // 1. Código único automático
            $t->string('codigo', 20)->unique()->nullable()->after('id');
            
            $t->foreignId('comunidad_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $t->foreignId('genero_id')->nullable()->constrained('generos')->nullOnDelete();
            $t->foreignId('estado_civil_id')->nullable()->constrained('estados_civiles')->nullOnDelete();
            $t->string('cedula', 20)->nullable()->unique();
            $t->string('nombres', 255);
            $t->string('apellidos', 255);
            $t->date('fecha_nac')->nullable();
            $t->date('fecha_fallecimiento')->nullable();
            $t->text('observaciones')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();
            
            // 2. CORRECCIÓN: Índice compuesto para búsqueda rápida
            // Incluimos cédula, apellidos, nombres y código
            $t->index(['cedula', 'apellidos', 'nombres', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fallecidos');
    }
};