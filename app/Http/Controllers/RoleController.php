<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role; 

class RoleController extends Controller
{
    public function index()
    {
        // Orden ascendente por ID para mantener orden visual (R001, R002...)
        $roles = Role::with('permissions')->orderBy('id', 'asc')->paginate(12);
        
        return view('roles.role-index', compact('roles'));
    }

    public function create()
    {
        return view('roles.role-create');
    }

    // Guardar nuevo rol (Create)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:100','unique:roles,name'],
        ]);

        // 1. Guardamos el rol en una variable "$nuevoRol" para poder leer sus datos
        $nuevoRol = Role::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        // 2. Usamos el nombre y el código en el mensaje
        return redirect()
            ->route('roles.index')
            ->with('ok', "El rol '{$nuevoRol->name}' con código {$nuevoRol->codigo} ha sido creado correctamente.");
    }

    // Cargar modal de edición (Edit)
    public function edit(Role $role)
    {
        return view('roles.role-edit', compact('role'));
    }

    // Actualizar rol (Update)
    public function update(Request $request, Role $role)
    {
        // Protección para el Admin
        if ($role->name === 'Administrador' && $request->name !== 'Administrador') {
            return back()->with('error', 'No se puede renombrar el rol Administrador.');
        }

        $validated = $request->validate([
            'name' => [
                'required','string','max:100',
                Rule::unique('roles','name')->ignore($role->id),
            ],
        ]);

        $role->update([
            'name'       => $validated['name'],
            'guard_name' => $role->guard_name ?? 'web',
        ]);

        // Mensaje personalizado con variables
        return redirect()
            ->route('roles.index')
            ->with('ok', "El rol '{$role->name}' ({$role->codigo}) ha sido actualizado correctamente.");
    }

    // Eliminar rol (Destroy)
    public function destroy(Role $role)
    {
        if ($role->name === 'Administrador') {
            return back()->with('error', 'No se puede eliminar el rol Administrador.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: hay usuarios usando este rol.');
        }

        // 1. Guardamos datos TEMPORALES antes de borrar
        $nombreEliminado = $role->name;
        $codigoEliminado = $role->codigo;

        // 2. Eliminamos
        $role->delete();

        // 3. Mostramos mensaje con los datos guardados
        return redirect()
            ->route('roles.index')
            ->with('ok', "El rol '{$nombreEliminado}' ({$codigoEliminado}) ha sido eliminado correctamente.");
    }
}