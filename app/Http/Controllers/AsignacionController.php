<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Nicho;
use App\Models\Socio;
use App\Models\Fallecido;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de importar el PDF

class AsignacionController extends Controller
{
    // --- 1. INDEX: LISTADO DE NICHOS ---
    public function index(Request $request)
    {
        // CORRECCIÓN: Filtramos para mostrar SOLO nichos que tengan Socio O Fallecido.
        // No mostramos los vacíos/disponibles sin asignar.
        $query = Nicho::with(['bloque', 'socios', 'fallecidos'])
            ->where(function($q) {
                $q->has('socios')->orHas('fallecidos');
            })
            ->orderBy('updated_at', 'desc'); // Los más recientes primero

        // Filtros opcionales
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }
        
        // Filtro para ver solo donde hubo exhumaciones
        if ($request->has('ver_exhumados') && $request->ver_exhumados == '1') {
            $query->whereHas('fallecidos', function($q) {
                $q->whereNotNull('fallecido_nicho.fecha_exhumacion');
            });
        }

        if ($request->has('search') && $request->search != '') {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                $q->where('codigo', 'ILIKE', "%{$term}%")
                  ->orWhereHas('socios', function($qs) use ($term) {
                      $qs->where('cedula', 'ILIKE', "%{$term}%")
                         ->orWhere('apellidos', 'ILIKE', "%{$term}%")
                         ->orWhere('nombres', 'ILIKE', "%{$term}%");
                  })
                  ->orWhereHas('fallecidos', function($qf) use ($term) {
                      $qf->where('cedula', 'ILIKE', "%{$term}%")
                         ->orWhere('apellidos', 'ILIKE', "%{$term}%")
                         ->orWhere('nombres', 'ILIKE', "%{$term}%");
                  });
            });
        }

        $nichos = $query->paginate(10);

        return view('asignaciones.asignacion-index', compact('nichos'));
    }

    // --- 2. CREATE: FORMULARIO DE ASIGNACIÓN ---
public function create()
    {
        try {
            // 1. NICHOS
            $nichosDisponibles = Nicho::with('bloque')
                ->withCount('fallecidos') 
                // CORRECCIÓN AQUÍ:
                // Usamos whereRaw para forzar la comparación booleana correcta en PostgreSQL
                // en lugar de ->where('disponible', true) que envía un 1.
                ->whereRaw('disponible = true') 
                ->orderBy('id', 'desc')
                ->get();

            // 2. SOCIOS
            $socios = Socio::orderBy('id', 'desc')->get();

            // 3. FALLECIDOS
            $fallecidos = Fallecido::doesntHave('nichos')
                ->orderBy('id', 'desc')
                ->get();

            return view('asignaciones.asignacion-create', compact('nichosDisponibles', 'socios', 'fallecidos'));

        } catch (\Exception $e) {
            // Si falla, te mostrará el error en pantalla
            dd("ERROR EN CREATE ASIGNACIÓN: " . $e->getMessage()); 
        }
    }

    // --- 3. SHOW: VER DETALLE ---
    public function show($id)
    {
        $nicho = Nicho::with(['socios', 'fallecidos', 'bloque'])->findOrFail($id);
        return view('asignaciones.asignacion-show', compact('nicho'));
    }

    // --- 4. EDIT: FORMULARIO DE EDICIÓN ---
    public function edit($id)
    {
        $nicho = Nicho::with(['socios', 'bloque', 'fallecidos'])->findOrFail($id);
        $socios = Socio::orderBy('apellidos')->get();

        // CORRECCIÓN IMPORTANTE:
        // Enviamos la lista de fallecidos DISPONIBLES + los que ya están en este nicho.
        // Esto permite cambiar al fallecido en el select si te equivocaste.
        $fallecidos = Fallecido::doesntHave('nichos')
                        ->orWhereHas('nichos', function($q) use ($id) {
                            $q->where('nichos.id', $id);
                        })
                        ->orderBy('apellidos')
                        ->get();

        return view('asignaciones.asignacion-edit', compact('nicho', 'socios', 'fallecidos'));
    }

    // =========================================================================
    // LÓGICA DE NEGOCIO (STORE, UPDATE, EXHUMAR, DESTROY)
    // =========================================================================

