<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FallecidoNicho;

class FallecidoNichoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fallecido_id' => 'required|exists:fallecidos,id',
            'nicho_id' => 'required|exists:nichos,id',
            'socio_id' => 'required|exists:socios,id', // Requerimos el socio
        ]);

        // Generar código ASG-XXXXX igual que en el otro controlador
        $ultimoId = \App\Models\FallecidoNicho::max('id') ?? 0;
        $codigoGenerado = 'ASG-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);

        FallecidoNicho::create([
            'codigo' => $codigoGenerado,
            'fallecido_id' => $request->fallecido_id,
            'nicho_id' => $request->nicho_id,
            'socio_id' => $request->socio_id,
            'posicion' => $request->posicion ?? 1,
            'fecha_inhumacion' => $request->fecha_inhumacion ?? now(), // Cambiado de fecha_ingreso
            'fecha_exhumacion' => $request->fecha_exhumacion,           // Cambiado de fecha_salida
            'observacion' => $request->observacion,
        ]);

        return back()->with('success', 'Fallecido asignado al nicho con su socio responsable.');
    }

    public function destroy(FallecidoNicho $fallecidoNicho)
    {
        $fallecidoNicho->delete();
        return back()->with('success', 'Asignación eliminada.');
    }
}