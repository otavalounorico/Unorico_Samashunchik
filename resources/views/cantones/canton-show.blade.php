{{-- ESTILOS LOCALES PARA ESTA VISTA --}}
<style>
    .badge-oscuro {
        background-color: #999da0ff !important; 
        color: #ffffff !important;
    }
</style>

{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle del Cantón</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- CUERPO DEL MODAL --}}
<div class="modal-body pt-3">

    {{-- Tarjeta destacada para Código e ID --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código del Cantón</small>
            <span class="fw-bold text-dark fs-5">{{ $canton->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">ID Interno</small>
            <span class="fw-bold text-dark fs-5">#{{ $canton->id }}</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información General</h6>
        </div>

        <div class="col-12">
            <label class="d-block text-muted small mb-0">Nombre del Cantón</label>
            <div class="fw-bold text-dark fs-6">{{ $canton->nombre }}</div>
        </div>

        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">
                Parroquias Asociadas
                <span class="badge badge-oscuro ms-1">{{ $canton->parroquias->count() }}</span>
            </h6>
        </div>

        <div class="col-12">
            @if($canton->parroquias->count() > 0)
                <div class="bg-light p-3 rounded border">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($canton->parroquias as $parroquia)
                            <span class="badge bg-white text-dark border shadow-sm">
                                <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 0.7rem;"></i>
                                {{ $parroquia->nombre }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-muted small fst-italic">No hay parroquias registradas en este cantón.</div>
            @endif
        </div>

        {{-- AUDITORÍA --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Registrado por: <strong>{{ auth()->user()->name ?? 'Sistema' }}</strong></span>
            <span>Fecha: {{ $canton->created_at ? $canton->created_at->format('d/m/Y H:i') : '—' }}</span>
        </div>
    </div>
</div>

{{-- PIE DEL MODAL --}}
<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>