{{-- LIBRERÍAS (Asegúrate que carguen) --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<style>
    /* 1. FORZAR ESTÉTICA IDENTICA A TUS INPUTS */
    .ts-wrapper .ts-control {
        border: 1px solid #dee2e6 !important; /* Mismo borde gris suave que tus inputs */
        background-color: #fff !important;
        border-radius: 0.375rem !important;
        padding: 0.5rem 0.75rem !important;
        height: auto !important;
        min-height: 40px !important; /* Altura cómoda */
        font-size: 1rem !important;
        box-shadow: none !important; /* Quitar sombras internas feas */
        display: flex;
        align-items: center;
    }

    /* 2. ESTILO CUANDO HACES CLIC (FOCUS AZUL) */
    .ts-wrapper.focus .ts-control {
        border-color: #5ea6f7 !important; /* Tu color azul */
        box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25) !important; /* Tu resplandor azul */
        outline: 0 !important;
    }

    /* 3. ARREGLAR EL MENÚ DESPLEGABLE */
    .ts-dropdown {
        z-index: 99999 !important; /* Que flote encima del modal */
        border-color: #5ea6f7 !important;
        border-radius: 0.375rem !important;
        margin-top: 4px !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* 4. ITEMS DENTRO DE LA LISTA */
    .ts-dropdown .option {
        padding: 10px 15px !important;
        font-size: 0.9rem !important;
    }
    .ts-dropdown .active {
        background-color: #e7f1ff !important; /* Azul muy clarito al pasar el mouse */
        color: #1c2a48 !important; /* Texto oscuro */
        font-weight: 600 !important;
    }
    
    /* Ocultar la flechita fea por defecto */
    .ts-wrapper.single .ts-control::after {
        display: none !important;
    }
</style>

<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">
        <i class="fa-solid fa-monument me-2"></i> Nuevo Nicho
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form method="POST" action="{{ route('nichos.store') }}">
    @csrf
    <div class="modal-body">
        
        <div class="alert alert-info py-2 mb-3 text-xs shadow-sm border-0" style="background-color: #e7f1ff; color: #0c5460;">
            <i class="fas fa-info-circle me-1"></i> El <b>Código</b> se genera automáticamente (o selecciona del mapa).
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- TABS --}}
        <ul class="nav nav-tabs nav-fill mb-3" id="createTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#ubicacion" type="button">
                    <i class="fas fa-map-marker-alt me-1"></i> Ubicación
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#caracteristicas" type="button">
                    <i class="fas fa-cogs me-1"></i> Datos Técnicos
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1 --}}
            <div class="tab-pane fade show active" id="ubicacion">
                <div class="row g-3">
                    {{-- GIS --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-primary small">Mapa GIS (Opcional)</label>
                        <select name="nicho_geom_id" id="select_gis" placeholder="Buscar código en mapa...">
                            <option value="">-- Ninguno (Manual) --</option>
                            @isset($nichosGeom)
                                @foreach($nichosGeom as $ng)
                                    <option value="{{ $ng->id }}" @selected(old('nicho_geom_id') == $ng->id)>{{ $ng->codigo }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- Bloque --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Bloque <span class="text-danger">*</span></label>
                        <select name="bloque_id" id="select_bloque" required placeholder="Seleccione...">
                            <option value="">Seleccione...</option>
                            @foreach($bloques as $b)
                                <option value="{{ $b->id }}" @selected(old('bloque_id') == $b->id)>{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Socio --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Socio Titular <span class="text-danger">*</span></label>
                        <select name="socio_id" id="select_socio" required placeholder="Buscar socio...">
                            <option value="">Seleccione...</option>
                            @foreach($socios as $s)
                                <option value="{{ $s->id }}" @selected(old('socio_id') == $s->id)>{{ $s->apellidos }} {{ $s->nombres }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- TAB 2 --}}
            <div class="tab-pane fade" id="caracteristicas">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tipo</label>
                        <select name="tipo_nicho" class="form-select" required>
                            <option value="PROPIO" @selected(old('tipo_nicho') == 'PROPIO')>PROPIO</option>
                            <option value="COMPARTIDO" @selected(old('tipo_nicho') == 'COMPARTIDO')>COMPARTIDO</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Clase</label>
                        <select name="clase_nicho" class="form-select" required>
                            <option value="BOVEDA" @selected(old('clase_nicho') == 'BOVEDA')>Bóveda</option>
                            <option value="TIERRA" @selected(old('clase_nicho') == 'TIERRA')>Tierra</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Capacidad</label>
                        <input type="number" name="capacidad" min="1" value="{{ old('capacidad', 3) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estado</label>
                        <select name="estado" class="form-select" required>
                            <option value="BUENO">Bueno</option>
                            <option value="MANTENIMIENTO">Mantenimiento</option>
                            <option value="MALO">Malo</option>
                            <option value="ABANDONADO">Abandonado</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Notas</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success fw-bold px-4">Guardar</button>
    </div>
</form>

<script>
    // Configuración para que el buscador funcione bien
    var settings = {
        create: false,
        sortField: { field: "text", direction: "asc" },
        plugins: ['dropdown_input'], // Esto permite escribir
    };

    new TomSelect("#select_gis", settings);
    new TomSelect("#select_bloque", settings);
    new TomSelect("#select_socio", settings);
</script>