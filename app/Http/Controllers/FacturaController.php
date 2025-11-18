<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Socio;
use App\Models\Beneficio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Servicio;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Factura::with('socio')->orderByDesc('fecha')->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('cliente_nombre', 'ILIKE', "%{$q}%")
                    ->orWhere('cliente_apellido', 'ILIKE', "%{$q}%")
                    ->orWhere('cliente_cedula', 'ILIKE', "%{$q}%")
                    ->orWhereHas('socio', function ($s) use ($q) {
                        $s->where('nombres', 'ILIKE', "%{$q}%")
                            ->orWhere('apellidos', 'ILIKE', "%{$q}%")
                            ->orWhere('cedula', 'ILIKE', "%{$q}%");
                    });
            });
        }

        $facturas = $query->paginate(10)->withQueryString();

        return view('facturas.factura-index', compact('facturas', 'q'));
    }

    public function create()
    {
        $socios = Socio::orderBy('apellidos')
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'apellidos', 'cedula', 'telefono', 'email']);

        $beneficios = Beneficio::orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();

        return view('facturas.factura-create', compact('socios', 'beneficios', 'servicios'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'socio_id' => 'nullable|exists:socios,id',
            'cliente_nombre' => 'required|string|max:255',
            'cliente_apellido' => 'nullable|string|max:255',
            'cliente_cedula' => 'nullable|string|max:20',
            'cliente_email' => 'nullable|string|max:255',
            'cliente_telefono' => 'nullable|string|max:30',
            'fecha' => 'required|date',
            // arrays de items
            'items.beneficio_id.*' => 'nullable|exists:beneficios,id',
            'items.servicio_id.*' => 'nullable|exists:servicios,id',
            'items.cantidad.*' => 'required|integer|min:1',
            'items.precio.*' => 'required|numeric|min:0',

        ]);

        // Armamos los ítems válidos (cantidad > 0)
        $items = [];
        $beneficioIds = $request->input('items.beneficio_id', []);
        $servicioIds = $request->input('items.servicio_id', []);
        $cantidades = $request->input('items.cantidad', []);
        $precios = $request->input('items.precio', []);

        $filas = max(
            count($beneficioIds),
            count($servicioIds),
            count($cantidades),
            count($precios),
        );

        for ($i = 0; $i < $filas; $i++) {
            $beneficioId = $beneficioIds[$i] ?? null;
            $servicioId = $servicioIds[$i] ?? null;
            $cant = (int) ($cantidades[$i] ?? 0);
            $precio = (float) ($precios[$i] ?? 0);

            // Ignorar filas vacías o sin cantidad
            if ($cant <= 0 || (!$beneficioId && !$servicioId)) {
                continue;
            }

            // Evitar que marquen ambos a la vez
            if ($beneficioId && $servicioId) {
                return back()
                    ->withInput()
                    ->with('error', 'En cada fila debes seleccionar solo beneficio o solo servicio, no ambos.');
            }

            $items[] = [
                'beneficio_id' => $beneficioId ?: null,
                'servicio_id' => $servicioId ?: null,
                'cantidad' => $cant,
                'precio' => $precio,
                'subtotal' => $cant * $precio,
            ];
        }

        if (count($items) === 0) {
            return back()->withInput()->with('error', 'Debes agregar al menos un ítem con cantidad.');
        }


        DB::beginTransaction();
        try {
            // Si viene socio_id pero faltan datos del cliente, los rellenamos (snapshot)
            if ($request->filled('socio_id')) {
                $socio = Socio::find($request->socio_id);
                if ($socio) {
                    $nombre = $request->cliente_nombre ?: $socio->nombres;
                    $apellido = $request->cliente_apellido ?: $socio->apellidos;
                    $cedula = $request->cliente_cedula ?: $socio->cedula;
                    $email = $request->cliente_email ?: $socio->email;
                    $tel = $request->cliente_telefono ?: $socio->telefono;
                    $request->merge([
                        'cliente_nombre' => $nombre,
                        'cliente_apellido' => $apellido,
                        'cliente_cedula' => $cedula,
                        'cliente_email' => $email,
                        'cliente_telefono' => $tel,
                    ]);
                }
            }

            $total = array_sum(array_column($items, 'subtotal'));

            $factura = Factura::create([
                'socio_id' => $request->socio_id,
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_apellido' => $request->cliente_apellido,
                'cliente_cedula' => $request->cliente_cedula,
                'cliente_email' => $request->cliente_email,
                'cliente_telefono' => $request->cliente_telefono,
                'fecha' => $request->fecha,
                'total' => $total,
                'estado' => 'PENDIENTE',
            ]);

            foreach ($items as $it) {
                $it['factura_id'] = $factura->id;
                FacturaDetalle::create($it);
            }

            DB::commit();
            return redirect()->route('facturas.show', $factura)->with('success', 'Factura creada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

public function show(Factura $factura)
{
    $factura->load(['socio','detalles.beneficio','detalles.servicio']);
    return view('facturas.factura-show', compact('factura'));
}


    // Edición simple: estado o datos del cliente (opcional)
    public function edit(Factura $factura)
    {
        return view('facturas.factura-edit', compact('factura'));
    }

    public function update(Request $request, Factura $factura)
    {
        $request->validate([
            'estado' => 'required|string|max:20',
            'cliente_nombre' => 'required|string|max:255',
            'cliente_apellido' => 'nullable|string|max:255',
            'cliente_cedula' => 'nullable|string|max:20',
            'cliente_email' => 'nullable|string|max:255',
            'cliente_telefono' => 'nullable|string|max:30',
        ]);

        $factura->update($request->only([
            'estado',
            'cliente_nombre',
            'cliente_apellido',
            'cliente_cedula',
            'cliente_email',
            'cliente_telefono'
        ]));

        return redirect()->route('facturas.show', $factura)->with('success', 'Factura actualizada.');
    }

    public function destroy(Factura $factura)
    {
        try {
            $factura->delete();
            return redirect()->route('facturas.index')->with('success', 'Factura eliminada.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }
}
