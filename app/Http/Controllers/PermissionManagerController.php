<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role; // ✅ TU modelo (con código)
use Spatie\Permission\Models\Permission; // ✅ Permiso de Spatie

class PermissionManagerController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        // Mapa para saber qué checks marcar
        $rolePerms = [];
        foreach ($roles as $role) {
            // Usamos la relación permissions() de Spatie
            $rolePerms[$role->id] = $role->permissions->pluck('id')->all();
        }

        return view('roles.role-permissions-manager', compact('roles', 'permissions', 'rolePerms'));
    }

    public function update(Request $request)
    {
        // La matriz viene del formulario
        $matrix = $request->input('permission_role', []);

        $roles = Role::all();
        $locked = ['Administrador']; 

        foreach ($roles as $role) {
            if (in_array($role->name, $locked)) {
                continue; 
            }

            // Recolectamos IDs
            $newPermIds = [];
            foreach ($matrix as $permId => $byRole) {
                if (isset($byRole[$role->id])) {
                    $newPermIds[] = (int) $permId;
                }
            }
            
            // ✅ FUNCIÓN DE SPATIE: Guardamos
            $role->syncPermissions($newPermIds);
        }

        // Aseguramos al Admin
        if ($admin = Role::where('name', 'Administrador')->first()) {
            $admin->syncPermissions(Permission::all());
        }

        // Limpiar caché
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        return back()->with('ok', 'La matriz de permisos ha sido actualizada correctamente para todos los roles.');
    }
}