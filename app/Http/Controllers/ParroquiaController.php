<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parroquia;
use App\Models\Canton;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericExport;

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

        return view('parroquias.parroquia-index', compact('parroquias','cantones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'canton_id' => 'required|exists:cantones,id',
            'nombre'    => 'required|string|max:255|unique:parroquias,nombre,NULL,id,canton_id,' . $request->canton_id,
        ]);

        try {
            
            $parroquia = Parroquia::create([
                'canton_id' => $request->canton_id,
                'nombre'    => $request->nombre,
            ]);

            $mensaje = "Parroquia {$parroquia->codigo} - {$parroquia->nombre} creada correctamente.";
            
            return redirect()->route('parroquias.index')->with('success', $mensaje);

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

            $mensaje = "Parroquia {$parroquia->codigo} - {$parroquia->nombre} actualizada correctamente.";

            return redirect()->route('parroquias.index')->with('success', $mensaje);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function destroy(Parroquia $parroquia)
    {
        try {
            $detalle = "{$parroquia->codigo} - {$parroquia->nombre}";

            $parroquia->delete();

            return redirect()->route('parroquias.index')->with('success', "Parroquia {$detalle} eliminada.");

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

    public function create()
    {
        $cantones = Canton::orderBy('nombre')->get();
        $isModal = request()->ajax(); 
        return view('parroquias.parroquia-create', compact('cantones', 'isModal'));
    }

    public function edit(Parroquia $parroquia)
    {
        $cantones = Canton::orderBy('nombre')->get();
        $isModal = request()->ajax();
        return view('parroquias.parroquia-edit', compact('parroquia', 'cantones', 'isModal'));
    }

    public function show(Parroquia $parroquia)
    {
        $parroquia->load('canton');
        $isModal = request()->ajax();
        return view('parroquias.parroquia-show', compact('parroquia', 'isModal'));
    }

    public function generateReports(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos una parroquia para el reporte.');
        }

        $parroquias = Parroquia::with('canton')
                            ->whereIn('id', $ids)
                            ->orderBy('codigo', 'asc')
                            ->get();

        if ($request->report_type == 'pdf') {
            $pdf = Pdf::loadView('parroquias.reports-pdf', compact('parroquias'));
            return $pdf->download('parroquias_reporte_'.date('YmdHis').'.pdf');
        } 
        elseif ($request->report_type == 'excel') {
            $dataExport = $parroquias->map(function($p) {
                return [
                    'Código'   => $p->codigo,
                    'Nombre'   => $p->nombre,
                    'Cantón'   => $p->canton->nombre ?? 'Sin Cantón',
                ];
            });

            $headings = ['Código', 'Parroquia', 'Cantón'];
            return Excel::download(new GenericExport($dataExport, $headings), 'parroquias_reporte_'.date('YmdHis').'.xlsx');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}