<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Nicho extends Model implements Auditable
{
    use SoftDeletes; 
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'nichos';
    protected $guarded = [];

    protected $casts = [
        // 'disponible' => 'boolean',
    ];

    /**
     * RELACIONES
     */
    public function bloque() { return $this->belongsTo(Bloque::class); }
    public function creador() { return $this->belongsTo(User::class, 'created_by'); }
    public function socio() { return $this->belongsTo(Socio::class, 'socio_id'); }
    
    // Nueva relación con el mapa
    public function nichoGeom() { return $this->belongsTo(NichoGeom::class, 'nicho_geom_id'); }

    // Relaciones Pivot
    public function socios()
    {
        return $this->belongsToMany(Socio::class, 'socio_nicho')
                    ->using(SocioNicho::class)
                    ->withPivot('rol', 'desde', 'hasta')
                    ->withTimestamps();
    }

    public function fallecidos()
    {
        return $this->belongsToMany(Fallecido::class, 'fallecido_nicho')
                    ->using(FallecidoNicho::class)
                    ->withPivot('socio_id','codigo','posicion', 'fecha_inhumacion', 'fecha_exhumacion', 'observacion')
                    ->withTimestamps();
    }

    /**
     * GENERACIÓN DE CÓDIGO AUTOMÁTICO (Solo si no viene del mapa)
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            
            // Si el controlador NO le pasó un código (porque es un nicho manual),
            // generamos uno secuencial: N0001, N0002...
            if (empty($model->codigo)) {
                $last = self::withTrashed()->where('codigo', 'LIKE', 'N%')->orderBy('id', 'desc')->first();
                
                // Extraemos el número si existe, sino empezamos en 1
                $number = 0;
                if ($last && preg_match('/N(\d+)/', $last->codigo, $matches)) {
                    $number = intval($matches[1]);
                }
                
                $next = $number + 1;
                $model->codigo = 'N' . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
            
            // Asignar creador
            $model->created_by = auth()->id() ?? 1;
        });
    }
}