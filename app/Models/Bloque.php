<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bloque extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bloques';
    protected $guarded = [];

    protected $casts = [
        'geom' => 'array',
        'area_m2' => 'decimal:2',
    ];

    // --- CAMBIO: Generar código automático ---
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->codigo)) {
                $last = self::withTrashed()->orderBy('id', 'desc')->first();
                $next = $last ? $last->id + 1 : 1;
                $model->codigo = 'B' . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
        });
    }
    // -----------------------------------------

    /** Relaciones **/
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bloqueGeom()
    {
        return $this->belongsTo(BloqueGeom::class, 'bloque_geom_id');
    }
}