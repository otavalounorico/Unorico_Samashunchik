{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle del Registro</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- CUERPO DEL MODAL --}}
<div class="modal-body pt-3">
    
    {{-- Tarjeta destacada (ORIGINAL) --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código Nicho</small>
            <span class="fw-bold text-dark fs-5">{{ $nicho->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Estado</small>
            @switch($nicho->estado)
                @case('disponible') <span class="badge bg-success">Disponible</span> @break
                @case('ocupado') <span class="badge bg-danger">Ocupado</span> @break
                @default <span class="badge bg-warning text-dark">{{ ucfirst($nicho->estado) }}</span>
            @endswitch
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información General</h6>
        </div>

        {{-- Bloque (ORIGINAL) --}}
        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Bloque Asignado</label>
            <div class="fw-bold text-dark">{{ $nicho->bloque->nombre ?? 'Sin bloque' }}</div>
            <small class="text-muted">{{ $nicho->bloque->codigo ?? '' }}</small>
        </div>

        {{-- SOCIO (NUEVO - Insertado aquí) --}}
        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Socio Titular</label>
            @if($nicho->socio)
                <div class="fw-bold text-primary">
                    <i class="fas fa-user-tie me-1"></i> {{ $nicho->socio->apellidos ?? '' }} {{ $nicho->socio->nombres ?? '' }}
                </div>
            @else
                <span class="badge bg-light text-secondary border">Sin asignar</span>
            @endif
        </div>

        {{-- TIPO (NUEVO) --}}
        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Tipo</label>
            <div>
                @if($nicho->tipo_nicho === 'PROPIO')
                    <span class="badge bg-gradient-info text-dark border">PROPIO</span>
                @else
                    <span class="badge bg-gradient-primary">COMPARTIDO</span>
                @endif
            </div>
        </div>

        {{-- Capacidad (ORIGINAL - Ajustado a col-4) --}}
        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Capacidad</label>
            <div class="fw-bold text-dark">{{ $nicho->capacidad }}</div>
        </div>

        {{-- Disponibilidad (ORIGINAL - Ajustado a col-4) --}}
        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Disponibilidad</label>
            <div>{!! $nicho->disponible ? '<span class="text-success fw-bold"><i class="fas fa-check me-1"></i>Sí</span>' : '<span class="text-danger fw-bold"><i class="fas fa-times me-1"></i>No</span>' !!}</div>
        </div>

        {{-- DESCRIPCIÓN (NUEVO - Fila completa) --}}
        <div class="col-12 mt-2">
            <label class="d-block text-muted small mb-0">Descripción / Notas</label>
            <div class="p-2 border rounded bg-light text-secondary text-sm">
                {{ $nicho->descripcion ?: 'Sin descripción registrada.' }}
            </div>
        </div>

        {{-- Datos Técnicos (ORIGINAL) --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Datos Técnicos</h6>
        </div>

        <div class="col-12">
            <label class="d-block text-muted small mb-0">Código QR (Interno)</label>
            <div class="bg-light p-2 rounded border text-sm font-monospace text-secondary text-break">
                {{ $nicho->qr_uuid ?? 'Generado automáticamente' }}
            </div>
        </div>

        {{-- Auditoría (ORIGINAL) --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Creado por: <strong>{{ $nicho->creador->name ?? 'Sistema' }}</strong></span>
            <span>Fecha: {{ $nicho->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>