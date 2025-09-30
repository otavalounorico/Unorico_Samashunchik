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
        Schema::create('factura_detalles', function (Blueprint $t) {
            $t->id(); 

            // Relación con factura
            $t->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();

            // Relación con beneficio
            $t->foreignId('beneficio_id')->constrained('beneficios')->cascadeOnDelete();

            // Detalle de la línea
            $t->integer('cantidad')->default(1);
            $t->decimal('precio', 10, 2);   // precio unitario
            $t->decimal('subtotal', 10, 2); // cantidad * precio

            $t->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_detalles');
    }
};
