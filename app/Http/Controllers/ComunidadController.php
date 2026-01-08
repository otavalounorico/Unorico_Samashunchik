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
        $q = trim($request->get('search', '')); 
        $parroquiaId = $request->get('parroquia_id'); 

        $query = Comunidad::with('parroquia.canton')->orderBy('nombre');

        if (!empty($parroquiaId)) {
            $query->where('parroquia_id', $parroquiaId);
        }

        if ($q !== '') {
            $query->where(function($subquery) use ($q){
                $subquery->where('nombre', 'ILIKE', "%{$q}%")
                         ->orWhere('codigo_unico', 'ILIKE', "%{$q}%");
            });
        }

        $parroquias = Parroquia::orderBy('nombre')->get(['id', 'nombre']); 
        
        $comunidades = $query->paginate(10)->withQueryString();

        return view('comunidades.comunidad-index', compact('comunidades', 'parroquias'));
    }
    
    public function create()
    {
        $parroquias = Parroquia::orderBy('nombre')->get(['id','nombre']);
        return view('comunidades.comunidad-create', compact('parroquias'));
    }

    public function store(Request $request)
    {
        // 1. Validación básica (tipos de datos)
        $request->validate([
            'parroquia_id' => 'required|exists:parroquias,id',
            'nombre'       => 'required|string|max:255',
        ]);

        // 2. VALIDACIÓN MANUAL DE DUPLICADOS (Para que salga alerta roja en el Index)
        $existe = Comunidad::where('parroquia_id', $request->parroquia_id)
                            ->where('nombre', $request->nombre)
                            ->exists();

        if ($existe) {
            // Obtenemos el nombre de la parroquia para que el mensaje sea más claro
            $nombreParroquia = Parroquia::find($request->parroquia_id)->nombre ?? 'seleccionada';
            
            return redirect()->route('comunidades.index')
                ->with('error', "Error: La comunidad '{$request->nombre}' ya existe en la parroquia '{$nombreParroquia}'.");
        }

        try {
            // 3. Crear (El modelo genera el código automáticamente)
            $comunidad = Comunidad::create([
                'parroquia_id' => $request->parroquia_id,
                'nombre'       => $request->nombre,
            ]);

            // 4. Mensaje con Código y Nombre
            return redirect()->route('comunidades.index')
                ->with('success', "Comunidad {$comunidad->codigo_unico} - {$comunidad->nombre} creada correctamente.");

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
        // 1. Validación básica
        $request->validate([
            'parroquia_id' => 'required|exists:parroquias,id',
            'nombre'       => 'required|string|max:255',
        ]);

        // 2. VALIDACIÓN MANUAL AL ACTUALIZAR
        // Buscamos si existe OTRA comunidad con el mismo nombre en la misma parroquia (excluyendo la actual)
        $existe = Comunidad::where('parroquia_id', $request->parroquia_id)
                            ->where('nombre', $request->nombre)
                            ->where('id', '!=', $comunidad->id) // Ignorar la propia comunidad
                            ->exists();

        if ($existe) {
            $nombreParroquia = Parroquia::find($request->parroquia_id)->nombre ?? 'seleccionada';

            return redirect()->route('comunidades.index')
                ->with('error', "Error: Ya existe otra comunidad llamada '{$request->nombre}' en la parroquia '{$nombreParroquia}'.");
        }

        try {
            $comunidad->update([
                'parroquia_id' => $request->parroquia_id,
                'nombre'       => $request->nombre,
            ]);
            
            // 3. Mensaje con Código y Nombre
            return redirect()->route('comunidades.index')
                ->with('success', "Comunidad {$comunidad->codigo_unico} - {$comunidad->nombre} actualizada correctamente.");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function destroy(Comunidad $comunidad)
    {
        try {
            // 1. Guardar datos antes de borrar
            $datos = "{$comunidad->codigo_unico} - {$comunidad->nombre}";

            $comunidad->delete();

            // 2. Mensaje con los datos guardados
            return redirect()->route('comunidades.index')
                ->with('success', "Comunidad {$datos} eliminada.");

        } catch (\Exception $e) {
            return redirect()->route('comunidades.index')
                ->with('error', "No se pudo eliminar la comunidad {$comunidad->codigo_unico} - {$comunidad->nombre}. Detalle: " . $e->getMessage());
        }
    }

    public function show(Comunidad $comunidad)
    {
        $comunidad->load('parroquia.canton');
        return view('comunidades.comunidad-show', compact('comunidad'));
    }

    // Para AJAX de combos (comunidades por parroquia)
    public function byParroquia(Parroquia $parroquia)
    {
        return response()->json(
            $parroquia->comunidades()->orderBy('nombre')->get(['id','nombre'])
        );
    }
    
    public function reports(Request $request)
    {
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos una comunidad para el reporte.');
        }

        $comunidades = Comunidad::with('parroquia.canton')
                            ->whereIn('id', $ids)
                            ->orderBy('parroquia_id')
                            ->orderBy('nombre')
                            ->get();

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

        if ($reportType === 'excel') {
            return Excel::download(new ComunidadExport($data, $headings), 'comunidades_reporte_'.date('YmdHis').'.xlsx');
            
        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('comunidades.reports_pdf', compact('data', 'headings')); 
            return $pdf->download('comunidades_reporte_'.date('YmdHis').'.pdf');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}