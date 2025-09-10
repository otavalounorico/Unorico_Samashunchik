<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parroquia;
use App\Models\Canton;

class ParroquiaController extends Controller
{
    public function index()
    {
        $parroquias = Parroquia::with('canton')->orderBy('nombre')->get();
        return view('parroquias.parroquia-index', compact('parroquias'));
    }

    public function create()
    {
        $cantones = Canton::orderBy('nombre')->get(['id','nombre']);
        return view('parroquias.parroquia-create', compact('cantones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'canton_id' => 'required|exists:cantones,id',
            // unicidad del nombre dentro del cantón
            'nombre'    => 'required|string|max:255|unique:parroquias,nombre,NULL,id,canton_id,' . $request->canton_id,
        ]);

        try {
            Parroquia::create([
                'canton_id' => $request->canton_id,
                'nombre'    => $request->nombre,
            ]);
            return redirect()->route('parroquias.index')->with('success', 'Parroquia creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al crear: '.$e->getMessage());
        }
    }

    public function edit(Parroquia $parroquia)
    {
        $cantones = Canton::orderBy('nombre')->get(['id','nombre']);
        return view('parroquias.parroquia-edit', compact('parroquia','cantones'));
    }

    public function update(Request $request, Parroquia $parroquia)
    {
        $request->validate([
            'canton_id' => 'required|exists:cantones,id',
            // ignora el propio id y mantiene unicidad por canton_id
            'nombre'    => 'required|string|max:255|unique:parroquias,nombre,' . $parroquia->id . ',id,canton_id,' . $request->canton_id,
        ]);

        try {
            $parroquia->update([
                'canton_id' => $request->canton_id,
                'nombre'    => $request->nombre,
            ]);
            return redirect()->route('parroquias.index')->with('success', 'Parroquia actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function show(Parroquia $parroquia)
    {
        $parroquia->load(['canton','comunidades']);
        return view('parroquias.parroquia-show', compact('parroquia'));
    }

    public function destroy(Parroquia $parroquia)
    {
        try {
            $parroquia->delete();
            return redirect()->route('parroquias.index')->with('success', 'Parroquia eliminada.');
        } catch (\Exception $e) {
            return redirect()->route('parroquias.index')->with('error', 'No se puede eliminar: tiene comunidades asociadas.');
        }
    }

    // Para AJAX de combos (parroquias por cantón)
    public function byCanton(Canton $canton)
    {
        return response()->json(
            $canton->parroquias()->orderBy('nombre')->get(['id','nombre'])
        );
    }
}