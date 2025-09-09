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
        Schema::create('socio_nicho', function (Blueprint $t) {
            $t->id();
            $t->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $t->foreignId('nicho_id')->constrained('nichos')->restrictOnDelete();
            $t->string('rol', 20)->default('TITULAR'); // 'TITULAR','CO-TITULAR','RESPONSABLE'
            $t->date('desde')->nullable();
            $t->date('hasta')->nullable();
            $t->timestampsTz();
            $t->unique(['socio_id', 'nicho_id', 'rol']); // no duplicar mismo rol
            $t->index(['nicho_id', 'rol']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('socio_nicho');
    }
};
