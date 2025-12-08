<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunidad extends Model
{
    protected $table = 'comunidades';
    // Asegúrate de que 'codigo_unico' no esté en $guarded si defines $fillable
    // o que esté permitido si usas $guarded = []
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // Genera el código único antes de que el modelo sea creado
        static::creating(function ($comunidad) {
            // Busca el último ID de la tabla para generar el siguiente código
            // Se usa DB::table en un caso real para evitar problemas si se usa el modelo en la misma transacción
            // Pero para simplificar, usaremos un enfoque directo si el ID es autoincremental
            $lastId = self::max('id') ?? 0; // Obtener el último ID o 0 si la tabla está vacía
            $newId = $lastId + 1;
            $comunidad->codigo_unico = 'COM' . str_pad($newId, 3, '0', STR_PAD_LEFT);
        });
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class);
    }
}