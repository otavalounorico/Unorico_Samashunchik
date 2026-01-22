<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nichos', function (Blueprint $t) {
            $t->id();
            
            $t->foreignId('socio_id')
              ->nullable()
              ->constrained('socios') // Asumo que tu tabla se llama 'socios'
              ->nullOnDelete();       // Si se borra el socio, el nicho queda libre (null)

            // 2. UBICACIÓN (Bloque físico)
            $t->foreignId('bloque_id')
              ->constrained('bloques')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

            // 3. RELACIÓN GEOMÉTRICA (QGIS/PostGIS)
            // Importante: nullable() porque dijiste que no todos están dibujados aún.
            // Asumo que la tabla donde guardas la info de QGIS se llama 'nichos_geom'.
            $t->foreignId('nicho_geom_id')
              ->nullable()
              ->constrained('nichos_geom') 
              ->nullOnDelete();

            // 4. DATOS ESPECÍFICOS DEL NICHO
            $t->string('codigo', 10)->unique(); // N001
            
            // Aquí definimos si es PROPIO o COMPARTIDO
            // Usamos enum para asegurar que solo entren esos dos valores.
            $t->enum('tipo_nicho', ['PROPIO', 'COMPARTIDO'])->default('PROPIO');
            
            $t->text('descripcion')->nullable(); // Detalles adicionales

            // IDENTIFICACIÓN DIGITAL
            $t->uuid('qr_uuid')->nullable()->unique();

            // CAPACIDAD Y ESTADO
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