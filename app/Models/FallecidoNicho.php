<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class FallecidoNicho extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'fallecido_nicho';
    public $incrementing = true; // Correcto, ya que tienes un ID primario serial

    // Agregamos los campos que se pueden llenar
    protected $fillable = [
        'codigo',
        'fallecido_id',
        'nicho_id',
        'socio_id',      // <-- Agregado
        'posicion',
        'fecha_inhumacion',
        'fecha_exhumacion',
        'observacion'
    ];

    protected $casts = [
        'fecha_inhumacion' => 'date',
        'fecha_exhumacion' => 'date',
    ];

    // Relación opcional: permite saber quién es el socio directamente desde el registro del muerto
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'socio_id');
    }
}