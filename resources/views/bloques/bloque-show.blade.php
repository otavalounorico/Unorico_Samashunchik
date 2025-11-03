<x-app-layout>
  <main class="main-content">
    <x-app.navbar />
    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Detalle del Bloque</h4>
        <div class="d-flex gap-2">
          <a href="{{ route('bloques.edit',$bloque) }}" class="btn btn-warning">Editar</a>
          <a href="{{ route('bloques.index') }}" class="btn btn-secondary">Volver</a>
        </div>
      </div>

      <div class="card"><div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="text-muted d-block">Código</label>
            <div class="fw-semibold">{{ $bloque->codigo }}</div>
          </div>
          <div class="col-md-8">
            <label class="text-muted d-block">Nombre</label>
            <div class="fw-semibold">{{ $bloque->nombre }}</div>
          </div>

          <div class="col-md-4">
            <label class="text-muted d-block">Área (m²)</label>
            <div class="fw-semibold">{{ $bloque->area_m2 ?? '—' }}</div>
          </div>
          <div class="col-md-8">
            <label class="text-muted d-block">Descripción</label>
            <div class="fw-semibold">{{ $bloque->descripcion ?? '—' }}</div>
          </div>

          <div class="col-12">
            <label class="text-muted d-block">Geom (JSON)</label>
            <pre class="bg-light p-3 rounded" style="white-space:pre-wrap">
{{ $bloque->geom ? json_encode($bloque->geom, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '—' }}
            </pre>
          </div>

          <div class="col-md-6">
            <label class="text-muted d-block">Polígono QGIS (ID)</label>
            <div class="fw-semibold">{{ optional($bloque->bloqueGeom)->id ?? '—' }}</div>
          </div>

          <div class="col-md-6">
            <label class="text-muted d-block">Creado por</label>
            <div class="fw-semibold">{{ $bloque->creador?->name ?? '—' }}</div>
          </div>

          <div class="col-md-3">
            <label class="text-muted d-block">Creado</label>
            <div class="fw-semibold">{{ $bloque->created_at?->format('d/m/Y H:i') }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted d-block">Actualizado</label>
            <div class="fw-semibold">{{ $bloque->updated_at?->format('d/m/Y H:i') }}</div>
          </div>
        </div>
      </div></div>
    </div>
    <x-app.footer />
  </main>
</x-app-layout>
