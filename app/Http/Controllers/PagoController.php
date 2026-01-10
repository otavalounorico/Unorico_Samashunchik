<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Pago;
use App\Models\Recibo; // <--- Importante
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    // --- 1. HISTORIAL GENERAL (Ahora muestra RECIBOS agrupados) ---
    public function general(Request $request)
    {
        $search = trim($request->get('search', ''));

        // Consultamos RECIBOS, no pagos sueltos
        $query = Recibo::with(['socio', 'pagos'])->orderBy('created_at', 'desc');

        if ($search !== '') {
            $query->whereHas('socio', function ($q) use ($search) {
                $q->whereRaw("CAST(cedula AS TEXT) ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CONCAT(apellidos, ' ', nombres) ILIKE ?", ["%{$search}%"]);
            });
        }

        $recibos = $query->paginate(10)->withQueryString();
        $totalRecaudado = Recibo::sum('total');

        // Retornamos la vista 'general' (la actualizaremos en el paso 4)
        return view('pagos.general', compact('recibos', 'totalRecaudado'));
    }

    // --- 2. MOSTRAR MODAL DE COBRO (Igual que antes) ---
    public function index(Socio $socio)
    {
        $socio->load('pagos');
        $aniosPendientes = $socio->anios_deuda;
        return view('pagos.index-modal', compact('socio', 'aniosPendientes'));
    }

    // --- 3. GUARDAR PAGO (Crea 1 Recibo + Varios Pagos) ---
    public function store(Request $request, Socio $socio)
    {
        $request->validate([
            'anios_pagados' => 'required|array|min:1',
            'fecha_pago' => 'required|date',
        ]);

        $precio = 25.00; // Tu precio base

        try {
            DB::transaction(function () use ($request, $socio, $precio) {

                // 1. BUSCAR SI YA EXISTE UN RECIBO DE ESTE SOCIO EN ESTA FECHA
                $recibo = Recibo::where('socio_id', $socio->id)
                    ->where('fecha_pago', $request->fecha_pago) // Misma fecha
                    ->first();

                // 2. SI NO EXISTE, LO CREAMOS (Lógica anterior)
                if (!$recibo) {
                    $recibo = Recibo::create([
                        'socio_id' => $socio->id,
                        'fecha_pago' => $request->fecha_pago,
                        'total' => 0, // Se actualizará abajo
                        'observacion' => $request->observacion,
                        'created_by' => auth()->id(),
                    ]);
                } else {
                    // SI YA EXISTE, SOLO ACTUALIZAMOS LA OBSERVACIÓN (OPCIONAL)
                    if ($request->observacion) {
                        $recibo->update([
                            'observacion' => $recibo->observacion . ' | ' . $request->observacion
                        ]);
                    }
                }

                // 3. AGREGAMOS LOS NUEVOS AÑOS A ESE RECIBO (Sea nuevo o viejo)
                $nuevosPagosCount = 0;

                foreach ($request->anios_pagados as $anio) {

                    // Verificamos que no estemos duplicando un año que ya tenía
                    $existePago = Pago::where('socio_id', $socio->id)
                        ->where('anio_pagado', $anio)
                        ->exists();

                    if (!$existePago) {
                        Pago::create([
                            'recibo_id' => $recibo->id, // Usamos el ID del recibo encontrado/creado
                            'socio_id' => $socio->id,
                            'anio_pagado' => $anio,
                            'monto' => $precio,
                            'fecha_pago' => $request->fecha_pago,
                        ]);
                        $nuevosPagosCount++;
                    }
                }

                // 4. RECALCULAR EL TOTAL DEL RECIBO
                // Sumamos lo que ya tenía + lo nuevo
                $recibo->increment('total', $nuevosPagosCount * $precio);
            });

            return back()->with('success', 'Pago registrado/actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // --- 4. VER RECIBO (Solo lectura) ---
    public function show(Recibo $recibo)
    {
        $recibo->load(['socio', 'pagos']);
        return view('pagos.show', compact('recibo'));
    }

    // --- 5. EDITAR RECIBO (Corregir años) ---
    public function edit(Recibo $recibo)
    {
        $recibo->load('pagos');
        $socio = $recibo->socio;

        // Calcular años disponibles para corregir
        $aniosPagadosPorOtros = Pago::where('socio_id', $socio->id)
            ->where('recibo_id', '!=', $recibo->id)
            ->pluck('anio_pagado')->toArray();

        $anioInicio = $socio->fecha_inscripcion ? $socio->fecha_inscripcion->year : now()->year;
        $todosAnios = range($anioInicio, now()->year + 1);

        $aniosDisponibles = array_diff($todosAnios, $aniosPagadosPorOtros);
        $aniosMarcados = $recibo->pagos->pluck('anio_pagado')->toArray();

        return view('pagos.edit', compact('recibo', 'aniosDisponibles', 'aniosMarcados'));
    }

    // --- 6. ACTUALIZAR RECIBO ---
    public function update(Request $request, Recibo $recibo)
    {
        $request->validate(['anios_pagados' => 'required|array|min:1']);
        $precio = 25.00;

        try {
            DB::transaction(function () use ($request, $recibo, $precio) {
                // Borrar pagos viejos de este recibo y crear los nuevos seleccionados
                $recibo->pagos()->delete();

                foreach ($request->anios_pagados as $anio) {
                    Pago::create([
                        'recibo_id' => $recibo->id,
                        'socio_id' => $recibo->socio_id,
                        'anio_pagado' => $anio,
                        'monto' => $precio,
                        'fecha_pago' => $recibo->fecha_pago,
                    ]);
                }
                // Actualizar total
                $recibo->update([
                    'total' => count($request->anios_pagados) * $precio,
                    'observacion' => $request->observacion
                ]);
            });
            return back()->with('success', 'Recibo actualizado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // --- 7. ELIMINAR RECIBO ---
    public function destroy(Recibo $recibo)
    {
        $recibo->delete();
        return back()->with('success', 'Recibo eliminado.');
    }

    // --- 8. BUSCADOR DE SOCIO (El que ya tenías) ---
    public function create(Request $request)
    {
        // ... (Copia aquí tu función create del paso anterior tal cual estaba) ...
        // Si no la tienes a mano, avísame y te la pongo.
        // Es la que busca al socio para iniciar el cobro.
        $search = trim($request->get('search', ''));
        $socios = Socio::query()->orderBy('apellidos')->orderBy('nombres');
        if ($search !== '') {
            $socios->where(function ($w) use ($search) {
                $w->whereRaw("CAST(cedula AS TEXT) ILIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CONCAT(apellidos, ' ', nombres) ILIKE ?", ["%{$search}%"]);
            });
        }
        $resultados = $socios->paginate(5)->withQueryString();
        if ($request->ajax())
            return view('pagos.partials.lista-socios', compact('resultados'))->render();
        return view('pagos.create', compact('resultados'));
    }
    // --- 9. NUEVO: HISTORIAL INDIVIDUAL DEL SOCIO (El "Ojo") ---
// En PagoController.php

    public function historialSocio(Socio $socio)
    {
        $recibos = Recibo::with('pagos')
            ->where('socio_id', $socio->id)
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $aniosPendientes = $socio->anios_deuda;
        $totalHistorico = Recibo::where('socio_id', $socio->id)->sum('total');

        // Este es el archivo NUEVO que creamos con el diseño bonito de solo lectura
        return view('pagos.partials.modal-kardex', compact('socio', 'recibos', 'aniosPendientes', 'totalHistorico'));
    }
}