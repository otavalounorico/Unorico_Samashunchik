<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable;

class Fallecido extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'fallecidos';
    protected $guarded = [];

    protected $casts = [
        'fecha_nac' => 'date',
        'fecha_fallecimiento' => 'date',
    ];

    // ── 1. GENERACIÓN AUTOMÁTICA DE CÓDIGO (FAL0001) ────────────────
    protected static function booted()
    {
        static::creating(function ($fallecido) {
            $ultimoId = Fallecido::max('id') ?? 0;
            $nuevoId = $ultimoId + 1;
            
            // Genera: FAL0001, FAL0002...
            $fallecido->codigo = 'FAL' . str_pad($nuevoId, 4, '0', STR_PAD_LEFT);
        });
    }

    // ── 2. SCOPE DE BÚSQUEDA ────────────────────────────────────────
    // Busca por Cédula, Apellidos, Nombres o Código
    public function scopeBuscar($query, $term)
    {
        $term = trim($term);
        if ($term === '') return $query;

        return $query->where(function($q) use ($term) {
            $q->where('cedula', 'ILIKE', "%{$term}%")
              ->orWhere('apellidos', 'ILIKE', "%{$term}%")
              ->orWhere('nombres', 'ILIKE', "%{$term}%")
              ->orWhere('codigo', 'ILIKE', "%{$term}%");
        });
    }

    // ── 3. RELACIONES ───────────────────────────────────────────────
    public function comunidad()   { return $this->belongsTo(Comunidad::class); }
    public function genero()      { return $this->belongsTo(Genero::class); }
    public function estadoCivil() { return $this->belongsTo(EstadoCivil::class, 'estado_civil_id'); }
    public function creador()     { return $this->belongsTo(User::class, 'created_by'); }

    // Relación con Nichos (Usando el modelo Pivot corregido)
    public function nichos()
    {
        return $this->belongsToMany(Nicho::class, 'fallecido_nicho')
                    ->using(FallecidoNicho::class)
                    ->withPivot('socio_id',
                    'codigo','posicion', 'fecha_inhumacion', 'fecha_exhumacion')
                    ->withTimestamps();
    }
}
