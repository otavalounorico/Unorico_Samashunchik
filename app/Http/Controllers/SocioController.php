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

class SocioController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $comunidadId = $request->get('comunidad_id');

        $query = Socio::with(['comunidad.parroquia.canton', 'genero', 'estadoCivil'])
            ->orderBy('apellidos')->orderBy('nombres');

        if ($comunidadId)
            $query->where('comunidad_id', $comunidadId);

        if ($search !== '') {
            $query->where(function ($w) use ($search) {
                $w->where('cedula', 'ILIKE', "%{$search}%")
                    ->orWhere('nombres', 'ILIKE', "%{$search}%")
                    ->orWhere('apellidos', 'ILIKE', "%{$search}%")
                    ->orWhere('codigo', 'ILIKE', "%{$search}%");
            });
        }

        $socios = $query->paginate(10)->withQueryString();
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);

        // Alerta de candidatos a exoneración
        $fechaLimite = \Carbon\Carbon::now()->subYears(75);
        $candidatos = Socio::where('fecha_nac', '<=', $fechaLimite)
            ->where('tipo_beneficio', '!=', 'exonerado')
            ->orderBy('apellidos')
            ->get();

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
        // 1. Validaciones
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
            'es_representante'  => 'boolean',
            'condicion'         => 'required|in:ninguna,discapacidad,enfermedad_terminal',
            'estatus'           => 'required|in:vivo,fallecido',
        ]);

        try {
            // 2. Lógica de Negocio (Edad para exoneración)
            $edad = Carbon::parse($request->fecha_nac)->age;

            if ($request->tipo_beneficio === 'exonerado') {
                if ($edad < 75) {
                    return back()->withInput()->with('error', 
                        "No se puede crear como Exonerado. El socio tiene $edad años (Mínimo 75).");
                }
            } else {
                $request->merge(['fecha_exoneracion' => null]);
            }

            // 3. Preparar datos
            $data = $request->except(['_token', '_method']);
            $data['created_by'] = auth()->id();
            $data['es_representante'] = $request->has('es_representante') ? 1 : 0;

            // 4. Crear
            $nuevoSocio = Socio::create($data);

            // MENSAJE SOLICITADO: CODIGO Y NOMBRE
            return redirect()->route('socios.index')
                ->with('success', "Socio creado correctamente: [{$nuevoSocio->codigo}] {$nuevoSocio->apellidos} {$nuevoSocio->nombres}");

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear socio: ' . $e->getMessage());
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
        $request->validate([
            'cedula'            => 'required|string|max:20|unique:socios,cedula,' . $socio->id,
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
            'es_representante'  => 'boolean',
            'condicion'         => 'required|in:ninguna,discapacidad,enfermedad_terminal',
            'estatus'           => 'required|in:vivo,fallecido',
        ]);

        try {
            $edad = Carbon::parse($request->fecha_nac)->age;

            if ($request->tipo_beneficio === 'exonerado') {
                if ($edad < 75) {
                    return back()->withInput()->with('error',
                        "Acción denegada: El socio tiene $edad años. Solo mayores de 75 pueden ser Exonerados.");
                }
            } else {
                $request->merge(['fecha_exoneracion' => null]);
            }

            $data = $request->all();

            if (!$request->has('es_representante')) {
                $data['es_representante'] = 0;
            }

            $socio->update($data);

            // MENSAJE SOLICITADO: CODIGO Y NOMBRE
            return redirect()->route('socios.index')
                ->with('success', "Socio actualizado correctamente: [{$socio->codigo}] {$socio->apellidos} {$socio->nombres}");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
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
        $socio->load(['comunidad.parroquia.canton', 'genero', 'estadoCivil']);
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
            
            $canton    = $s->comunidad?->parroquia?->canton?->nombre ?? 'Sin Cantón';
            $parroquia = $s->comunidad?->parroquia?->nombre ?? 'Sin Parr.';
            $comunidad = $s->comunidad?->nombre ?? 'Sin Com.';
            $ubicacion = "$canton / $parroquia / $comunidad";

            $condicion = ucfirst(str_replace('_', ' ', $s->condicion));
            
            $beneficio = match($s->tipo_beneficio) {
                'sin_subsidio' => 'Sin Subsidio',
                'con_subsidio' => 'Con Subsidio',
                'exonerado'    => 'Exonerado',
                default        => $s->tipo_beneficio,
            };

            return [
                'id'        => $s->id,
                'codigo'    => $s->codigo,
                'cedula'    => $s->cedula,
                'nombres'   => $s->apellidos . ' ' . $s->nombres,
                'fecha_nac' => $s->fecha_nac ? $s->fecha_nac->format('d/m/Y') : '',
                'ubicacion' => $ubicacion,
                'direccion' => $s->direccion ?? '—',
                'edad'      => $s->edad,
                'beneficio' => $beneficio,
                'telefono'  => $s->telefono,
                'condicion' => $condicion,
                'estatus'   => ucfirst($s->estatus),
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