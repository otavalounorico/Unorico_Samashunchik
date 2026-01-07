<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role; 
use Spatie\Permission\Models\Permission; 

class RolePermissionController extends Controller
{
    // Muestra la vista de edición (checkboxes agrupados)
    public function edit(Role $role)
    {
        // 1) Traemos los permisos de Spatie filtrados por guard
        $perms = Permission::where('guard_name', $role->guard_name ?? 'web')
            ->orderBy('name')
            ->get();

        // 2) Agrupamos visualmente
        $permissions = $perms->groupBy(function ($p) {
            $parts = preg_split('/\s+/', trim($p->name));
            return strtolower(end($parts)); 
        });

        // 3) Obtenemos los IDs actuales usando la relación de Spatie
        $rolePermissionIds = $role->permissions()->pluck('id')->toArray();

        return view('roles.role-permissions', compact('role','permissions','rolePermissionIds'));
    }

    // Guarda los cambios usando funciones de Spatie
    public function update(Request $request, Role $role)
    {
        // Validación
        $validated = $request->validate([
            'permissions'   => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        // 4) ✅ FUNCIÓN DE SPATIE: Sincroniza los permisos marcados
        $role->syncPermissions($validated['permissions'] ?? []);

        // Limpieza de caché de Spatie
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        // 5) Retorno con mensaje personalizado (Nombre y Código)
        return redirect()
            ->route('roles.index')
            ->with('ok', "Los permisos del rol '{$role->name}' ({$role->codigo}) han sido actualizados correctamente.");
    }
}