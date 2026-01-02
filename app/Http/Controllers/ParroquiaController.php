<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parroquia;
use App\Models\Canton;

// 👇 IMPORTANTE: Librerías para PDF y Excel
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericExport; // Asumo que tienes esta clase exportable genérica

class ParroquiaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('search', ''));
        $cantonId = $request->get('canton_id');

        $parroquiasQuery = Parroquia::with('canton')->orderBy('codigo', 'asc');

        if ($q !== '') {
            $parroquiasQuery->where(function($query) use ($q){
                $query->where('nombre', 'ILIKE', "%{$q}%")
                      ->orWhere('codigo', 'ILIKE', "%{$q}%");
            });
        }

        if ($cantonId) {
            $parroquiasQuery->where('canton_id', $cantonId);
        }

        $parroquias = $parroquiasQuery->paginate(10)->withQueryString();
        $cantones = Canton::orderBy('nombre')->get(['id','nombre']);

        // He mantenido 'parroquias.parroquia-index' como en tu código.
        return view('parroquias.parroquia-index', compact('parroquias','cantones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'canton_id' => 'required|exists:cantones,id',
            'nombre'    => 'required|string|max:255|unique:parroquias,nombre,NULL,id,canton_id,' . $request->canton_id,
        ]);

        try {
            Parroquia::create([
                'canton_id' => $request->canton_id,
                'nombre'    => $request->nombre,
            ]);
            return redirect()->route('parroquias.index')->with('success', 'Parroquia creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear: '.$e->getMessage());
        }
    }

    public function update(Request $request, Parroquia $parroquia)
    {
        $request->validate([
            'canton_id' => 'required|exists:cantones,id',
            'nombre'    => 'required|string|max:255|unique:parroquias,nombre,' . $parroquia->id . ',id,canton_id,' . $request->canton_id,
        ]);

        try {
            $parroquia->update([
                'canton_id' => $request->canton_id,
                'nombre'    => $request->nombre,
            ]);
            return redirect()->route('parroquias.index')->with('success', 'Parroquia actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
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

    public function byCanton(Canton $canton)
    {
        return response()->json(
            $canton->parroquias()->orderBy('nombre')->get(['id','nombre'])
        );
    }

    // 1. Método para el Modal de CREAR
    public function create()
    {
        $cantones = Canton::orderBy('nombre')->get();
        
        // Variable para controlar si se muestra solo el form o el layout completo
        $isModal = request()->ajax(); 
        
        return view('parroquias.parroquia-create', compact('cantones', 'isModal'));
    }

    // 2. Método para el Modal de EDITAR
    public function edit(Parroquia $parroquia)
    {
        $cantones = Canton::orderBy('nombre')->get();
        $isModal = request()->ajax();

        return view('parroquias.parroquia-edit', compact('parroquia', 'cantones', 'isModal'));
    }

    // 3. Método para el Modal de VER (Show)
    public function show(Parroquia $parroquia)
    {
        // Cargamos la relación del cantón para mostrar el nombre
        $parroquia->load('canton');
        $isModal = request()->ajax();

        return view('parroquias.parroquia-show', compact('parroquia', 'isModal'));
    }

    // 👇 MÉTODO CORREGIDO: GENERAR REPORTES
    public function generateReports(Request $request)
    {
        // 1. Validar que se hayan seleccionado items
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos una parroquia para el reporte.');
        }

        // 2. Obtener los datos seleccionados
        $parroquias = Parroquia::with('canton')
                            ->whereIn('id', $ids)
                            ->orderBy('codigo', 'asc')
                            ->get();

        // 3. Generar PDF (Se mantiene intacto, incluye la fecha)
        if ($request->report_type == 'pdf') {
            $pdf = Pdf::loadView('parroquias.reports-pdf', compact('parroquias'));
            return $pdf->download('parroquias_reporte_'.date('YmdHis').'.pdf');
        } 
        
        // 4. Generar Excel (CORRECCIÓN APLICADA AQUÍ)
        elseif ($request->report_type == 'excel') {
            
            // Mapeamos los datos, OMITIENDO el campo 'Creación' para evitar el error de formato.
            $dataExport = $parroquias->map(function($p) {
                return [
                    'Código'   => $p->codigo,
                    'Nombre'   => $p->nombre,
                    'Cantón'   => $p->canton->nombre ?? 'Sin Cantón',
                    // Campo 'Creación' removido intencionalmente
                ];
            });

            // Definimos el encabezado solo con los 3 campos que exportamos
            $headings = ['Código', 'Parroquia', 'Cantón'];
            
            // Usamos GenericExport, que ahora recibe datos planos y funciona sin problemas.
            return Excel::download(new GenericExport($dataExport, $headings), 'parroquias_reporte_'.date('YmdHis').'.xlsx');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}