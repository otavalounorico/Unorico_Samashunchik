<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficio;

class BeneficioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));

        $query = Beneficio::orderBy('nombre');

        // Postgres: ILIKE | MySQL: LIKE
        if ($q !== '') {
            $query->where(function($w) use ($q) {
                $w->where('nombre','ILIKE',"%{$q}%")
                  ->orWhere('descripcion','ILIKE',"%{$q}%")
                  ->orWhere('tipo','ILIKE',"%{$q}%");
            });
        }

        $beneficios = $query->paginate(10)->withQueryString();

        return view('beneficios.index', compact('beneficios','q'));
    }

    public function create()
    {
        return view('beneficios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo'        => 'required|string|max:10',     // según tu schema
            'valor'       => 'nullable|numeric',           // decimal(7,2)
        ]);

        try {
            Beneficio::create([
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo'        => $request->tipo,
                'valor'       => $request->valor,
            ]);
            return redirect()->route('beneficios.index')->with('success','Beneficio creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error','Error al crear: '.$e->getMessage());
        }
    }

    public function show(Beneficio $beneficio)
    {
        return view('beneficios.show', compact('beneficio'));
    }

    public function edit(Beneficio $beneficio)
    {
        return view('beneficios.edit', compact('beneficio'));
    }

    public function update(Request $request, Beneficio $beneficio)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo'        => 'required|string|max:10',
            'valor'       => 'nullable|numeric',
        ]);

        try {
            $beneficio->update([
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo'        => $request->tipo,
                'valor'       => $request->valor,
            ]);
            return redirect()->route('beneficios.index')->with('success','Beneficio actualizado.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error','Error al actualizar: '.$e->getMessage());
        }
    }

    public function destroy(Beneficio $beneficio)
    {
        try {
            $beneficio->delete();
            return redirect()->route('beneficios.index')->with('success','Beneficio eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('beneficios.index')->with('error','Error al eliminar: '.$e->getMessage());
        }
    }
}
