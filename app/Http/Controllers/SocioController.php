<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Comunidad;
use App\Models\Genero;
use App\Models\EstadoCivil;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SociosExport;
use Carbon\Carbon;
use App\Models\Nicho;

class SocioController extends Controller
{
    /**
     * Muestra la lista de socios con sus contadores de nichos.
     */
    public function index(Request $request)
    {
        // 1. Cargar comunidades para el filtro (dropdown)
        $comunidades = Comunidad::orderBy('nombre', 'asc')->get();

        // 2. Lógica para la alerta de "Candidatos a Exoneración" (Mayores de 75 años no exonerados)
        // Nota: Filtramos en colección para aprovechar el accessor 'edad' del modelo, 
        // pero idealmente esto se debería hacer con whereRaw en SQL para optimizar si son muchos datos.
        $candidatos = Socio::where('tipo_beneficio', '!=', 'exonerado')
            ->whereNotNull('fecha_nac')
            ->get()
            ->filter(function ($s) {
                return $s->edad >= 75;
            });

        // 3. Consulta Principal de Socios
        $socios = Socio::query()
            ->with(['comunidad']) // Carga la relación de comunidad para no hacer N+1 queries

            // --- AQUÍ ESTÁ LA LÓGICA DE LOS NICHOS ---
            ->withCount([
                // Cuenta TOTAL de nichos asignados a este ID de socio
                'nichos as total_nichos',

                // Cuenta SOLO los que tienen tipo_nicho = 'PROPIO'
                'nichos as propios_count' => function ($query) {
                    $query->where('tipo_nicho', 'PROPIO');
                },

                // Cuenta SOLO los que tienen tipo_nicho = 'COMPARTIDO'
                'nichos as compartidos_count' => function ($query) {
                    $query->where('tipo_nicho', 'COMPARTIDO');
                }
            ])
            // -----------------------------------------

            // Aplicar Scope de Búsqueda (definido en tu modelo Socio)
            ->buscar($request->search)

            // Aplicar Filtro de Comunidad si se seleccionó una
            ->when($request->comunidad_id, function ($query, $id) {
                return $query->where('comunidad_id', $id);
            })

            // Ordenamiento por defecto
            ->orderBy('apellidos', 'asc')
            ->orderBy('nombres', 'asc')

            // Paginación (Mantiene los filtros en la URL al cambiar de página)
            ->paginate(10)
            ->withQueryString();

        // 4. Retornar la vista con todas las variables necesarias
        return view('socios.socio-index', compact('socios', 'comunidades', 'candidatos'));
    }

    public function create()
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);
        $generos = Genero::orderBy('nombre')->get(['id', 'nombre']);
        $estados = EstadoCivil::orderBy('nombre')->get(['id', 'nombre']);

        return view('socios.socio-create', compact('comunidades', 'generos', 'estados'));
    }
public function store(Request $request)
    {
        $request->validate([
            'cedula'            => 'required|string|max:20|unique:socios,cedula',
            'nombres'           => 'required|string|max:255',
            'apellidos'         => 'required|string|max:255',
            'fecha_nac'         => 'required|date',
            'telefono'          => 'nullable|string|max:30',
            'direccion'         => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'comunidad_id'      => 'required|exists:comunidades,id',
            'estado_civil_id'   => 'required|exists:estados_civiles,id',
            'genero_id'         => 'nullable|exists:generos,id',
            'fecha_inscripcion' => 'required|date',
            'tipo_beneficio'    => 'required|in:sin_subsidio,con_subsidio,exonerado',
            'fecha_exoneracion' => 'nullable|date|required_if:tipo_beneficio,exonerado',
            'condicion'         => 'required|in:ninguna,discapacidad,enfermedad_terminal',
            'estatus'           => 'required|in:vivo,fallecido',
        ]);

        try {
            $edad = Carbon::parse($request->fecha_nac)->age;
            if ($request->tipo_beneficio === 'exonerado' && $edad < 75) {
                return back()->withInput()->with('error', "No se puede crear como Exonerado. Edad: $edad (Mínimo 75).");
            }

            // LIMPIEZA TOTAL: Quitamos el campo del request. 
            // El modelo pondrá 'f' automáticamente.
            $data = $request->except(['_token', '_method', 'es_representante']);
            
            $data['created_by'] = auth()->id();

            $nuevoSocio = Socio::create($data);

            return redirect()->route('socios.index')
                ->with('success', "Socio creado correctamente: [{$nuevoSocio->codigo}] {$nuevoSocio->nombre_completo}");

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Error al crear socio: ' . $e->getMessage());
        }
    }
    public function edit(Socio $socio)
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);
        $generos = Genero::orderBy('nombre')->get(['id', 'nombre']);
        $estados = EstadoCivil::orderBy('nombre')->get(['id', 'nombre']);

        return view('socios.socio-edit', compact('socio', 'comunidades', 'generos', 'estados'));
    }

