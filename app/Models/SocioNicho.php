<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SocioNicho extends Model
{
    use SoftDeletes;

    protected $table = 'socio_nicho';
    protected $guarded = [];

    public function socio()   { return $this->belongsTo(Socio::class); }
    public function nicho()   { return $this->belongsTo(Nicho::class); }
    public function creador() { return $this->belongsTo(User::class, 'created_by'); }
}