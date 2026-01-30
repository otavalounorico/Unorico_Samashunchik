<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\Pago;
use OwenIt\Auditing\Contracts\Auditable;

class Socio extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'socios';
    protected $guarded = []; // Esto permite los nuevos campos automáticamente

    protected $casts = [
        'fecha_nac' => 'date',
        'fecha_inscripcion' => 'date',
        'fecha_exoneracion' => 'date',
// 'es_representante' => 'boolean',  <-- ¡BORRA ESTA LÍNEA! CAUSA EL ERROR
    ];

    protected static function booted()
    {
        static::creating(function ($socio) {
            $ultimoId = Socio::max('id') ?? 0;
            $nuevoId = $ultimoId + 1;
            $socio->codigo = 'SOC' . str_pad($nuevoId, 4, '0', STR_PAD_LEFT);
            
            $socio->created_by = auth()->id() ?? 1;

            // SOLUCIÓN DEFINITIVA: 
            // Enviamos la letra 'f'. Postgres la interpreta como FALSE sin dar error.
            if (!isset($socio->es_representante)) {
                $socio->es_representante = 'f'; 
            }
        });
    }
    public function getEdadAttribute()
    {
        return $this->fecha_nac ? $this->fecha_nac->age : 'N/A';
    }

    public function getAniosInscritoAttribute()
    {
        return $this->fecha_inscripcion ? $this->fecha_inscripcion->age : 0;
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class);
    }
    public function genero()
    {
        return $this->belongsTo(Genero::class);
    }
    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class, 'estado_civil_id');
    }
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function nichos()
    // {
    //     return $this->belongsToMany(Nicho::class, 'socio_nicho')
    //         ->using(SocioNicho::class)
    //         ->withPivot('rol', 'desde', 'hasta')
    //         ->withTimestamps();
    // }

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->apellidos ?? '') . ' ' . ($this->nombres ?? ''));
    }

    public function scopeBuscar($q, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '')
            return $q;
        return $q->where(function ($qq) use ($term) {
            $qq->where('cedula', 'ILIKE', "%{$term}%")
                ->orWhere('nombres', 'ILIKE', "%{$term}%")
                ->orWhere('apellidos', 'ILIKE', "%{$term}%")
                ->orWhere('codigo', 'ILIKE', "%{$term}%");
        });
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class)->orderBy('anio_pagado', 'desc');
    }

    public function getAniosDeudaAttribute()
    {
        if (!$this->fecha_inscripcion)
            return [];

        $anioInicio = $this->fecha_inscripcion->year;
        $anioActual = now()->year;

        $aniosDebidos = range($anioInicio, $anioActual);
        $aniosPagados = $this->pagos->pluck('anio_pagado')->toArray();

        return array_values(array_diff($aniosDebidos, $aniosPagados));
    }

    public function nichos()
    {
        // Relación directa: Un socio tiene muchos nichos asociados a su ID
        return $this->hasMany(Nicho::class, 'socio_id');
    }
}