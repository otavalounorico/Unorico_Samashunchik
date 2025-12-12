{{-- CABECERA DEL MODAL (Azul Informativo) --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle del Registro</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- CUERPO DEL MODAL --}}
<div class="modal-body pt-3">
    
    {{-- Tarjeta destacada para Código y Cédula --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código Único</small>
            <span class="fw-bold text-dark fs-5">{{ $fallecido->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Cédula</small>
            <span class="fw-bold text-dark fs-5">{{ $fallecido->cedula ?? 'S/N' }}</span>
        </div>
    </div>

    <div class="row g-3">
        {{-- Información Personal --}}
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información Personal</h6>
        </div>

        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Apellidos</label>
            <div class="fw-bold text-dark">{{ $fallecido->apellidos }}</div>
        </div>
        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Nombres</label>
            <div class="fw-bold text-dark">{{ $fallecido->nombres }}</div>
        </div>

        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Género</label>
            <div>{{ $fallecido->genero->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Estado Civil</label>
            <div>{{ $fallecido->estadoCivil->nombre ?? '—' }}</div>
        </div>

        {{-- Ubicación --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Ubicación</h6>
        </div>

        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Comunidad</label>
            <div class="fw-bold">{{ $fallecido->comunidad->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Parroquia</label>
            <div>{{ $fallecido->comunidad->parroquia->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="d-block text-muted small mb-0">Cantón</label>
            <div>{{ $fallecido->comunidad->parroquia->canton->nombre ?? '—' }}</div>
        </div>

        {{-- Fechas Importantes --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Fechas</h6>
        </div>

        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Fecha de Nacimiento</label>
            <div>{{ optional($fallecido->fecha_nac)->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Fecha de Fallecimiento</label>
            <div class="fw-bold text-danger">
                {{ optional($fallecido->fecha_fallecimiento)->format('d/m/Y') ?? '—' }}
                @if($fallecido->fecha_fallecimiento)
                    <small class="text-muted fw-normal ms-1">
                        ({{ $fallecido->fecha_nac ? $fallecido->fecha_nac->diffInYears($fallecido->fecha_fallecimiento) : '?' }} años)
                    </small>
                @endif
            </div>
        </div>

        {{-- Observaciones --}}
        @if($fallecido->observaciones)
            <div class="col-12 mt-3">
                <label class="d-block text-muted small mb-1 fw-bold">Observaciones</label>
                <div class="bg-light p-2 rounded border text-sm text-secondary">
                    {{ $fallecido->observaciones }}
                </div>
            </div>
        @endif

        {{-- Auditoría --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Registrado por: <strong>{{ $fallecido->creador->name ?? 'Sistema' }}</strong></span>
            <span>Fecha: {{ $fallecido->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

{{-- PIE DEL MODAL --}}
<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>