{{-- CABECERA DEL MODAL (Azul Informativo) --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle de la Comunidad</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- CUERPO DEL MODAL --}}
<div class="modal-body pt-3">
    
    {{-- Tarjeta destacada para Código e ID --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código Único</small>
            <span class="fw-bold text-dark fs-5">{{ $comunidad->codigo_unico ?? $comunidad->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">ID Interno</small>
            <span class="fw-bold text-dark fs-5">#{{ $comunidad->id }}</span>
        </div>
    </div>

    <div class="row g-3">
        {{-- Información General --}}
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información General</h6>
        </div>

        <div class="col-12">
            <label class="d-block text-muted small mb-0">Nombre de la Comunidad</label>
            <div class="fw-bold text-dark fs-6">{{ $comunidad->nombre }}</div>
        </div>

        {{-- Ubicación Geográfica --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Ubicación Geográfica</h6>
        </div>

        <div class="col-6">
            <label class="d-block text-muted small mb-0">Parroquia</label>
            <div class="fw-bold text-dark">{{ $comunidad->parroquia->nombre }}</div>
        </div>
        <div class="col-6">
            <label class="d-block text-muted small mb-0">Cantón</label>
            <div class="fw-bold text-dark">{{ $comunidad->parroquia->canton->nombre }}</div>
        </div>

        {{-- Auditoría --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Registrado por: <strong>{{ auth()->user()->name ?? 'Sistema' }}</strong></span>
            <span>Fecha: {{ $comunidad->created_at ? $comunidad->created_at->format('d/m/Y H:i') : '—' }}</span>
        </div>
    </div>
</div>

{{-- PIE DEL MODAL --}}
<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>