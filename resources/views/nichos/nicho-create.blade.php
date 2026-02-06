<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Registro de Nicho</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form method="POST" action="{{ route('nichos.store') }}">
    @csrf
    <div class="modal-body">
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> Si seleccionas una <b>Ubicación en Mapa</b>, el código se copiará automáticamente.
        </div>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold text-primary">Ubicación en Mapa (GIS)</label>
                <select name="nicho_geom_id" class="form-select select2">
                    <option value="">-- Generar Código Automático (Manual) --</option>
                    @isset($nichosGeom)
                        @foreach($nichosGeom as $ng)
                            <option value="{{ $ng->id }}" @selected(old('nicho_geom_id') == $ng->id)>{{ $ng->codigo }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Bloque Físico <span class="text-danger">*</span></label>
                <select name="bloque_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($bloques as $b)
                        <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold">Socio Titular</label>
                <select name="socio_id" class="form-select">
                    <option value="">-- Sin asignar --</option>
                    @foreach($socios as $s)
                        <option value="{{ $s->id }}">{{ $s->apellidos }} {{ $s->nombres }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                <select name="tipo_nicho" class="form-select" required>
                    <option value="PROPIO">PROPIO</option>
                    <option value="COMPARTIDO">COMPARTIDO</option>
                </select>
            </div>

            {{-- NUEVO: CLASE --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Clase Construcción <span class="text-danger">*</span></label>
                <select name="clase_nicho" class="form-select" required>
                    <option value="BOVEDA">Bóveda</option>
                    <option value="TIERRA">Tierra</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Capacidad Total <span class="text-danger">*</span></label>
                <input type="number" name="capacidad" min="1" value="3" class="form-control" required>
            </div>

            {{-- NUEVO: ESTADO FÍSICO --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Estado Físico <span class="text-danger">*</span></label>
                <select name="estado" class="form-select" required>
                    <option value="BUENO">Bueno</option>
                    <option value="MANTENIMIENTO">En Mantenimiento</option>
                    <option value="MALO">Malo</option>
                    <option value="ABANDONADO">Abandonado</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>