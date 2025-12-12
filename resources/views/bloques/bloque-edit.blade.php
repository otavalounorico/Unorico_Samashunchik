{{-- CABECERA DEL MODAL (Estilo Advertencia para Editar) --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Bloque</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('bloques.update', $bloque) }}">
    @csrf @method('PUT')
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Errores --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Código --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Código <span class="text-danger">*</span></label>
                <input name="codigo" value="{{ old('codigo', $bloque->codigo) }}" class="form-control" required>
            </div>
            
            {{-- Nombre --}}
            <div class="col-md-8">
                <label class="form-label fw-bold">Nombre del Bloque <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre', $bloque->nombre) }}" class="form-control" required>
            </div>

            {{-- Fila 2: Área y Geometría --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Área (m²)</label>
                <input type="number" step="0.01" min="0" name="area_m2" value="{{ old('area_m2', $bloque->area_m2) }}" class="form-control">
            </div>
            <div class="col-md-8">
                <label class="form-label fw-bold">Geometría (QGIS)</label>
                <select id="bloque_geom_id" name="bloque_geom_id" class="form-select">
                    <option value="">-- Conservar Actual --</option>
                    @isset($bloquesGeom)
                        @foreach($bloquesGeom as $bg)
                            <option value="{{ $bg->id }}" @selected(old('bloque_geom_id', $bloque->bloque_geom_id) == $bg->id)>
                                {{ $bg->id }} - {{ $bg->nombre }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>

            {{-- Fila 3: Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $bloque->descripcion) }}</textarea>
            </div>

            {{-- Input Oculto para JSON Geometría --}}
            <input type="hidden" id="geom" name="geom" value="{{ old('geom', $bloque->geom ? json_encode($bloque->geom) : '') }}">
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>