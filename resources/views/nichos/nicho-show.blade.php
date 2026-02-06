<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle del Registro</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pt-3">
    
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código Nicho</small>
            <span class="fw-bold text-dark fs-5">{{ $nicho->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Estado Físico</small>
            @switch($nicho->estado)
                @case('BUENO') <span class="badge bg-success">Bueno</span> @break
                @case('MANTENIMIENTO') <span class="badge bg-warning text-dark">Mantenimiento</span> @break
                @case('MALO') <span class="badge bg-danger">Malo</span> @break
                @default <span class="badge bg-secondary">{{ ucfirst($nicho->estado) }}</span>
            @endswitch
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información General</h6>
        </div>

        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Bloque</label>
            <div class="fw-bold text-dark">{{ $nicho->bloque->nombre ?? 'Sin bloque' }}</div>
            <small class="text-muted">{{ $nicho->bloque->codigo ?? '' }}</small>
        </div>

        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Socio Titular</label>
            @if($nicho->socio)
                <div class="fw-bold text-primary"><i class="fas fa-user-tie me-1"></i> {{ $nicho->socio->apellidos }} {{ $nicho->socio->nombres }}</div>
            @else
                <span class="badge bg-light text-secondary border">Sin asignar</span>
            @endif
        </div>

        {{-- NUEVO: CLASE --}}
        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Clase</label>
            @if($nicho->clase_nicho == 'TIERRA')
                <span class="badge bg-secondary"><i class="fas fa-seedling me-1"></i> Tierra</span>
            @else
                <span class="badge bg-dark"><i class="fas fa-dungeon me-1"></i> Bóveda</span>
            @endif
        </div>

        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Tipo</label>
            <span class="badge bg-gradient-{{ $nicho->tipo_nicho == 'PROPIO' ? 'info text-dark border' : 'primary' }}">{{ $nicho->tipo_nicho }}</span>
        </div>

        {{-- NUEVO: OCUPACIÓN --}}
        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Ocupación</label>
            <div class="fw-bold {{ $nicho->ocupacion >= $nicho->capacidad ? 'text-danger' : 'text-success' }}">
                {{ $nicho->ocupacion }} / {{ $nicho->capacidad }}
            </div>
        </div>

        <div class="col-12 mt-2">
            <label class="d-block text-muted small mb-0">Descripción</label>
            <div class="p-2 border rounded bg-light text-secondary text-sm">{{ $nicho->descripcion ?: 'Sin descripción registrada.' }}</div>
        </div>

        <div class="col-12 mt-3"><h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Datos Técnicos</h6></div>
        <div class="col-12">
            <label class="d-block text-muted small mb-0">Código QR (Interno)</label>
            <div class="bg-light p-2 rounded border text-sm font-monospace text-secondary text-break">{{ $nicho->qr_uuid ?? 'Automático' }}</div>
        </div>
        
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Creado por: <strong>{{ $nicho->creador->name ?? 'Sistema' }}</strong></span>
            <span>Fecha: {{ $nicho->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>
<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>