<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Facturas</h4>
        <a href="{{ route('facturas.create') }}" class="btn btn-primary btn-sm">
          + Nueva factura
        </a>
      </div>

      {{-- Mensajes de sesión --}}
      @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
      @endif

      {{-- Buscador --}}
      <form method="GET" action="{{ route('facturas.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
          <input type="text"
                 name="q"
                 value="{{ $q }}"
                 class="form-control"
                 placeholder="Buscar por nombre, apellido o cédula...">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-outline-primary">
            Buscar
          </button>
          <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">
            Limpiar
          </a>
        </div>
      </form>

      @if ($facturas->count() === 0)
        <div class="alert alert-info">
          No hay facturas registradas.
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th style="width: 80px;">N°</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Socio</th>
                <th class="text-end" style="width: 120px;">Total</th>
                <th style="width: 120px;">Estado</th>
                <th style="width: 170px;" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($facturas as $factura)
                @php
                  $estado = strtoupper($factura->estado ?? 'PENDIENTE');
                  $badgeClass = [
                      'PENDIENTE' => 'warning',
                      'EMITIDA'   => 'info',
                      'PAGADA'    => 'success',
                      'ANULADA'   => 'danger',
                  ][$estado] ?? 'secondary';
                @endphp
                <tr>
                  <td>#{{ sprintf('%06d', $factura->id) }}</td>
                  <td>{{ $factura->fecha?->format('d/m/Y') }}</td>
                  <td>
                    {{ $factura->cliente_nombre }}
                    @if($factura->cliente_apellido)
                      {{ ' ' . $factura->cliente_apellido }}
                    @endif
                    @if($factura->cliente_cedula)
                      <br>
                      <small class="text-muted">CI: {{ $factura->cliente_cedula }}</small>
                    @endif
                  </td>
                  <td>
                    @if($factura->socio)
                      {{ $factura->socio->apellidos }} {{ $factura->socio->nombres }}<br>
                      <small class="text-muted">{{ $factura->socio->cedula }}</small>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="text-end">
                    $ {{ number_format($factura->total, 2) }}
                  </td>
                  <td>
                    <span class="badge bg-{{ $badgeClass }}">{{ $estado }}</span>
                  </td>
                  <td class="text-end">
                    <div class="btn-group btn-group-sm" role="group">
                      <a href="{{ route('facturas.show', $factura) }}"
                         class="btn btn-outline-primary">
                        Ver
                      </a>
                      <a href="{{ route('facturas.edit', $factura) }}"
                         class="btn btn-outline-secondary">
                        Editar
                      </a>
                      <form action="{{ route('facturas.destroy', $factura) }}"
                            method="POST"
                            onsubmit="return confirm('¿Seguro que deseas eliminar esta factura?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                          Eliminar
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $facturas->links() }}
        </div>
      @endif
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
