<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Bloque;
use App\Models\BloqueGeom;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BloquesExport;

class BloqueController extends Controller
{
    /**
     * Muestra la lista de bloques paginada y filtrable.
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        // Ordenamos por código (B-01, B-02...) para mantener el orden lógico
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

    /**
     * Formulario de creación.
     * Filtra los códigos del GIS para mostrar solo los DISPONIBLES.
     */
    public function create()
    {
        $bloquesGeom = BloqueGeom::whereNotIn('id', function ($q) {
            $q->select('bloque_geom_id')
                ->from('bloques')
                ->whereNull('deleted_at') // <--- CORRECCIÓN CLAVE: Ignorar eliminados
                ->whereNotNull('bloque_geom_id');
        })
            ->select('id', 'codigo', 'sector')
            ->orderBy('codigo', 'asc')
            ->get();

        return view('bloques.bloque-create', compact('bloquesGeom'));
    }

    /**
     * Guarda el bloque. COPIA EL CÓDIGO DEL GIS TEXTUALMENTE.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'area_m2' => 'nullable|numeric|min:0',

            // CORRECCIÓN VALIDACIÓN:
            // Usamos Rule::unique con whereNull('deleted_at') para que permita
            // reutilizar el ID si el bloque anterior fue eliminado.
            'bloque_geom_id' => [
                'nullable',
                'exists:bloques_geom,id',
                Rule::unique('bloques')->whereNull('deleted_at')
            ],
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'area_m2' => $request->area_m2,
                'bloque_geom_id' => $request->bloque_geom_id,
                'created_by' => auth()->id(),
            ];

            // Sincronizar Código
            if ($request->filled('bloque_geom_id')) {
                $geom = BloqueGeom::find($request->bloque_geom_id);
                if ($geom && $geom->codigo) {
                    // Validamos duplicados IGNORANDO ELIMINADOS
                    $existe = Bloque::where('codigo', $geom->codigo)
                        ->whereNull('deleted_at') // <--- IMPORTANTE
                        ->exists();

                    if ($existe) {
                        return back()->withInput()->with('error', "El código '{$geom->codigo}' ya está activo en el sistema.");
                    }
                    $data['codigo'] = $geom->codigo;
                }
            }

            $bloque = Bloque::create($data);

            // Calcular área (Directo de la tabla GIS)
            if (empty($bloque->area_m2) && $bloque->bloque_geom_id) {
                $area = DB::table('bloques_geom')
                    ->select(DB::raw('ST_Area(geom) as area_calc'))
                    ->where('id', $bloque->bloque_geom_id)
                    ->value('area_calc');

                if ($area)
                    $bloque->update(['area_m2' => $area]);
            }

            DB::commit();
            return redirect()->route('bloques.index')
                ->with('success', "Bloque creado. Código: [{$bloque->codigo}]");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el detalle.
     */
    public function show(Bloque $bloque)
    {
        $bloque->load('bloqueGeom', 'creador');
        return view('bloques.bloque-show', compact('bloque'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Bloque $bloque)
    {
        $bloquesGeom = BloqueGeom::whereNotIn('id', function ($q) {
            $q->select('bloque_geom_id')
                ->from('bloques')
                ->whereNull('deleted_at') // <--- CORRECCIÓN CLAVE
                ->whereNotNull('bloque_geom_id');
        })
            ->orWhere('id', $bloque->bloque_geom_id)
            ->select('id', 'codigo', 'sector')
            ->orderBy('codigo', 'asc')
            ->get();

        return view('bloques.bloque-edit', compact('bloque', 'bloquesGeom'));
    }

    /**
     * Actualiza el bloque. Sincroniza código si cambia el mapa.
     */
    public function update(Request $request, Bloque $bloque)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'bloque_geom_id' => [
                'nullable',
                'exists:bloques_geom,id',
                // Validar único ignorando eliminados y ignorando a mí mismo
                Rule::unique('bloques')
                    ->whereNull('deleted_at')
                    ->ignore($bloque->id)
            ],
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'area_m2' => $request->area_m2,
                'bloque_geom_id' => $request->bloque_geom_id,
            ];

            if ($request->filled('bloque_geom_id') && $request->bloque_geom_id != $bloque->bloque_geom_id) {
                $geom = BloqueGeom::find($request->bloque_geom_id);
                if ($geom && $geom->codigo) {
                    // Validar duplicados IGNORANDO ELIMINADOS
                    $existe = Bloque::where('codigo', $geom->codigo)
                        ->where('id', '!=', $bloque->id)
                        ->whereNull('deleted_at') // <--- IMPORTANTE
                        ->exists();

                    if ($existe) {
                        return back()->withInput()->with('error', "El código '{$geom->codigo}' ya está en uso.");
                    }
                    $data['codigo'] = $geom->codigo;
                }
            }

            $bloque->update($data);

            if (empty($request->area_m2) && $bloque->bloque_geom_id) {
                $area = DB::table('bloques_geom')
                    ->select(DB::raw('ST_Area(geom) as area_calc'))
                    ->where('id', $bloque->bloque_geom_id)
                    ->value('area_calc');

                if ($area)
                    $bloque->update(['area_m2' => $area]);
            }

            DB::commit();
            return redirect()->route('bloques.index')->with('success', 'Actualizado: ' . $bloque->codigo);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function destroy(Bloque $bloque)
    {
        try {
            $bloque->delete();
            return redirect()->route('bloques.index')->with('success', 'Bloque eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('bloques.index')->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Generación de Reportes PDF/Excel (Incluyendo Sector)
     */
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

        // Encabezados incluyendo Sector
        $headings = ['ID', 'Código', 'Nombre', 'Sector', 'Área (m2)', 'Descripción'];

        $data = $bloques->map(function ($b) {
            return [
                'id' => $b->id,
                'codigo' => $b->codigo,
                'nombre' => $b->nombre,
                'sector' => $b->bloqueGeom?->sector ?? '---', // Obtenemos Sector
                'area_m2' => $b->area_m2 ? number_format($b->area_m2, 2) : '0.00',
                'descripcion' => $b->descripcion ?? '---',
            ];
        });

        if ($reportType === 'excel') {
            return Excel::download(new BloquesExport($data, $headings), 'bloques_reporte.xlsx');
        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('bloques.reports-pdf', compact('data', 'headings'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('bloques_reporte.pdf');
        }
        return redirect()->back();
    }
}