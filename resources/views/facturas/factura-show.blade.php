<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">
          Factura N° {{ sprintf('%06d', $factura->id) }}
        </h4>

        <div class="d-flex gap-2">
          <a href="{{ route('facturas.index') }}" class="btn btn-secondary btn-sm">
            ← Volver al listado
          </a>
          <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
            🖨 Imprimir
          </button>
        </div>
      </div>

      @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
      @endif

      <div class="card shadow-sm">
        <div class="card-body">

          {{-- Encabezado empresa --}}
          <div class="row mb-4">
            <div class="col-md-7">
              <h2 class="h5 mb-1">Cementerio Municipal</h2>
              <div>Dirección del cementerio</div>
              <div>Teléfono: 000-000-000</div>
              <div>Correo: info@cementerio.test</div>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
              <div><strong>Factura N°:</strong> {{ sprintf('%06d', $factura->id) }}</div>
              <div><strong>Fecha:</strong> {{ $factura->fecha?->format('d/m/Y') }}</div>
              <div>
                <strong>Estado:</strong>
                @php
                  $estado = strtoupper($factura->estado ?? 'PENDIENTE');
                  $badgeClass = [
                      'PENDIENTE' => 'warning',
                      'EMITIDA'   => 'info',
                      'PAGADA'    => 'success',
                      'ANULADA'   => 'danger',
                  ][$estado] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $badgeClass }}">
                  {{ $estado }}
                </span>
              </div>
            </div>
          </div>

          <hr>

          {{-- Datos del cliente --}}
          <div class="row mb-4">
            <div class="col-md-6">
              <h3 class="h6">Datos del cliente</h3>
              <div><strong>Nombre:</strong>
                {{ $factura->cliente_nombre }}
                @if($factura->cliente_apellido)
                  {{ ' ' . $factura->cliente_apellido }}
                @endif
              </div>
              @if($factura->cliente_cedula)
                <div><strong>Cédula / RUC:</strong> {{ $factura->cliente_cedula }}</div>
              @endif
              @if($factura->cliente_telefono)
                <div><strong>Teléfono:</strong> {{ $factura->cliente_telefono }}</div>
              @endif
              @if($factura->cliente_email)
                <div><strong>Email:</strong> {{ $factura->cliente_email }}</div>
              @endif
            </div>

            @if($factura->socio)
              <div class="col-md-6 mt-3 mt-md-0">
                <h3 class="h6">Socio asociado</h3>
                <div><strong>Nombre:</strong> {{ $factura->socio->nombres }} {{ $factura->socio->apellidos }}</div>
                @if($factura->socio->cedula)
                  <div><strong>Cédula:</strong> {{ $factura->socio->cedula }}</div>
                @endif
                @if($factura->socio->telefono)
                  <div><strong>Teléfono:</strong> {{ $factura->socio->telefono }}</div>
                @endif
                @if($factura->socio->email)
                  <div><strong>Email:</strong> {{ $factura->socio->email }}</div>
                @endif
              </div>
            @endif
          </div>

          {{-- Detalle --}}
          <h3 class="h6 mb-2">Detalle</h3>

          <div class="table-responsive mb-3">
            <table class="table table-sm align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 40px;">#</th>
                  <th>Tipo</th>
                  <th>Descripción</th>
                  <th class="text-end" style="width: 90px;">Cantidad</th>
                  <th class="text-end" style="width: 120px;">Precio unitario</th>
                  <th class="text-end" style="width: 120px;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @php $totalCalculado = 0; @endphp
                @forelse($factura->detalles as $i => $detalle)
                  @php $totalCalculado += $detalle->subtotal; @endphp
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                      <span class="badge bg-secondary">
                        {{ $detalle->tipo_item }}
                      </span>
                    </td>
                    <td>{{ $detalle->nombre_item }}</td>
                    <td class="text-end">
                      {{ number_format($detalle->cantidad, 0) }}
                    </td>
                    <td class="text-end">
                      $ {{ number_format($detalle->precio, 2) }}
                    </td>
                    <td class="text-end">
                      $ {{ number_format($detalle->subtotal, 2) }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                      No hay ítems registrados en esta factura.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- Totales --}}
          <div class="row justify-content-end">
            <div class="col-md-5">
              <table class="table table-sm">
                <tr>
                  <th class="text-end">Total calculado (detalle):</th>
                  <td class="text-end">
                    $ {{ number_format($totalCalculado, 2) }}
                  </td>
                </tr>
                <tr>
                  <th class="text-end">Total registrado en factura:</th>
                  <td class="text-end">
                    $ {{ number_format($factura->total, 2) }}
                  </td>
                </tr>
                @if(abs($totalCalculado - $factura->total) > 0.01)
                  <tr>
                    <td colspan="2" class="text-end text-warning small">
                      ⚠ Diferencia entre detalle y total guardado.
                    </td>
                  </tr>
                @endif
              </table>
            </div>
          </div>

          <div class="mt-4">
            <p class="small text-muted mb-1">
              Gracias por confiar en la administración del cementerio.
            </p>
            <p class="small text-muted mb-0">
              Conserve este comprobante como respaldo de su pago.
            </p>
          </div>

        </div>
      </div>
    </div>

    <style>
      @media print {
        body {
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }

        .btn,
        .navbar,
        .footer,
        .alert,
        a[href]:after {
          display: none !important;
        }

        .card {
          border: none !important;
          box-shadow: none !important;
        }

        .container {
          max-width: 100% !important;
        }
      }
    </style>

    <x-app.footer />
  </main>
</x-app-layout>
