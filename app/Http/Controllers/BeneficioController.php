<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficio;

// Importaciones para Reportes
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BeneficiosExport;

class BeneficioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));

        $query = Beneficio::orderBy('id','asc');

        if ($q !== '') {
            $query->where(function($w) use ($q) {
                $w->where('nombre','ILIKE',"%{$q}%")
                  ->orWhere('codigo','ILIKE',"%{$q}%") // <-- Busqueda por código agregada
                  ->orWhere('descripcion','ILIKE',"%{$q}%")
                  ->orWhere('tipo','ILIKE',"%{$q}%");
            });
        }

        $beneficios = $query->paginate(10)->withQueryString();

        return view('beneficios.beneficio-index', compact('beneficios','q'));
    }

    public function create()
    {
        return view('beneficios.beneficio-create');
    }

    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo'        => 'required|string|max:10',
            'valor'       => 'nullable|numeric',
            // El codigo se genera solo, pero si quisieras permitir manual:
            // 'codigo' => 'nullable|string|unique:beneficios,codigo' 
        ]);

        try {
            // No enviamos 'codigo', el Modelo lo generará automáticamente (BEN-XXXX)
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
        return view('beneficios.beneficio-show', compact('beneficio'));
    }

    public function edit(Beneficio $beneficio)
    {
        return view('beneficios.beneficio-edit', compact('beneficio'));
    }

    public function update(Request $request, Beneficio $beneficio)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo'        => 'required|string|max:10',
            'valor'       => 'nullable|numeric',
            // En update ignoramos el ID actual para que no de error de unique consigo mismo
            'codigo'      => 'nullable|string|max:20|unique:beneficios,codigo,'.$beneficio->id,
        ]);

        try {
            $data = [
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo'        => $request->tipo,
                'valor'       => $request->valor,
            ];

            // Solo actualizamos el código si el usuario lo envió explícitamente (raro, pero posible)
            if($request->filled('codigo')) {
                $data['codigo'] = $request->codigo;
            }

            $beneficio->update($data);
            
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

    // ── REPORTES PDF Y EXCEL (ADAPTADO) ────────────────────────────────
    public function reports(Request $request)
    {
        // 1. Recibir IDs seleccionados desde la vista (checkboxes)
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un beneficio para generar el reporte.');
        }

        // 2. Buscar los datos
        $beneficios = Beneficio::whereIn('id', $ids)
            ->orderBy('nombre')
            ->get();

        // 3. Definir Encabezados
        $headings = [
            'ID',
            'Código',
            'Nombre del Beneficio',
            'Descripción',
            'Tipo',
            'Valor ($)',
            'Fecha Creación'
        ];

        // 4. Mapear datos (Dar formato bonito)
        $data = $beneficios->map(function ($b) {
            return [
                'id'          => $b->id,
                'codigo'      => $b->codigo, // El campo nuevo que agregamos
                'nombre'      => $b->nombre,
                'descripcion' => $b->descripcion ?? 'N/A',
                'tipo'        => strtoupper($b->tipo),
                'valor'       => $b->valor ? number_format($b->valor, 2) : '0.00',
                'created_at'  => $b->created_at ? $b->created_at->format('d/m/Y') : '',
            ];
        });

        // 5. Generar Excel
        if ($reportType === 'excel') {
            $fileName = 'beneficios_reporte_' . date('YmdHis') . '.xlsx';
            return Excel::download(new BeneficiosExport($data, $headings), $fileName);
        } 
        
        // 6. Generar PDF
        elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('beneficios.reports-pdf', compact('data', 'headings'));
            // Usamos portrait (vertical) porque son pocas columnas, landscape si fueran muchas
            $pdf->setPaper('A4', 'portrait'); 
            return $pdf->download('beneficios_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}