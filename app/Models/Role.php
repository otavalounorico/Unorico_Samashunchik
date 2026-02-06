<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

use OwenIt\Auditing\Contracts\Auditable;

class Role extends SpatieRole implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected static function booted()
    {
        static::created(function ($role) {
            // Esto rellena el codigo automaticamente: ID 1 => R001
            $role->codigo = 'R-' . str_pad($role->id, 2, '0', STR_PAD_LEFT);
            $role->saveQuietly();
        });
    }
}