<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canton;


class CantonController extends Controller
{
public function index(Request $request)
{
    $q = trim($request->get('q', ''));

    $query = Canton::orderBy('nombre');

    // Postgres: ILIKE | MySQL: LIKE
    if ($q !== '') {
        $query->where('nombre', 'ILIKE', "%{$q}%"); // cambia a 'LIKE' si usas MySQL
    }

    // 👇 retorna paginator (ya puedes usar ->links())
    $cantones = $query->paginate(10)->withQueryString();

    return view('cantones.canton-index', compact('cantones'));
}

    public function create()
    {
        return view('cantones.canton-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:cantones,nombre',
        ]);

        try {
            Canton::create([
                'nombre' => $request->nombre,
            ]);
            return redirect()->route('cantones.index')->with('success', 'Cantón creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al crear: '.$e->getMessage());
        }
    }

    public function edit(Canton $canton)
    {
        return view('cantones.canton-edit', compact('canton'));
    }

    public function update(Request $request, Canton $canton)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:cantones,nombre,' . $canton->id,
        ]);

        try {
            $canton->update([
                'nombre' => $request->nombre,
            ]);
            return redirect()->route('cantones.index')->with('success', 'Cantón actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function show(Canton $canton)
    {
        $canton->load('parroquias');
        return view('cantones.canton-show', compact('canton'));
    }

    public function destroy(Canton $canton)
    {
        try {
            $canton->delete();
            return redirect()->route('cantones.index')->with('success', 'Cantón eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('cantones.index')->with('error', 'No se puede eliminar: tiene parroquias asociadas.');
        }
    }
}