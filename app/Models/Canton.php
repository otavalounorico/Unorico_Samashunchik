<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
class Canton extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $table = 'cantones';

    // Permite asignación masiva
    protected $guarded = []; 

    /**
     * El método "boot" se ejecuta cuando el modelo se inicializa.
     * Aquí interceptamos el evento "creating" para autogenerar el código.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($canton) {
            // Solo generamos el código si no se ha pasado uno manualmente
            if (empty($canton->codigo)) {
                // 1. Buscamos el último cantón creado
                $ultimoCanton = static::latest('id')->first();

                if (!$ultimoCanton) {
                    // Si no hay ninguno, empezamos en 1
                    $numero = 1;
                } else {
                    // 2. Extraemos la parte numérica del último código.
                    // substr($str, 2) elimina los primeros 2 caracteres ("CA")
                    // Ej: "CA005" -> "005" -> 5
                    $numero = intval(substr($ultimoCanton->codigo, 2)) + 1;
                }

                // 3. Formateamos: prefijo "CA" + número relleno con ceros a la izquierda (3 dígitos)
                // Ej: 1 -> CA001, 99 -> CA099, 100 -> CA100
                $canton->codigo = 'CA-' . str_pad($numero, 2, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relación
    public function parroquias()
    {
        return $this->hasMany(Parroquia::class);
    }
}