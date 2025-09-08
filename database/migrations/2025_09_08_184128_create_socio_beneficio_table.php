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
        Schema::create('socio_beneficio', function (Blueprint $t) {
            $t->id();
            $t->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $t->foreignId('beneficio_id')->constrained('beneficios')->restrictOnDelete();
            $t->date('desde')->nullable();
            $t->date('hasta')->nullable();
            $t->string('estado', 20)->default('ACTIVO'); // ACTIVO/INACTIVO/VENCIDO
            $t->string('nota', 120)->nullable();
            $t->timestampsTz();
            $t->unique(['socio_id', 'beneficio_id', 'desde']);
            $t->index(['estado']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('socio_beneficio');
    }
};