public function update(Request $request, Socio $socio)
    {
        // Validaciones idénticas al store (ignorando unique cedula propia)
        $request->validate([
            'cedula' => 'required|string|max:20|unique:socios,cedula,' . $socio->id,
            // ... resto de validaciones ...
            'nombres' => 'required', 'apellidos' => 'required', 'fecha_nac' => 'required', 'comunidad_id' => 'required', 'estado_civil_id' => 'required', 'fecha_inscripcion' => 'required', 'tipo_beneficio' => 'required', 'condicion' => 'required', 'estatus' => 'required',
        ]);

        try {
            $edad = Carbon::parse($request->fecha_nac)->age;
            if ($request->tipo_beneficio === 'exonerado' && $edad < 75) {
                return back()->withInput()->with('error', "Acción denegada: Edad $edad (Mínimo 75).");
            }

            // También aquí lo quitamos para no sobreescribir con basura
            $data = $request->except(['_token', '_method', 'es_representante']);

            $socio->update($data);

            return redirect()->route('socios.index')
                ->with('success', "Socio actualizado correctamente: [{$socio->codigo}] {$socio->nombre_completo}");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    public function destroy(Socio $socio)
    {
        try {
            // Guardamos info antes de borrar
            $infoSocio = "[{$socio->codigo}] {$socio->apellidos} {$socio->nombres}";

            $socio->delete();

            // MENSAJE SOLICITADO: CODIGO Y NOMBRE
            return redirect()->route('socios.index')
                ->with('success', "Socio eliminado correctamente: {$infoSocio}");

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Socio $socio)
    {
        // Cargamos los nichos Y su bloque (ubicación) para mostrarlos en la tabla del modal.
        // Esto evita que por cada nicho haga una consulta extra para buscar el bloque.
        $socio->load(['nichos.bloque']);

        return view('socios.socio-show', compact('socio'));
    }
    public function reports(Request $request)
    {
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un socio para generar el reporte.');
        }

        $socios = Socio::with(['comunidad.parroquia.canton', 'genero', 'estadoCivil'])
            ->whereIn('id', $ids)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        // 1. Encabezados (Incluye Dirección, Condición, Estatus)
        $headings = [
            'ID',
            'Código',
            'Cédula',
            'Apellidos y Nombres',
            'Fecha Nac.',
            'Ubicación',
            'Dirección', // Campo solicitado
            'Edad',
            'Beneficio',
            'Teléfono',
            'Condición', // Campo nuevo
            'Estatus'    // Campo nuevo
        ];

        // 2. Mapeo de datos
        $data = $socios->map(function ($s) {

            $canton = $s->comunidad?->parroquia?->canton?->nombre ?? 'Sin Cantón';
            $parroquia = $s->comunidad?->parroquia?->nombre ?? 'Sin Parr.';
            $comunidad = $s->comunidad?->nombre ?? 'Sin Com.';
            $ubicacion = "$canton / $parroquia / $comunidad";

            $condicion = ucfirst(str_replace('_', ' ', $s->condicion));

            $beneficio = match ($s->tipo_beneficio) {
                'sin_subsidio' => 'Sin Subsidio',
                'con_subsidio' => 'Con Subsidio',
                'exonerado' => 'Exonerado',
                default => $s->tipo_beneficio,
            };

            return [
                'id' => $s->id,
                'codigo' => $s->codigo,
                'cedula' => $s->cedula,
                'nombres' => $s->apellidos . ' ' . $s->nombres,
                'fecha_nac' => $s->fecha_nac ? $s->fecha_nac->format('d/m/Y') : '',
                'ubicacion' => $ubicacion,
                'direccion' => $s->direccion ?? '—',
                'edad' => $s->edad,
                'beneficio' => $beneficio,
                'telefono' => $s->telefono,
                'condicion' => $condicion,
                'estatus' => ucfirst($s->estatus),
            ];
        });

        if ($reportType === 'excel') {
            return Excel::download(new SociosExport($data, $headings), 'socios_reporte_' . date('YmdHis') . '.xlsx');

        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('socios.reports-pdf', compact('data', 'headings'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('socios_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}