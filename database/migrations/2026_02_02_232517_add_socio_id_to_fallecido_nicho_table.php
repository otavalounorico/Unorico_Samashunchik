<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('fallecido_nicho', function (Blueprint $table) {
        // Añadimos la columna socio_id después de fallecido_id
        $table->unsignedBigInteger('socio_id')->nullable()->after('fallecido_id');

        // Definimos la llave foránea
        $table->foreign('socio_id')
              ->references('id')
              ->on('socios')
              ->onDelete('set null'); // O 'cascade' según tu lógica
    });
}

public function down(): void
{
    Schema::table('fallecido_nicho', function (Blueprint $table) {
        $table->dropForeign(['socio_id']);
        $table->dropColumn('socio_id');
    });
}
};
