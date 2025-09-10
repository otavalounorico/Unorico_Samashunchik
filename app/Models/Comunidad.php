<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunidad extends Model
{
    protected $guarded = [];
    public function parroquia() { return $this->belongsTo(Parroquia::class); }
}
