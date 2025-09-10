<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canton extends Model
{
    protected $guarded = [];
    public function parroquias() { return $this->hasMany(Parroquia::class); }
}
