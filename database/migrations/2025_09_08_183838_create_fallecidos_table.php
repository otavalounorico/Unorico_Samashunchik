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
        Schema::create('fallecidos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('comunidad_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $t->foreignId('genero_id')->nullable()->constrained('generos')->nullOnDelete();
            $t->foreignId('estado_civil_id')->nullable()->constrained('estados_civiles')->nullOnDelete();
            $t->string('cedula', 20)->nullable()->unique(); // puede ser null si no hay
            $t->string('nombres', 255);
            $t->string('apellidos', 255);
            $t->date('fecha_nac')->nullable();
            $t->date('fecha_fallecimiento')->nullable();
            $t->text('observaciones')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();
            $t->index(['apellidos', 'fecha_fallecimiento']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('fallecidos');
    }
};
