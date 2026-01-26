<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nicho extends Model
{
    use SoftDeletes; 

    protected $table = 'nichos';
    protected $guarded = [];

    protected $casts = [
        // 'geom' => 'array',  <-- ELIMINADO PORQUE NO EXISTE EN LA BD
        'disponible' => 'boolean',
    ];

    /**
     * Generar código automático N0001 al crear
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->codigo)) {
                $last = self::withTrashed()->orderBy('id', 'desc')->first();
                $next = $last ? $last->id + 1 : 1;
                // Generamos N + 4 o 5 dígitos (ej: N0001)
                $model->codigo = 'N' . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /* Relaciones */
    public function bloque() 
    { 
        return $this->belongsTo(Bloque::class); 
    }

    public function creador() 
    { 
        return $this->belongsTo(User::class, 'created_by'); 
    }

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }

// Relación con Socios (Usando el modelo Pivot corregido)
    public function socios()
    {
        return $this->belongsToMany(Socio::class, 'socio_nicho')
                    ->using(SocioNicho::class) // <--- ESTO ES LO NUEVO
                    ->withPivot('rol', 'desde', 'hasta')
                    ->withTimestamps();
    }

    // Relación con Fallecidos (Usando el modelo Pivot corregido)
    public function fallecidos()
    {
        return $this->belongsToMany(Fallecido::class, 'fallecido_nicho')
                    ->using(FallecidoNicho::class) // <--- ESTO ES LO NUEVO
                    ->withPivot('posicion', 'fecha_inhumacion', 'fecha_exhumacion', 'observacion')
                    ->withTimestamps();
    }

    
}