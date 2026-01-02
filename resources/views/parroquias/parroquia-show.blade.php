{{-- CABECERA DEL MODAL (Azul Informativo) --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle de la Parroquia</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- CUERPO DEL MODAL --}}
<div class="modal-body pt-3">
    
    {{-- Tarjeta destacada para Código e ID --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código</small>
            <span class="fw-bold text-dark fs-5">{{ $parroquia->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">ID Interno</small>
            <span class="fw-bold text-dark fs-5">#{{ $parroquia->id }}</span>
        </div>
    </div>

    <div class="row g-3">
        {{-- Información General --}}
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información General</h6>
        </div>

        <div class="col-6">
            <label class="d-block text-muted small mb-0">Nombre de la Parroquia</label>
            <div class="fw-bold text-dark fs-6">{{ $parroquia->nombre }}</div>
        </div>

        <div class="col-6">
            <label class="d-block text-muted small mb-0">Cantón Perteneciente</label>
            <div class="fw-bold text-dark fs-6">{{ $parroquia->canton->nombre }}</div>
        </div>

        {{-- Comunidades Asociadas --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">
                Comunidades Asociadas <span class="badge bg-primary ms-1">{{ $parroquia->comunidades->count() }}</span>
            </h6>
        </div>

        <div class="col-12">
            @if($parroquia->comunidades->count() > 0)
                <div class="bg-light p-3 rounded border">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($parroquia->comunidades as $comunidad)
                            <span class="badge bg-white text-dark border shadow-sm">
                                <i class="fas fa-users text-success me-1" style="font-size: 0.7rem;"></i> 
                                {{ $comunidad->nombre }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-muted small fst-italic">No hay comunidades registradas en esta parroquia.</div>
            @endif
        </div>

        {{-- AUDITORÍA --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            {{-- Usuario logueado --}}
            <span>Registrado por: <strong>{{ auth()->user()->name ?? 'Sistema' }}</strong></span>
            <span>Fecha: {{ $parroquia->created_at ? $parroquia->created_at->format('d/m/Y H:i') : '—' }}</span>
        </div>
    </div>
</div>

{{-- PIE DEL MODAL --}}
<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>