<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected static function booted()
    {
        static::created(function ($role) {
            // Esto rellena el codigo automaticamente: ID 1 => R001
            $role->codigo = 'R' . str_pad($role->id, 3, '0', STR_PAD_LEFT);
            $role->saveQuietly();
        });
    }
}