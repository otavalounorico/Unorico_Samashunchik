<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficio extends Model
{
    protected $table = 'beneficios';
    protected $guarded = [];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    /**
     * Logica de autogeneración del código al crear.
     */
    protected static function booted()
    {
        static::creating(function ($beneficio) {
            // Solo genera si no viene un código manual
            if (empty($beneficio->codigo)) {
                // 1. Buscamos el último ID para calcular el siguiente
                // OJO: Esto es una aproximación simple. En sistemas de altísima concurrencia
                // se usarían secuencias de DB o UUIDs, pero para reportes esto es más legible.
                $ultimo = static::orderBy('id', 'desc')->first();
                $siguienteId = $ultimo ? $ultimo->id + 1 : 1;

                // 2. Formateamos: BENN001, BENN002, etc.
                $beneficio->codigo = 'BEN' . str_pad($siguienteId, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}