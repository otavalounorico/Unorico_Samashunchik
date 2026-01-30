<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Bloque extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'bloques';
    protected $guarded = [];

    protected $casts = [
        'geom' => 'array',
        'area_m2' => 'decimal:2',
    ];

    /**
     * RELACIONES
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bloqueGeom()
    {
        return $this->belongsTo(BloqueGeom::class, 'bloque_geom_id');
    }

    // --- LOGICA DE CÓDIGO AUTOMÁTICO ---
    protected static function booted()
    {
        static::creating(function ($bloque) {
            
            // 1. Asignar creador si no existe
            $bloque->created_by = auth()->id() ?? 1;

            // 2. Generación de Código (RESPALDO)
            // Solo generamos un código automático SI NO VIENE UNO YA DEFINIDO.
            // Si el Controlador ya le asignó "B-20" del GIS, esta parte se salta.
            if (empty($bloque->codigo)) {
                $ultimoId = Bloque::max('id') ?? 0;
                $nuevoId = $ultimoId + 1;
                
                // Usamos un prefijo distinto (INT) para diferenciar los
                // que creaste manualmente sin mapa, de los que vienen del GIS (B-XX).
                $bloque->codigo = 'INT-' . str_pad($nuevoId, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}