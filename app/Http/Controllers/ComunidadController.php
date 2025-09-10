<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comunidad;
use App\Models\Parroquia;

class ComunidadController extends Controller
{
    public function index()
    {
        $comunidades = Comunidad::with('parroquia.canton')->orderBy('nombre')->get();
        return view('comunidades.comunidad-index', compact('comunidades'));
    }

    public function create()
    {
        $parroquias = Parroquia::orderBy('nombre')->get(['id','nombre']);
        return view('comunidades.comunidad-create', compact('parroquias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'parroquia_id' => 'required|exists:parroquias,id',
            // unicidad del nombre dentro de la parroquia
            'nombre'       => 'required|string|max:255|unique:comunidades,nombre,NULL,id,parroquia_id,' . $request->parroquia_id,
        ]);

        try {
            Comunidad::create([
                'parroquia_id' => $request->parroquia_id,
                'nombre'       => $request->nombre,
            ]);
            return redirect()->route('comunidades.index')->with('success', 'Comunidad creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al crear: '.$e->getMessage());
        }
    }

    public function edit(Comunidad $comunidad)
    {
        $parroquias = Parroquia::orderBy('nombre')->get(['id','nombre']);
        return view('comunidades.comunidad-edit', compact('comunidad','parroquias'));
    }

    public function update(Request $request, Comunidad $comunidad)
    {
        $request->validate([
            'parroquia_id' => 'required|exists:parroquias,id',
            // ignora el propio id y mantiene unicidad por parroquia_id
            'nombre'       => 'required|string|max:255|unique:comunidades,nombre,' . $comunidad->id . ',id,parroquia_id,' . $request->parroquia_id,
        ]);

        try {
            $comunidad->update([
                'parroquia_id' => $request->parroquia_id,
                'nombre'       => $request->nombre,
            ]);
            return redirect()->route('comunidades.index')->with('success', 'Comunidad actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function show(Comunidad $comunidad)
    {
        $comunidad->load('parroquia.canton');
        return view('comunidades.comunidad-show', compact('comunidad'));
    }

    public function destroy(Comunidad $comunidad)
    {
        try {
            $comunidad->delete();
            return redirect()->route('comunidades.index')->with('success', 'Comunidad eliminada.');
        } catch (\Exception $e) {
            return redirect()->route('comunidades.index')->with('error', 'Error al eliminar: '.$e->getMessage());
        }
    }

    // Para AJAX de combos (comunidades por parroquia)
    public function byParroquia(Parroquia $parroquia)
    {
        return response()->json(
            $parroquia->comunidades()->orderBy('nombre')->get(['id','nombre'])
        );
    }
}