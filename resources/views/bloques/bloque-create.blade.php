{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Registro de Bloque</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('bloques.store') }}">
    @csrf
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Mensaje informativo --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> 
            Si seleccionas un <b>Código GIS</b>, este se asignará automáticamente al bloque.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Fila 1: Nombre y Área --}}
            <div class="col-md-8">
                <label class="form-label fw-bold">Nombre del Bloque <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre') }}" class="form-control" required placeholder="Ej. Bloque Norte">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Área (m²)</label>
                <input type="number" step="0.01" min="0" name="area_m2" value="{{ old('area_m2') }}" class="form-control" placeholder="0.00">
            </div>

            {{-- Fila 2: Geometría (AQUÍ ESTÁ EL CAMBIO) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-primary">Código GIS y Sector (Mapa)</label>
                <select id="bloque_geom_id" name="bloque_geom_id" class="form-select">
                    <option value="">-- Seleccionar Código del Mapa --</option>
                    @isset($bloquesGeom)
                        @foreach($bloquesGeom as $bg)
                            {{-- Muestra: B-01 | Sector Norte --}}
                            <option value="{{ $bg->id }}" @selected(old('bloque_geom_id') == $bg->id)>
                                {{ $bg->codigo }} | {{ $bg->sector ?? 'General' }}
                            </option>
                        @endforeach
                    @endisset
                </select>
                <small class="text-muted text-xs">El código seleccionado (ej. B-20) será el código del nuevo bloque.</small>
            </div>

            {{-- Fila 3: Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
            </div>

            {{-- Input Oculto para JSON --}}
            <input type="hidden" id="geom" name="geom" value="{{ old('geom') }}">
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar Bloque</button>
    </div>
</form>