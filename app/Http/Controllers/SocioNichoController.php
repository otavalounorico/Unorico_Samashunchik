<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocioNicho;
use App\Models\Socio;
use App\Models\Nicho;

class SocioNichoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));

        $query = SocioNicho::with(['socio','nicho.bloque'])->orderByDesc('id');

        if ($q !== '') {
            $query->whereHas('socio', function($w) use($q) {
                $w->where('apellidos','ILIKE',"%{$q}%")
                  ->orWhere('nombres','ILIKE',"%{$q}%")
                  ->orWhere('cedula','ILIKE',"%{$q}%");
            });
        }

        $asignaciones = $query->paginate(10)->withQueryString();

        return view('socio_nicho.index', compact('asignaciones','q'));
    }

    public function create()
    {
        $socios = Socio::orderBy('apellidos')->orderBy('nombres')->get();
        $nichos = Nicho::with('bloque')->orderBy('codigo')->get();
        return view('socio_nicho.create', compact('socios','nichos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'socio_id' => 'required|exists:socios,id',
            'nicho_id' => 'required|exists:nichos,id',
            'rol'      => 'required|string|max:50',
            'desde'    => 'nullable|date',
            'hasta'    => 'nullable|date|after_or_equal:desde',
        ]);

        try {
            SocioNicho::create([
                'socio_id'   => $request->socio_id,
                'nicho_id'   => $request->nicho_id,
                'rol'        => $request->rol,
                'desde'      => $request->desde,
                'hasta'      => $request->hasta,
                'observacion'=> $request->observacion,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('socio_nicho.index')->with('success','Asignación creada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error','Error: '.$e->getMessage());
        }
    }

    public function show(SocioNicho $socioNicho)
    {
        $socioNicho->load(['socio','nicho.bloque','creador']);
        return view('socio_nicho.show', compact('socioNicho'));
    }

    public function edit(SocioNicho $socioNicho)
    {
        $socios = Socio::orderBy('apellidos')->orderBy('nombres')->get();
        $nichos = Nicho::with('bloque')->orderBy('codigo')->get();
        return view('socio_nicho.edit', compact('socioNicho','socios','nichos'));
    }

    public function update(Request $request, SocioNicho $socioNicho)
    {
        $request->validate([
            'socio_id' => 'required|exists:socios,id',
            'nicho_id' => 'required|exists:nichos,id',
            'rol'      => 'required|string|max:50',
            'desde'    => 'nullable|date',
            'hasta'    => 'nullable|date|after_or_equal:desde',
        ]);

        try {
            $socioNicho->update([
                'socio_id'   => $request->socio_id,
                'nicho_id'   => $request->nicho_id,
                'rol'        => $request->rol,
                'desde'      => $request->desde,
                'hasta'      => $request->hasta,
                'observacion'=> $request->observacion,
            ]);

            return redirect()->route('socio_nicho.index')->with('success','Asignación actualizada.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error','Error: '.$e->getMessage());
        }
    }

    public function destroy(SocioNicho $socioNicho)
    {
        try {
            $socioNicho->delete();
            return redirect()->route('socio_nicho.index')->with('success','Asignación eliminada.');
        } catch (\Exception $e) {
            return redirect()->route('socio_nicho.index')->with('error','Error: '.$e->getMessage());
        }
    }
}