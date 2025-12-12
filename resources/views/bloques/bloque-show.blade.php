{{-- CABECERA DEL MODAL (Azul Informativo) --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle del Bloque</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- CUERPO DEL MODAL --}}
<div class="modal-body pt-3">
    
    {{-- Tarjeta destacada para Código --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código Único</small>
            <span class="fw-bold text-dark fs-5">{{ $bloque->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Área Total</small>
            <span class="fw-bold text-dark fs-5">{{ $bloque->area_m2 ? $bloque->area_m2 . ' m²' : '—' }}</span>
        </div>
    </div>

    <div class="row g-3">
        {{-- Información General --}}
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información General</h6>
        </div>

        <div class="col-12">
            <label class="d-block text-muted small mb-0">Nombre del Bloque</label>
            <div class="fw-bold text-dark">{{ $bloque->nombre }}</div>
        </div>

        <div class="col-12">
            <label class="d-block text-muted small mb-0">Descripción</label>
            <div>{{ $bloque->descripcion ?? 'Sin descripción' }}</div>
        </div>

        {{-- Geometría / Ubicación --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Geometría (GIS)</h6>
        </div>

        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Polígono Origen (QGIS)</label>
            <div class="fw-bold">{{ optional($bloque->bloqueGeom)->nombre ?? 'No asignado' }}</div>
        </div>
        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Estado Geometría</label>
            <div>
                @if($bloque->geom) 
                    <span class="badge bg-success">Cargada</span> 
                @else 
                    <span class="badge bg-secondary">Vacía</span> 
                @endif
            </div>
        </div>

        {{-- Auditoría --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Creado por: <strong>{{ $bloque->creador->name ?? 'Sistema' }}</strong></span>
            <span>Fecha: {{ $bloque->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

{{-- PIE DEL MODAL --}}
<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>