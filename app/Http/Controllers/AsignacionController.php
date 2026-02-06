<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Nicho;
use App\Models\Socio;
use App\Models\Fallecido;
use Barryvdh\DomPDF\Facade\Pdf;
// Asegúrate de importar el PDF

class AsignacionController extends Controller
{
    // --- 1. INDEX: LISTADO DE NICHOS ---
    public function index(Request $request)
    {
        // 1. Base de la consulta
        $query = Nicho::with(['bloque', 'socios', 'fallecidos'])
            ->orderBy('updated_at', 'desc');

        // =========================================================
        // LÓGICA DE FILTROS (Aquí estaba el conflicto)
        // =========================================================

        // A. ¿El usuario está usando el buscador?
        $buscando = $request->filled('search');
        // B. ¿El usuario seleccionó un estado?
        $filtrandoEstado = $request->filled('estado');

        // CASO 1: Si el usuario está FILTRANDO por Estado
        if ($filtrandoEstado) {
            $estado = strtoupper(trim($request->estado)); // Forzamos mayúsculas y quitamos espacios

            if ($estado === 'MANTENIMIENTO') {
                // Truco: Usamos whereRaw para ignorar mayúsculas/minúsculas en la base de datos
                $query->whereRaw('UPPER(estado) = ?', ['MANTENIMIENTO']);
            } else {
                // Si busca OCUPADO, buscamos por estado Y nos aseguramos que tenga gente (opcional)
                $query->where('estado', $request->estado);
            }
        }
        // CASO 2: Si NO filtra por estado, pero tampoco busca nada (VISTA INICIAL)
        else if (!$buscando) {
            // Regla por defecto: Mostrar (Con Gente) O (En Mantenimiento)
            $query->where(function ($q) {
                $q->has('socios')
                    ->orWhereHas('fallecidos', function ($qf) {
                        $qf->where('fallecido_nicho.fecha_exhumacion', null);
                    })
                    ->orWhereRaw('UPPER(estado) = ?', ['MANTENIMIENTO']);
            });
        }
        // CASO 3: Si solo está buscando texto (Search), buscamos en todo lado sin restringir.

        // =========================================================
        // BUSCADOR DE TEXTO
        // =========================================================
        if ($buscando) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                // Buscamos código de nicho (insensible a mayúsculas)
                $q->where('codigo', 'ILIKE', "%{$term}%")
                    // O socio
                    ->orWhereHas('socios', function ($qs) use ($term) {
                        $qs->where('cedula', 'ILIKE', "%{$term}%")
                            ->orWhere('apellidos', 'ILIKE', "%{$term}%")
                            ->orWhere('nombres', 'ILIKE', "%{$term}%");
                    })
                    // O fallecido activo
                    ->orWhereHas('fallecidos', function ($qf) use ($term) {
                        $qf->where('fallecido_nicho.fecha_exhumacion', null)
                            ->where(function ($sub) use ($term) {
                                $sub->where('cedula', 'ILIKE', "%{$term}%")
                                    ->orWhere('apellidos', 'ILIKE', "%{$term}%")
                                    ->orWhere('nombres', 'ILIKE', "%{$term}%");
                            });
                    });
            });
        }

        $nichos = $query->paginate(10);
        return view('asignaciones.asignacion-index', compact('nichos')); // Asegúrate que tu vista se llame así
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
            ->orWhereHas('nichos', function ($q) use ($id) {
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

                // 2. Asignar Socio al Nicho (Relación general del espacio)
                $nicho->socios()->syncWithoutDetaching([
                    $request->socio_id => [
                        'rol' => $request->rol,
                        'desde' => now()
                    ]
                ]);

                // 3. Generar Código Correlativo
                $ultimoId = DB::table('fallecido_nicho')->max('id') ?? 0;
                $siguienteId = $ultimoId + 1;
                $codigoGenerado = 'ASG-' . str_pad($siguienteId, 2, '0', STR_PAD_LEFT);

                // 4. Asignar Fallecido con el SOCIO_ID vinculado al registro
                $nicho->fallecidos()->attach($request->fallecido_id, [
                    'socio_id' => $request->socio_id, // <--- CAMBIO AQUÍ
                    'codigo' => $codigoGenerado,
                    'posicion' => $ocupantesActivos + 1,
                    'fecha_inhumacion' => now(),
                    'observacion' => 'Ingreso registrado'
                ]);

                // 5. Actualizar Ocupación del Nicho
                $totalAhora = $ocupantesActivos + 1;
                $nicho->update([
                    'ocupacion' => $totalAhora,
                    'disponible' => ($totalAhora < $nicho->capacidad)
                ]);
            });

            return back()->with('success', 'Asignación correcta. El registro se vinculó al socio.');

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

            // Liberar espacio (decrementamos ocupación)
            $ocupantesActivos = $nicho->fallecidos()
                ->wherePivot('fecha_exhumacion', null)
                ->count();
            $nicho->update([
                'ocupacion' => $ocupantesActivos,
                'disponible' => ($ocupantesActivos < $nicho->capacidad)
            ]);
        });

        return back()->with('success', 'Exhumación registrada correctamente.');
    }

    public function update(Request $request, $nicho_id)
    {
        $request->validate([
            'socio_id' => 'required|exists:socios,id',
            'socio_anterior_id' => 'required|exists:socios,id',
            'fallecido_id' => 'required|exists:fallecidos,id',
            'fallecido_anterior_id' => 'required|exists:fallecidos,id',
            'fecha_inhumacion' => 'required|date',
            'rol' => 'required|string'
        ]);

        try {
            DB::transaction(function () use ($request, $nicho_id) {
                $nicho = Nicho::findOrFail($nicho_id);

                // A. Actualizar Socio en la tabla general de nichos
                if ($request->socio_id != $request->socio_anterior_id) {
                    
                    // --- CORRECCIÓN ---
                    // En lugar de detach() directo, buscamos el modelo Pivot y lo borramos.
                    // Esto carga el ID en memoria para que la auditoría funcione.
                    $pivotSocio = \App\Models\SocioNicho::where('nicho_id', $nicho_id)
                                    ->where('socio_id', $request->socio_anterior_id)
                                    ->first();
                    
                    if ($pivotSocio) {
                        $pivotSocio->delete();
                    }

                    // Ahora asignamos el nuevo socio
                    $nicho->socios()->syncWithoutDetaching([
                        $request->socio_id => ['rol' => $request->rol, 'desde' => now()]
                    ]);
                }

                // B. Actualizar registro en fallecido_nicho incluyendo el nuevo socio_id
                $nicho->fallecidos()->updateExistingPivot($request->fallecido_id, [
                    'socio_id' => $request->socio_id, // <--- Mantiene sincronía
                    'fecha_inhumacion' => $request->fecha_inhumacion,
                    'observacion' => $request->observacion,
                ]);
            });

            return back()->with('success', 'Datos corregidos y socio sincronizado.');

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

                // 1. Sacar al fallecido (la tabla pivote se limpia)
                $nicho->fallecidos()->detach($fallecido_id);

                // 2. Verificar si quedan más fallecidos del mismo socio
                // Si el nicho queda vacío, podrías evaluar si quitar al socio también
                $ocupantesRestantes = $nicho->fallecidos()
                    ->wherePivot('fecha_exhumacion', null)
                    ->count();

                $nicho->update([
                    'ocupacion' => $ocupantesRestantes,
                    'disponible' => ($ocupantesRestantes < $nicho->capacidad)
                ]);
            });

            return back()->with('success', 'Registro eliminado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // REPORTES PDF (AGREGADOS)
    // =========================================================================

    // 1. Reporte General (Todo lo ocupado)
    public function pdfReporteGeneral(Request $request)
    {
        // 1. Iniciamos la consulta
        $query = Nicho::query();

        // 2. FILTRO OBLIGATORIO: Solo nichos con fallecidos ACTIVOS (no exhumados)
        // Usamos el nombre explícito de la tabla 'fallecido_nicho' para evitar errores
        $query->whereHas('fallecidos', function ($q) {
            $q->where('fallecido_nicho.fecha_exhumacion', null);
        });

        // 3. Filtro de Estado (Si se seleccionó uno)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 4. BÚSQUEDA INTELIGENTE (Aquí está el cambio clave)
        if ($request->filled('search')) {
            $search = trim($request->search); // Quitamos espacios accidentales

            $query->where(function ($q) use ($search) {
                // Usamos ILIKE para PostgreSQL (insensible a mayúsculas)
                $q->where('codigo', 'ILIKE', "%{$search}%")
                    ->orWhereHas('socios', function ($sq) use ($search) {
                        $sq->where('nombres', 'ILIKE', "%{$search}%")
                            ->orWhere('apellidos', 'ILIKE', "%{$search}%")
                            // Opcional: buscar por cédula también
                            ->orWhere('cedula', 'ILIKE', "%{$search}%");
                    })
                    ->orWhereHas('fallecidos', function ($fq) use ($search) {
                        $fq->where('nombres', 'ILIKE', "%{$search}%")
                            ->orWhere('apellidos', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // 5. Obtenemos los datos (con filtro en la relación para no traer exhumados)
        $nichos = $query->with([
            'fallecidos' => function ($q) {
                $q->where('fallecido_nicho.fecha_exhumacion', null);
            },
            'socios',
            'bloque'
        ])->get();

        // 6. Generamos el PDF
        $pdf = \PDF::loadView('asignaciones.pdf-reporte-general', compact('nichos'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('reporte_asignaciones.pdf');
    }
    // 2. Reporte de Exhumados
    public function pdfReporteExhumados()
    {
        // 1. CONSULTA ESTRICTA
        // "whereHas" asegura que SOLO traigamos nichos que tengan historial de exhumación.
        // Si un nicho está ocupado pero nunca se ha exhumado a nadie ahí, NO saldrá.
        $nichos = Nicho::whereHas('fallecidos', function ($q) {
            $q->whereNotNull('fallecido_nicho.fecha_exhumacion');
        })
            ->with([
                'fallecidos' => function ($q) {
                    // 2. FILTRO DE CARGA
                    // Aquí le decimos: "Del nicho, tráeme SOLO los datos de los que fueron exhumados".
                    // Ignora a los fallecidos actuales (activos).
                    $q->whereNotNull('fallecido_nicho.fecha_exhumacion')
                        ->orderBy('fallecido_nicho.fecha_exhumacion', 'desc');
                },
                'bloque'
            ])
            ->get();

        // 3. ENVIAR A LA VISTA
        // Usamos compact('nichos') para que la variable exista en el PDF.
        $pdf = \PDF::loadView('asignaciones.pdf-lista-exhumados', compact('nichos'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('reporte_exhumados.pdf');
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