<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canton extends Model
{
    use HasFactory;

    // 👇 fuerza el nombre real de la tabla
    protected $table = 'cantones';

    // opcional: si tu PK no es "id", descomenta y pon el correcto
    // protected $primaryKey = 'canton_id';

    protected $guarded = [];

    public function parroquias()
    {
        return $this->hasMany(Parroquia::class);
    }
}
