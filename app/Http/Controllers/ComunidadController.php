<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comunidad;
use App\Models\Parroquia;

// Importaciones necesarias para Reportes
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComunidadExport;

class ComunidadController extends Controller
{
    public function index(Request $request)
    {
        // Usamos 'search' en lugar de 'q' para ser consistentes con la vista PDF
        $q = trim($request->get('search', '')); 
        // Obtener el ID de la parroquia del filtro
        $parroquiaId = $request->get('parroquia_id'); 

        $query = Comunidad::with('parroquia.canton')->orderBy('nombre');

        // Aplicar filtro por Parroquia si existe
        if (!empty($parroquiaId)) {
            $query->where('parroquia_id', $parroquiaId);
        }

        // Aplicar filtro por nombre
        if ($q !== '') {
            $query->where('nombre', 'ILIKE', "%{$q}%"); // o 'LIKE' en MySQL
        }

        // Obtener todas las parroquias para el dropdown de filtro
        $parroquias = Parroquia::orderBy('nombre')->get(['id', 'nombre']); 
        
        // paginator
        $comunidades = $query->paginate(10)->withQueryString();

        // Pasar parroquias a la vista
        return view('comunidades.comunidad-index', compact('comunidades', 'parroquias'));
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
            'nombre' => 'required|string|max:255|unique:comunidades,nombre,NULL,id,parroquia_id,' . $request->parroquia_id,
        ]);

        try {
            Comunidad::create([
                'parroquia_id' => $request->parroquia_id,
                'nombre' => $request->nombre,
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
            'nombre' => 'required|string|max:255|unique:comunidades,nombre,' . $comunidad->id . ',id,parroquia_id,' . $request->parroquia_id,
        ]);

        try {
            $comunidad->update([
                'parroquia_id' => $request->parroquia_id,
                'nombre' => $request->nombre,
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
    
   /**
     * Genera reportes (Excel o PDF) de las comunidades seleccionadas.
     * REQUISITO: Debe seleccionarse al menos un checkbox (ids[]).
     */
    public function reports(Request $request)
    {
        // 1. Obtener los IDs seleccionados (checkboxes)
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        // 🚨 VALIDACIÓN ESTRICTA: Si no hay IDs seleccionados, NO genera reporte.
        // Esto imita exactamente la lógica de tu ParroquiaController.
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos una comunidad para el reporte.');
        }

        // 2. Obtener los datos basándose SOLO en los IDs seleccionados
        $comunidades = Comunidad::with('parroquia.canton')
                            ->whereIn('id', $ids)
                            ->orderBy('parroquia_id')
                            ->orderBy('nombre')
                            ->get();

        // 3. Definir encabezados y mapear los datos
        $headings = [
            'ID',
            'Código Único', 
            'Nombre Comunidad', 
            'Parroquia', 
            'Cantón', 
            'Fecha Registro',
        ];

        $data = $comunidades->map(function ($com) {
            return [
                $com->id,
                $com->codigo_unico,
                $com->nombre,
                $com->parroquia->nombre ?? 'N/A',
                $com->parroquia->canton->nombre ?? 'N/A',
                $com->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // 4. Procesar el tipo de reporte
        if ($reportType === 'excel') {
            // Usamos ComunidadExport para Excel
            return Excel::download(new ComunidadExport($data, $headings), 'comunidades_reporte_'.date('YmdHis').'.xlsx');
            
        } elseif ($reportType === 'pdf') {
            // Usamos la vista 'comunidades.reports_pdf' para PDF
            $pdf = Pdf::loadView('comunidades.reports_pdf', compact('data', 'headings')); 
            return $pdf->download('comunidades_reporte_'.date('YmdHis').'.pdf');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}