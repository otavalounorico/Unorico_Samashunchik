{{-- CABECERA DEL MODAL (Azul Informativo) --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle del Beneficio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- CUERPO DEL MODAL --}}
<div class="modal-body pt-3">
    
    {{-- Tarjeta destacada para Código y Valor --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código Único</small>
            <span class="fw-bold text-dark fs-5">{{ $beneficio->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Valor Actual</small>
            <span class="fw-bold text-success fs-5">
                {{ $beneficio->valor ? '$ ' . number_format($beneficio->valor, 2) : '—' }}
            </span>
        </div>
    </div>

    <div class="row g-3">
        {{-- Información General --}}
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información General</h6>
        </div>

        <div class="col-8">
            <label class="d-block text-muted small mb-0">Nombre del Beneficio</label>
            <div class="fw-bold text-dark">{{ $beneficio->nombre }}</div>
        </div>
        <div class="col-4">
            <label class="d-block text-muted small mb-0">Tipo</label>
            <span class="badge bg-secondary">{{ $beneficio->tipo }}</span>
        </div>

        <div class="col-12">
            <label class="d-block text-muted small mb-0">Descripción</label>
            <div class="p-2 bg-light border rounded text-sm">
                {{ $beneficio->descripcion ?? 'Sin descripción detallada.' }}
            </div>
        </div>

        {{-- Auditoría --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Última actualización: {{ $beneficio->updated_at->diffForHumans() }}</span>
            <span>Creado: {{ $beneficio->created_at->format('d/m/Y') }}</span>
        </div>
    </div>
</div>

{{-- PIE DEL MODAL --}}
<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>