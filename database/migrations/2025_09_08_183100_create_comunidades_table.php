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
        Schema::create('comunidades', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parroquia_id')->constrained('parroquias')->cascadeOnUpdate()->restrictOnDelete();
            $t->string('codigo_unico', 20)->unique(); 
            $t->string('nombre', 255);
            $t->timestampsTz();
            $t->unique(['parroquia_id', 'nombre']);
            $t->index('parroquia_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('comunidades');
    }
};