public function store(Request $request)
    {
        $request->validate([
            'nicho_id' => 'required|exists:nichos,id',
            'socio_id' => 'required|exists:socios,id',
            'fallecido_id' => 'required|exists:fallecidos,id',
            'rol' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $nicho = Nicho::findOrFail($request->nicho_id);

                // 1. Validar capacidad
                $ocupantesActivos = $nicho->fallecidos()
                    ->wherePivot('fecha_exhumacion', null)
                    ->count();

                if ($ocupantesActivos >= 3) {
                    throw new \Exception("El nicho ya está al límite (3 fallecidos).");
                }

                // 2. Asignar Socio
                $nicho->socios()->syncWithoutDetaching([
                    $request->socio_id => [
                        'rol' => $request->rol,
                        'desde' => now()
                    ]
                ]);

                // --- 3. GENERAR CÓDIGO (LÓGICA NUEVA) ---
                // Buscamos el ID más alto actual y sumamos 1
                // Usamos DB::table directo porque es más rápido para obtener el ID max
                $ultimoId = DB::table('fallecido_nicho')->max('id') ?? 0;
                $siguienteId = $ultimoId + 1;
                
                // Genera formato: ASG-00001
                $codigoGenerado = 'ASG-' . str_pad($siguienteId, 5, '0', STR_PAD_LEFT);

                // 4. Asignar Fallecido (Incluyendo el código)
                $nicho->fallecidos()->attach($request->fallecido_id, [
                    'codigo'           => $codigoGenerado, // <--- AQUÍ SE GUARDA
                    'posicion'         => $ocupantesActivos + 1,
                    'fecha_inhumacion' => now(),
                    'observacion'      => 'Ingreso registrado'
                ]);

                // 5. Actualizar Estado del Nicho
                $totalAhora = $ocupantesActivos + 1;
                $nicho->update([
                    'estado' => ($totalAhora >= 3 ? 'LLENO' : 'OCUPADO'),
                    'disponible' => ($totalAhora < 3)
                ]);
            });

            return back()->with('success', 'Asignación correcta. El nicho se ha actualizado.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- FORMULARIO EXCLUSIVO DE EXHUMACIÓN ---
    public function exhumarForm($id)
    {
        $nicho = Nicho::with(['bloque', 'fallecidos'])->findOrFail($id);
        
        // Filtramos SOLO los activos para mostrar en el select
        $fallecidosActivos = $nicho->fallecidos->where('pivot.fecha_exhumacion', null);

        if ($fallecidosActivos->isEmpty()) {
            return back()->with('error', 'Este nicho no tiene ocupantes activos para exhumar.');
        }

        return view('asignaciones.asignacion-exhumar', compact('nicho', 'fallecidosActivos'));
    }

    // --- PROCESAR EXHUMACIÓN ---
    public function exhumar(Request $request)
    {
        $request->validate([
            'nicho_id' => 'required|exists:nichos,id',
            'fallecido_id' => 'required|exists:fallecidos,id',
            'fecha_exhumacion' => 'required|date', // Validamos que venga fecha
        ]);

        DB::transaction(function () use ($request) {
            $nicho = Nicho::findOrFail($request->nicho_id);

            // Actualizamos PIVOTE (Marcar salida)
            $nicho->fallecidos()->updateExistingPivot($request->fallecido_id, [
                'fecha_exhumacion' => $request->fecha_exhumacion,
                'observacion' => $request->observacion ?? 'Exhumado / Restos retirados'
            ]);

            // Liberar espacio
            $nicho->update([
                'estado' => 'OCUPADO',
                'disponible' => true
            ]);
        });

        return back()->with('success', 'Exhumación registrada correctamente.');
    }

    // --- ACTUALIZAR / CORREGIR ERROR ---
    public function update(Request $request, $nicho_id)
    {
        $request->validate([
            'socio_id'          => 'required|exists:socios,id',
            'socio_anterior_id' => 'required|exists:socios,id',
            'fallecido_id'      => 'required|exists:fallecidos,id',
            'fallecido_anterior_id' => 'required|exists:fallecidos,id', // Validamos el anterior
            'fecha_inhumacion'  => 'required|date',
            'observacion'       => 'nullable|string',
            'rol'               => 'required|string'
        ]);

        try {
            DB::transaction(function () use ($request, $nicho_id) {
                $nicho = Nicho::findOrFail($nicho_id);

                // A. CORREGIR EL SOCIO
                if ($request->socio_id != $request->socio_anterior_id) {
                    $nicho->socios()->detach($request->socio_anterior_id);
                    $nicho->socios()->syncWithoutDetaching([
                        $request->socio_id => ['rol' => $request->rol, 'desde' => now()]
                    ]);
                } else {
                    $nicho->socios()->updateExistingPivot($request->socio_id, ['rol' => $request->rol]);
                }

                // B. CORREGIR FALLECIDO (SI HUBO ERROR DE SELECCIÓN)
                if ($request->fallecido_id != $request->fallecido_anterior_id) {
                    // 1. Sacamos al equivocado
                    $nicho->fallecidos()->detach($request->fallecido_anterior_id);
                    
                    // 2. Metemos al correcto (conservando posición si es posible)
                    $nicho->fallecidos()->attach($request->fallecido_id, [
                        'posicion' => 1, 
                        'fecha_inhumacion' => $request->fecha_inhumacion,
                        'observacion' => $request->observacion
                    ]);
                } else {
                    // Si es el mismo, solo actualizamos datos
                    $nicho->fallecidos()->updateExistingPivot($request->fallecido_id, [
                        'fecha_inhumacion' => $request->fecha_inhumacion,
                        'observacion'      => $request->observacion,
                    ]);
                }
            });

            return back()->with('success', 'Datos corregidos correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // --- ELIMINAR (BORRAR ERROR) ---
    public function destroy($nicho_id, $fallecido_id)
    {
        try {
            DB::transaction(function () use ($nicho_id, $fallecido_id) {
                $nicho = Nicho::findOrFail($nicho_id);

                // 1. Sacar al fallecido
                $nicho->fallecidos()->detach($fallecido_id);

                // 2. Recalcular Estado
                $ocupantesRestantes = $nicho->fallecidos()
                    ->wherePivot('fecha_exhumacion', null)
                    ->count();

                if ($ocupantesRestantes < 3) {
                    $nicho->update([
                        'estado' => ($ocupantesRestantes > 0) ? 'OCUPADO' : 'DISPONIBLE',
                        'disponible' => true
                    ]);
                }
            });

            return back()->with('success', 'Registro eliminado y espacio liberado.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // REPORTES PDF (AGREGADOS)
    // =========================================================================

    // 1. Reporte General (Todo lo ocupado)
    public function pdfReporteGeneral()
    {
        $asignaciones = Nicho::with(['bloque', 'socios', 'fallecidos'])
            ->where(function($q) { $q->has('socios')->orHas('fallecidos'); })
            ->orderBy('bloque_id')->orderBy('codigo')
            ->get();

        $pdf = Pdf::loadView('asignaciones.pdf-reporte-general', compact('asignaciones'));
        return $pdf->stream('Reporte_General.pdf');
    }

    // 2. Reporte de Exhumados
    public function pdfReporteExhumados()
    {
        $registros = DB::table('fallecido_nicho')
            ->join('fallecidos', 'fallecido_nicho.fallecido_id', '=', 'fallecidos.id')
            ->join('nichos', 'fallecido_nicho.nicho_id', '=', 'nichos.id')
            ->join('bloques', 'nichos.bloque_id', '=', 'bloques.id')
            ->whereNotNull('fecha_exhumacion')
            ->select(
                'fallecidos.apellidos', 'fallecidos.nombres', 'fallecidos.cedula',
                'nichos.codigo as nicho_codigo', 'bloques.descripcion as bloque',
                'fallecido_nicho.fecha_inhumacion', 'fallecido_nicho.fecha_exhumacion', 'fallecido_nicho.observacion'
            )
            ->orderBy('fecha_exhumacion', 'desc')
            ->get();

        $pdf = Pdf::loadView('asignaciones.pdf-lista-exhumados', compact('registros'));
        return $pdf->stream('Reporte_Exhumados.pdf');
    }

    // 3. Certificado Individual
    public function pdfCertificadoExhumacion($nicho_id, $fallecido_id)
    {
        $nicho = Nicho::findOrFail($nicho_id);
        $fallecido = $nicho->fallecidos()->where('fallecidos.id', $fallecido_id)->firstOrFail();

        if (!$fallecido->pivot->fecha_exhumacion) {
            return back()->with('error', 'Este fallecido aún no ha sido exhumado.');
        }

        $pdf = Pdf::loadView('asignaciones.pdf-certificado', compact('nicho', 'fallecido'));
        return $pdf->stream('Certificado_Exhumacion.pdf');
    }
}