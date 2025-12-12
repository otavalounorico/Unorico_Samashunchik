<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
        Schema::create('bloques', function (Blueprint $t) {
            $t->id();
            
            // --- CAMBIO: Campo agregado ---
            $t->string('codigo', 10)->unique();
            $t->string('nombre', 255);
            $t->text('descripcion')->nullable();

            // FK hacia bloques_geom
            $t->foreignId('bloque_geom_id')
                ->nullable()
                ->constrained('bloques_geom')
                ->nullOnDelete();

            $t->decimal('area_m2', 12, 2)->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tu código original del down
        if (Schema::hasTable('bloques')) {
            Schema::table('bloques', function (Blueprint $table) {
                try {
                    $table->dropForeign(['bloque_geom_id']);
                } catch (\Throwable $e) {
                }
            });
        }

        Schema::dropIfExists('bloques');
    }
};