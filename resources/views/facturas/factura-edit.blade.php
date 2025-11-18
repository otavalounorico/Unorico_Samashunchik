<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Editar factura N° {{ sprintf('%06d', $factura->id) }}</h4>
        <a href="{{ route('facturas.show', $factura) }}" class="btn btn-secondary">Volver</a>
      </div>

      {{-- Errores --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <b>Hay errores en el formulario:</b>
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
      @endif

      <div class="card">
        <div class="card-body">
          <form action="{{ route('facturas.update', $factura) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-3">
              <div class="card-header">
                Datos de la factura
              </div>
              <div class="card-body">
                {{-- Estado --}}
                <div class="mb-3">
                  <label class="form-label">Estado *</label>
                  @php
                    $estados = ['PENDIENTE','EMITIDA','PAGADA','ANULADA'];
                  @endphp
                  <select name="estado" class="form-select" required>
                    @foreach ($estados as $estado)
                      <option value="{{ $estado }}"
                        {{ old('estado', $factura->estado) === $estado ? 'selected' : '' }}>
                        {{ $estado }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- Datos del cliente --}}
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="cliente_nombre" class="form-control"
                           value="{{ old('cliente_nombre', $factura->cliente_nombre) }}" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="cliente_apellido" class="form-control"
                           value="{{ old('cliente_apellido', $factura->cliente_apellido) }}">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Cédula</label>
                    <input type="text" name="cliente_cedula" class="form-control"
                           value="{{ old('cliente_cedula', $factura->cliente_cedula) }}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="cliente_telefono" class="form-control"
                           value="{{ old('cliente_telefono', $factura->cliente_telefono) }}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="cliente_email" class="form-control"
                           value="{{ old('cliente_email', $factura->cliente_email) }}">
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
              <a href="{{ route('facturas.show', $factura) }}" class="btn btn-secondary">
                Cancelar
              </a>
              <button type="submit" class="btn btn-primary">
                Guardar cambios
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
