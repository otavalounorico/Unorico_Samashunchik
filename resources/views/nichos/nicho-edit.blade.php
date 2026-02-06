<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Registro de Nicho</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST" action="{{ route('nichos.update', $nicho) }}">
    @csrf @method('PUT')
    
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold">Código Actual</label>
                <input name="codigo" value="{{ old('codigo', $nicho->codigo) }}" class="form-control bg-light" readonly>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold text-primary">Vincular con Mapa (GIS)</label>
                <select name="nicho_geom_id" class="form-select">
                    <option value="">-- Sin Mapa (Manual) --</option>
                    @isset($nichosGeom)
                        @foreach($nichosGeom as $ng)
                            <option value="{{ $ng->id }}" @selected(old('nicho_geom_id', $nicho->nicho_geom_id) == $ng->id)>{{ $ng->codigo }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Bloque <span class="text-danger">*</span></label>
                <select name="bloque_id" class="form-select" required>
                    @foreach($bloques as $b)
                        <option value="{{ $b->id }}" @selected(old('bloque_id', $nicho->bloque_id)==$b->id)>{{ $b->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Socio Titular</label>
                <select name="socio_id" class="form-select">
                    <option value="">-- Sin asignar --</option>
                    @foreach($socios as $s)
                        <option value="{{ $s->id }}" @selected(old('socio_id', $nicho->socio_id)==$s->id)>{{ $s->apellidos }} {{ $s->nombres }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo</label>
                <select name="tipo_nicho" class="form-select" required>
                    <option value="PROPIO" @selected(old('tipo_nicho', $nicho->tipo_nicho) == 'PROPIO')>PROPIO</option>
                    <option value="COMPARTIDO" @selected(old('tipo_nicho', $nicho->tipo_nicho) == 'COMPARTIDO')>COMPARTIDO</option>
                </select>
            </div>

            {{-- NUEVO: CLASE --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Clase Construcción</label>
                <select name="clase_nicho" class="form-select" required>
                    <option value="BOVEDA" @selected(old('clase_nicho', $nicho->clase_nicho) == 'BOVEDA')>Bóveda</option>
                    <option value="TIERRA" @selected(old('clase_nicho', $nicho->clase_nicho) == 'TIERRA')>Tierra</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Capacidad</label>
                <input type="number" min="1" name="capacidad" value="{{ old('capacidad', $nicho->capacidad) }}" class="form-control" required>
            </div>
            
            {{-- NUEVO: ESTADO FÍSICO --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Estado Físico</label>
                <select name="estado" class="form-select" required>
                    @foreach(['BUENO','MANTENIMIENTO','MALO','ABANDONADO'] as $e)
                        <option value="{{ $e }}" @selected(old('estado', $nicho->estado) == $e)>{{ ucfirst(strtolower($e)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $nicho->descripcion) }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>