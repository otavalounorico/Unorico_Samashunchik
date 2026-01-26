<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Nicho;
use App\Models\Bloque;
use App\Models\Socio;

// Librerías Reportes
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NichosExport;
use Illuminate\Support\Facades\Http;

class NichoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $bloqueId = $request->get('bloque_id');

        // Ordenar por código (N0001, N0002...)
        $query = Nicho::with('bloque')->orderBy('codigo', 'asc');

        if ($q !== '') {
            $query->where('codigo', 'ILIKE', "%{$q}%");
        }
        if ($bloqueId) {
            $query->where('bloque_id', $bloqueId);
        }

        $nichos = $query->paginate(10)->withQueryString();
        $bloques = Bloque::orderBy('nombre')->get();

        return view('nichos.nicho-index', compact('nichos', 'bloques', 'bloqueId', 'q'));
    }

public function create()
    {
        // 1. Traemos los bloques
        $bloques = Bloque::orderBy('nombre')->get();
        
        // 2. TRAEMOS LOS SOCIOS (Ahora sí, conectando a la BD)
        // Usamos orden por 'id' descendente (los más nuevos primero).
        // Esto es seguro y no fallará por nombres de columnas.
        $socios = Socio::orderBy('id', 'desc')->get(); 

        return view('nichos.nicho-create', compact('bloques', 'socios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'socio_id' => 'nullable|exists:socios,id', // Validar Socio
            'tipo_nicho' => 'required|in:PROPIO,COMPARTIDO',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:disponible,ocupado,mantenimiento',
            'descripcion' => 'nullable|string|max:1000',
            'qr_uuid' => 'nullable|string|unique:nichos,qr_uuid',
        ]);

        try {
            Nicho::create([
                'bloque_id' => $request->bloque_id,
                'socio_id' => $request->socio_id, // Guardar Socio
                'tipo_nicho' => $request->tipo_nicho,
                'capacidad' => $request->capacidad,
                'estado' => $request->estado,
                'descripcion' => $request->descripcion,
                'disponible' => $request->estado === 'disponible',
                'qr_uuid' => $request->qr_uuid,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('nichos.index')->with('success', 'Nicho creado correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Nicho $nicho)
    {
        $nicho->load('bloque');
        return view('nichos.nicho-show', compact('nicho'));
    }

    public function edit(Nicho $nicho)
    {
        $bloques = Bloque::orderBy('nombre')->get();
        $socios = Socio::orderBy('apellidos')->get(); // Cargamos socios
        return view('nichos.nicho-edit', compact('nicho', 'bloques', 'socios'));
    }

    public function update(Request $request, Nicho $nicho)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'socio_id' => 'nullable|exists:socios,id', // Validar Socio
            'codigo' => ['required', 'string', 'max:50', Rule::unique('nichos')->ignore($nicho->id)],
            'tipo_nicho' => 'required|in:PROPIO,COMPARTIDO',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:disponible,ocupado,mantenimiento',
            'descripcion' => 'nullable|string|max:1000',
            'qr_uuid' => ['nullable', 'string', Rule::unique('nichos')->ignore($nicho->id)],
        ]);

        try {
            $nicho->update([
                'bloque_id' => $request->bloque_id,
                'socio_id' => $request->socio_id, // Actualizar Socio
                'codigo' => $request->codigo,
                'tipo_nicho' => $request->tipo_nicho,
                'capacidad' => $request->capacidad,
                'estado' => $request->estado,
                'descripcion' => $request->descripcion,
                'disponible' => $request->estado === 'disponible',
                'qr_uuid' => $request->qr_uuid,
            ]);

            return redirect()->route('nichos.index')->with('success', 'Nicho actualizado.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function destroy(Nicho $nicho)
    {
        try {
            $nicho->delete();
            return redirect()->route('nichos.index')->with('success', 'Nicho eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('nichos.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ── REPORTES PDF Y EXCEL ───────────────────────────────────────
    public function reports(Request $request)
    {
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un registro.');
        }

        // Consulta optimizada sin relaciones extrañas
        $nichos = Nicho::with('bloque')
            ->whereIn('id', $ids)
            ->orderBy('codigo', 'asc')
            ->get();

        // Encabezados
        $headings = [
            'ID',
            'Código',
            'Bloque',
            'Estado',
            'Disponibilidad',
            'Capacidad'
        ];

        // Mapeo de datos
        $data = $nichos->map(function ($n) {
            return [
                'id' => $n->id,
                'codigo' => $n->codigo,
                'bloque' => $n->bloque->nombre ?? 'N/A',
                'estado' => ucfirst($n->estado),
                'disponibilidad' => $n->disponible ? 'Sí' : 'No',
                'capacidad' => $n->capacidad,
            ];
        });

        if ($reportType === 'excel') {
            return Excel::download(new NichosExport($data, $headings), 'nichos_reporte.xlsx');

        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('nichos.reports-pdf', compact('data', 'headings'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('nichos_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back();
    }
    // Agrega esto en tu controlador

    // ─── 1. VISTA DE PREVISUALIZACIÓN (Ticket) ───
    public function downloadQr(Request $request, Nicho $nicho)
    {
        $mode = $request->get('mode', 'text');

        // Cargamos relaciones
        $nicho->load(['bloque', 'fallecidos', 'socios']);

        $textoQR = "";
        $titulo = "";

        if ($mode === 'url') {
            // ---------------------------------------------------------
            // OPCIÓN FUTURA (ONLINE) - MANTENER COMENTADO
            // ---------------------------------------------------------
            // Esta línea se descomentará cuando tengas la ruta pública lista:
            // $textoQR = route('public.nicho.info', ['uuid' => $nicho->qr_uuid]);

            // POR AHORA: Usamos un link genérico para que no de error
            $textoQR = url('/ver-nicho/' . $nicho->qr_uuid);

            $titulo = "QR WEB (FUTURO - EN CONSTRUCCIÓN)";

        } else {
            // ---------------------------------------------------------
            // OPCIÓN PRESENTE (OFFLINE / TEXTO)
            // ---------------------------------------------------------
            $textoQR = "NICHO: " . $nicho->codigo . "\n";
            $textoQR .= "BLOQUE: " . ($nicho->bloque->nombre ?? 'S/N') . "\n";

            if ($nicho->fallecidos->isNotEmpty()) {
                $textoQR .= "\n--- OCUPANTES ---\n";
                foreach ($nicho->fallecidos as $f) {
                    $textoQR .= "- " . $f->apellidos . " " . $f->nombres . "\n";
                }
            } else {
                $textoQR .= "\nESTADO: " . ucfirst($nicho->estado);
            }

            if ($nicho->socios->isNotEmpty()) {
                $responsable = $nicho->socios->first();
                $textoQR .= "\n--- RESPONSABLE ---\n";
                $textoQR .= $responsable->apellidos . " " . $responsable->nombres;
            }

            $titulo = "QR DE DATOS (OFFLINE)";
        }

        // Generamos la URL de la imagen para la vista
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($textoQR);

        // Retornamos la vista de previsualización
        return view('nichos.nicho-qr-print', compact('nicho', 'qrUrl', 'textoQR', 'titulo', 'mode'));
    }

    // ─── 2. DESCARGA DIRECTA DE IMAGEN PNG (Acción del botón verde) ───
    public function downloadQrImage(Nicho $nicho)
    {
        // 1. Reconstruimos el texto (Usamos la lógica OFFLINE por defecto para la descarga directa)
        $nicho->load(['bloque', 'fallecidos', 'socios']);

        $texto = "NICHO: " . $nicho->codigo . "\n";
        $texto .= "BLOQUE: " . ($nicho->bloque->nombre ?? 'S/N') . "\n";

        if ($nicho->fallecidos->isNotEmpty()) {
            $texto .= "\n--- OCUPANTES ---\n";
            foreach ($nicho->fallecidos as $f) {
                $texto .= $f->apellidos . " " . $f->nombres . "\n";
            }
        } else {
            $texto .= "\nESTADO: " . ucfirst($nicho->estado);
        }

        if ($nicho->socios->isNotEmpty()) {
            $r = $nicho->socios->first();
            $texto .= "\n--- RESPONSABLE ---\n";
            $texto .= $r->apellidos . " " . $r->nombres;
        }

        // 2. Pedimos la imagen a la API (Tamaño 500x500 para mejor calidad)
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&margin=10&data=" . urlencode($texto);

        // 3. Obtenemos el contenido del archivo
        $imageContent = Http::get($apiUrl)->body();

        // 4. Forzamos la descarga del archivo .png
        $filename = 'QR_' . $nicho->codigo . '.png';

        return response($imageContent)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    // Muestra la información al escanear el QR (Vista para celular)
    public function publicShow($uuid)
    {
        $nicho = Nicho::with('bloque')->where('qr_uuid', $uuid)->firstOrFail();
        // Retorna una vista simple pública
        return view('nichos.public-info', compact('nicho'));
    }
}