<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parroquia extends Model
{
    protected $guarded = [];
    public function canton() { return $this->belongsTo(Canton::class); }
    public function comunidades() { return $this->hasMany(Comunidad::class); }
}