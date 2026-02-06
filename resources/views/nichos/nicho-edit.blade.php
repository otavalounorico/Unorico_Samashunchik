{{-- REUTILIZAMOS LOS MISMOS ESTILOS Y LIBRERÍAS --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<style>
    /* PEGAR EL MISMO CSS DE 'CREATE' AQUÍ PARA QUE SEAN IDÉNTICOS */
    .ts-wrapper .ts-control { border: 1px solid #dee2e6 !important; background-color: #fff !important; border-radius: 0.375rem !important; padding: 0.5rem 0.75rem !important; min-height: 40px !important; font-size: 1rem !important; box-shadow: none !important; display: flex; align-items: center; }
    .ts-wrapper.focus .ts-control { border-color: #5ea6f7 !important; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25) !important; outline: 0 !important; }
    .ts-dropdown { z-index: 99999 !important; border-color: #5ea6f7 !important; border-radius: 0.375rem !important; margin-top: 4px !important; }
    .ts-dropdown .option { padding: 10px 15px !important; font-size: 0.9rem !important; }
    .ts-dropdown .active { background-color: #e7f1ff !important; color: #1c2a48 !important; font-weight: 600 !important; }
    .ts-wrapper.single .ts-control::after { display: none !important; }
</style>

<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">
        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Nicho
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST" action="{{ route('nichos.update', $nicho) }}">
    @csrf @method('PUT')
    
    <div class="modal-body">
        <ul class="nav nav-tabs nav-fill mb-3" id="editTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-ubicacion" type="button"><i class="fas fa-map-marker-alt me-1"></i> Ubicación</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-caracteristicas" type="button"><i class="fas fa-cogs me-1"></i> Datos Técnicos</button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- TAB 1 --}}
            <div class="tab-pane fade show active" id="edit-ubicacion">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small">Código Actual</label>
                        <input value="{{ $nicho->codigo }}" class="form-control bg-light" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-primary small">Mapa GIS</label>
                        {{-- ID único para Edit --}}
                        <select name="nicho_geom_id" id="select_gis_edit">
                            <option value="">-- Ninguno --</option>
                            @isset($nichosGeom)
                                @foreach($nichosGeom as $ng)
                                    <option value="{{ $ng->id }}" @selected(old('nicho_geom_id', $nicho->nicho_geom_id) == $ng->id)>{{ $ng->codigo }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Bloque <span class="text-danger">*</span></label>
                        <select name="bloque_id" id="select_bloque_edit" required>
                            @foreach($bloques as $b)
                                <option value="{{ $b->id }}" @selected(old('bloque_id', $nicho->bloque_id)==$b->id)>{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Socio Titular <span class="text-danger">*</span></label>
                        <select name="socio_id" id="select_socio_edit" required>
                            @foreach($socios as $s)
                                <option value="{{ $s->id }}" @selected(old('socio_id', $nicho->socio_id)==$s->id)>{{ $s->apellidos }} {{ $s->nombres }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- TAB 2 --}}
            <div class="tab-pane fade" id="edit-caracteristicas">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tipo</label>
                        <select name="tipo_nicho" class="form-select" required>
                            <option value="PROPIO" @selected($nicho->tipo_nicho == 'PROPIO')>PROPIO</option>
                            <option value="COMPARTIDO" @selected($nicho->tipo_nicho == 'COMPARTIDO')>COMPARTIDO</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Clase</label>
                        <select name="clase_nicho" class="form-select" required>
                            <option value="BOVEDA" @selected($nicho->clase_nicho == 'BOVEDA')>Bóveda</option>
                            <option value="TIERRA" @selected($nicho->clase_nicho == 'TIERRA')>Tierra</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Capacidad</label>
                        <input type="number" min="1" name="capacidad" value="{{ old('capacidad', $nicho->capacidad) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estado</label>
                        <select name="estado" class="form-select" required>
                            @foreach(['BUENO','MANTENIMIENTO','MALO','ABANDONADO'] as $e)
                                <option value="{{ $e }}" @selected($nicho->estado == $e)>{{ ucfirst(strtolower($e)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Notas</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $nicho->descripcion) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning fw-bold px-4">Actualizar</button>
    </div>
</form>

<script>
    var settingsEdit = { create: false, sortField: { field: "text", direction: "asc" }, plugins: ['dropdown_input'] };
    new TomSelect("#select_gis_edit", settingsEdit);
    new TomSelect("#select_bloque_edit", settingsEdit);
    new TomSelect("#select_socio_edit", settingsEdit);
</script>