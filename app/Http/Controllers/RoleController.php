<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role; // ✅ IMPORTANTE: Usamos TU modelo personalizado, no el de Spatie

class RoleController extends Controller
{
    public function index()
    {
        // ✅ ORDEN FIJO: Ascendente por ID (R001, R002, R003...)
        // Usamos 'id' asc para que el orden visual coincida con el código (1=R001, 2=R002)
        // y NO se muevan de lugar cuando los edites.
        $roles = Role::with('permissions')->orderBy('id', 'asc')->paginate(12);
        
        // Retornamos la vista única que contiene la tabla y los modales
        return view('roles.role-index', compact('roles'));
    }

    public function create()
    {
        return view('roles.role-create');
    }

    // Guardar nuevo rol (Desde el Modal de Crear)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:100','unique:roles,name'],
        ]);

        // Al usar App\Models\Role, el evento "booted" de tu modelo 
        // se dispara y genera el código R00X automáticamente.
        Role::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('roles.index')
            ->with('ok', 'Rol creado correctamente.');
    }

    // Actualizar rol (Desde el Modal de Editar)
    public function update(Request $request, Role $role)
    {
        // 1. Protección: No dejar renombrar al Admin para evitar problemas de seguridad
        if ($role->name === 'Administrador' && $request->name !== 'Administrador') {
            return back()->with('error', 'No se puede renombrar el rol Administrador.');
        }

        $validated = $request->validate([
            'name' => [
                'required','string','max:100',
                Rule::unique('roles','name')->ignore($role->id),
            ],
        ]);

        // 2. Actualización:
        // Solo cambiamos el nombre. El ID y el Código (R00X) se mantienen iguales.
        // Por tanto, su posición en la tabla no cambiará.
        $role->update([
            'name'       => $validated['name'],
            'guard_name' => $role->guard_name ?? 'web',
        ]);

        return redirect()
            ->route('roles.index')
            ->with('ok', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Administrador') {
            return back()->with('error', 'No se puede eliminar el rol Administrador.');
        }

        // Validación opcional: No borrar si tiene usuarios asignados
        if ($role->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: hay usuarios usando este rol.');
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('ok', 'Rol eliminado correctamente.');
    }
}