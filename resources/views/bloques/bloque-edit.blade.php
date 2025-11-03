<x-app-layout>
  <main class="main-content">
    <x-app.navbar />
    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Editar Bloque</h4>
        <a href="{{ route('bloques.index') }}" class="btn btn-secondary">Volver</a>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger">
          <b>Revisa los campos:</b>
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('bloques.update', $bloque) }}">
          @csrf @method('PUT')
          <div class="row g-3">

            <div class="col-md-4">
              <label class="form-label">Código *</label>
              <input name="codigo" value="{{ old('codigo', $bloque->codigo) }}" class="form-control" required>
            </div>

            <div class="col-md-8">
              <label class="form-label">Nombre *</label>
              <input name="nombre" value="{{ old('nombre', $bloque->nombre) }}" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Área (m²)</label>
              <input type="number" step="0.01" min="0" name="area_m2" value="{{ old('area_m2', $bloque->area_m2) }}" class="form-control">
            </div>

            {{-- Selección de polígono (incluye el actualmente asignado) --}}
            <div class="col-md-8">
              <label class="form-label">Polígono (QGIS) <small class="text-muted">(opcional)</small></label>
              <select name="bloque_geom_id" class="form-control">
                <option value="">-- Seleccione (o conservar actual) --</option>
                @isset($bloquesGeom)
                  @foreach($bloquesGeom as $bg)
                    <option value="{{ $bg->id }}" @if(old('bloque_geom_id', $bloque->bloque_geom_id) == $bg->id) selected @endif>
                      {{ $bg->id }} - {{ $bg->nombre }}
                    </option>
                  @endforeach
                @endisset
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" rows="4" class="form-control">{{ old('descripcion', $bloque->descripcion) }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label d-flex justify-content-between">
                <span>Geom (JSON) <small class="text-muted">(opcional)</small></span>
              </label>
              <textarea name="geom" rows="3" class="form-control">{{ old('geom', $bloque->geom ? json_encode($bloque->geom) : '') }}</textarea>
              <div class="form-text">Si pega GeoJSON se sobreescribirá la geometría; si selecciona Polígono (QGIS) la copiará desde `bloques_geom`.</div>
            </div>

          </div>

          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary">Actualizar</button>
            <a href="{{ route('bloques.index') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div></div>
    </div>
    <x-app.footer />
  </main>
</x-app-layout>
