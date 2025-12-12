<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; // Importante para validar unique al editar
use App\Models\Bloque;
use App\Models\BloqueGeom;

// Librerías para Reportes
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BloquesExport; // ¡OJO! Debes crear este archivo (te explico abajo)

class BloqueController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        // Ordenamos por código (B0001, B0002...)
        $query = Bloque::orderBy('codigo', 'asc');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('codigo', 'ILIKE', "%{$q}%")
                  ->orWhere('nombre', 'ILIKE', "%{$q}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$q}%");
            });
        }

        $bloques = $query->paginate(10)->withQueryString();

        return view('bloques.bloque-index', compact('bloques'));
    }

    public function create()
    {
        $bloquesGeom = BloqueGeom::unassigned()->select('id', 'nombre')->get();
        return view('bloques.bloque-create', compact('bloquesGeom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // NO validamos 'codigo' aquí, el Modelo lo genera automático (B0001...)
            'nombre'         => 'required|string|max:150',
            'descripcion'    => 'nullable|string',
            'area_m2'        => 'nullable|numeric|min:0',
            'bloque_geom_id' => 'nullable|exists:bloques_geom,id',
            'geom'           => 'nullable|string', 
        ]);

        DB::beginTransaction();
        try {
            $bloque = Bloque::create([
                'nombre'         => $request->nombre,
                'descripcion'    => $request->descripcion,
                'area_m2'        => $request->area_m2,
                'bloque_geom_id' => $request->bloque_geom_id,
                'created_by'     => auth()->id(),
            ]);

            // Lógica PostGIS original
            if ($request->filled('geom')) {
                DB::statement(
                    "UPDATE bloques SET geom = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?",
                    [$request->geom, $bloque->id]
                );
            } elseif ($request->filled('bloque_geom_id')) {
                DB::statement(
                    "UPDATE bloques SET geom = (SELECT geom FROM bloques_geom WHERE id = ?) WHERE id = ?",
                    [$request->bloque_geom_id, $bloque->id]
                );
            }

            // Calcular área si falta
            if (empty($bloque->area_m2)) {
                DB::statement(
                    "UPDATE bloques SET area_m2 = ST_Area(geom::geography) WHERE id = ? AND geom IS NOT NULL",
                    [$bloque->id]
                );
            }

            DB::commit();

            return redirect()->route('bloques.index')
                ->with('success', 'Bloque generado con código: ' . $bloque->codigo);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear: '.$e->getMessage());
        }
    }

    public function show(Bloque $bloque)
    {
        $bloque->load('bloqueGeom', 'creador');
        return view('bloques.bloque-show', compact('bloque'));
    }

    public function edit(Bloque $bloque)
    {
        $bloquesGeom = BloqueGeom::whereNotIn('id', function ($q) {
            $q->select('bloque_geom_id')->from('bloques')->whereNotNull('bloque_geom_id');
        })
        ->orWhere('id', $bloque->bloque_geom_id)
        ->select('id', 'nombre')
        ->get();

        return view('bloques.bloque-edit', compact('bloque', 'bloquesGeom'));
    }

    public function update(Request $request, Bloque $bloque)
    {
        $request->validate([
            // Validamos unicidad ignorando el ID actual
            'codigo'         => ['required', 'string', 'max:10', Rule::unique('bloques')->ignore($bloque->id)],
            'nombre'         => 'required|string|max:150',
            'descripcion'    => 'nullable|string',
            'area_m2'        => 'nullable|numeric|min:0',
            'bloque_geom_id' => 'nullable|exists:bloques_geom,id',
            'geom'           => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $bloque->update([
                'codigo'         => $request->codigo,
                'nombre'         => $request->nombre,
                'descripcion'    => $request->descripcion,
                'area_m2'        => $request->area_m2,
                'bloque_geom_id' => $request->bloque_geom_id,
            ]);

            // Lógica PostGIS original
            if ($request->filled('geom')) {
                DB::statement(
                    "UPDATE bloques SET geom = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?",
                    [$request->geom, $bloque->id]
                );
            } elseif ($request->filled('bloque_geom_id')) {
                DB::statement(
                    "UPDATE bloques SET geom = (SELECT geom FROM bloques_geom WHERE id = ?) WHERE id = ?",
                    [$request->bloque_geom_id, $bloque->id]
                );
            }

            if (empty($request->area_m2)) {
                DB::statement(
                    "UPDATE bloques SET area_m2 = ST_Area(geom::geography) WHERE id = ? AND geom IS NOT NULL",
                    [$bloque->id]
                );
            }

            DB::commit();

            return redirect()->route('bloques.index')
                ->with('success', 'Bloque actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function destroy(Bloque $bloque)
    {
        try {
            $bloque->delete();
            return redirect()->route('bloques.index')->with('success', 'Bloque eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('bloques.index')->with('error', 'No se puede eliminar: '.$e->getMessage());
        }
    }

    // ── REPORTES PDF Y EXCEL ───────────────────────────────────────
    public function reports(Request $request)
    {
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un registro.');
        }

        $bloques = Bloque::with(['bloqueGeom', 'creador'])
            ->whereIn('id', $ids)
            ->orderBy('codigo', 'asc')
            ->get();

        // Encabezados
        $headings = [
            'ID',
            'Código',
            'Nombre del Bloque',
            'Descripción',
            'Área (m2)',
            'Fecha Creación'
        ];

        // Mapeo de datos
        $data = $bloques->map(function ($b) {
            return [
                'id'             => $b->id,
                'codigo'         => $b->codigo,
                'nombre'         => $b->nombre,
                'descripcion'    => $b->descripcion ?? '---',
                'area_m2'        => $b->area_m2 ? number_format($b->area_m2, 2) : '0.00',
                'fecha_creacion' => $b->created_at ? $b->created_at->format('d/m/Y') : '',
            ];
        });

        if ($reportType === 'excel') {
            return Excel::download(new BloquesExport($data, $headings), 'bloques_reporte.xlsx');
            
        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('bloques.reports-pdf', compact('data', 'headings'));
            
            // --- CAMBIO AQUÍ: 'portrait' para hoja vertical ---
            $pdf->setPaper('A4', 'portrait'); 
            
            return $pdf->download('bloques_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back();
    }
}