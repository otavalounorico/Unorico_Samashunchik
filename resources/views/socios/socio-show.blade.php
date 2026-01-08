{{-- CABECERA DEL MODAL (Igual que Comunidad) --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle del Socio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- CUERPO DEL MODAL --}}
<div class="modal-body pt-3">
    
    {{-- 1. TARJETA DESTACADA (Código y Cédula) --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código Socio</small>
            <span class="fw-bold text-primary fs-5">{{ $socio->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Cédula</small>
            <span class="fw-bold text-dark fs-5">{{ $socio->cedula }}</span>
        </div>
    </div>

    <div class="row g-3">
        
        {{-- SECCIÓN: INFORMACIÓN PERSONAL --}}
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Información Personal</h6>
        </div>

        {{-- Nombre Completo --}}
        <div class="col-12">
            <label class="d-block text-muted small mb-0">Apellidos y Nombres</label>
            <div class="fw-bold text-dark fs-6">{{ $socio->apellidos }} {{ $socio->nombres }}</div>
        </div>

        {{-- Edad y Nacimiento --}}
        <div class="col-6">
            <label class="d-block text-muted small mb-0">Edad</label>
            <div class="fw-bold text-dark">{{ $socio->edad }} años</div>
        </div>
        <div class="col-6">
            <label class="d-block text-muted small mb-0">Fecha de Nacimiento</label>
            <div class="fw-bold text-dark">{{ optional($socio->fecha_nac)->format('d/m/Y') ?? '—' }}</div>
        </div>

        {{-- Género y Estado Civil --}}
        <div class="col-6">
            <label class="d-block text-muted small mb-0">Género</label>
            <div class="text-dark">{{ $socio->genero?->nombre ?? '—' }}</div>
        </div>
        <div class="col-6">
            <label class="d-block text-muted small mb-0">Estado Civil</label>
            <div class="text-dark">{{ $socio->estadoCivil?->nombre ?? '—' }}</div>
        </div>

        {{-- SECCIÓN: ESTADO Y BENEFICIOS --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Estado y Beneficios</h6>
        </div>

        {{-- CAMPOS NUEVOS EN SHOW --}}
        <div class="col-6">
            <label class="d-block text-muted small mb-1">Condición</label>
            <span class="fw-bold text-dark">{{ ucfirst(str_replace('_', ' ', $socio->condicion)) }}</span>
        </div>

        <div class="col-6">
            <label class="d-block text-muted small mb-1">Estatus</label>
            @if($socio->estatus == 'vivo')
                <span class="badge rounded-pill" 
                      style="background-color: #198754 !important; color: white !important; font-size: 0.85rem; padding: 0.5em 1em;">
                    VIVO</span>
            @else
                <span class="badge bg-dark">FALLECIDO</span>
            @endif
        </div>

        {{-- Representante --}}
        <div class="col-6 mt-2">
            <label class="d-block text-muted small mb-1">¿Es Representante?</label>
            @if($socio->es_representante)
                <span class="badge bg-success">SÍ</span>
            @else
                <span class="badge bg-secondary text-dark">NO</span>
            @endif
        </div>

        {{-- Beneficio Actual --}}
        <div class="col-6 mt-2">
            <label class="d-block text-muted small mb-1">Beneficio Actual</label>
            @if($socio->tipo_beneficio == 'exonerado')
                <span class="badge rounded-pill" 
                      style="background-color: #198754 !important; color: white !important; font-size: 0.85rem; padding: 0.5em 1em;">
                    EXONERADO
                </span>
            @elseif($socio->tipo_beneficio == 'con_subsidio')
                <span class="badge rounded-pill" 
                      style="background-color: #2062b9ff !important; color: white !important; font-size: 0.85rem; padding: 0.5em 1em;">
                      CON SUBSIDIO</span>
            @else
                <span class="badge bg-secondary text-dark">SIN SUBSIDIO</span>
            @endif
        </div>

        {{-- Fechas Importantes --}}
        <div class="col-6 mt-2">
            <label class="d-block text-muted small mb-0">Fecha Inscripción</label>
            <div class="text-dark">{{ optional($socio->fecha_inscripcion)->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="col-6 mt-2">
            <label class="d-block text-muted small mb-0">Fecha Exoneración</label>
            <div class="text-dark">{{ optional($socio->fecha_exoneracion)->format('d/m/Y') ?? '—' }}</div>
        </div>


        {{-- SECCIÓN: UBICACIÓN Y CONTACTO --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Ubicación y Contacto</h6>
        </div>

        {{-- Jerarquía Geográfica --}}
        <div class="col-12">
            <label class="d-block text-muted small mb-0">Ubicación</label>
            <div class="text-dark">
                <span class="fw-bold">{{ $socio->comunidad?->nombre ?? '—' }}</span> 
                <small class="text-muted">({{ $socio->comunidad?->parroquia?->nombre }} / {{ $socio->comunidad?->parroquia?->canton?->nombre }})</small>
            </div>
        </div>

        <div class="col-12">
            <label class="d-block text-muted small mb-0">Dirección Domiciliaria</label>
            <div class="text-dark">{{ $socio->direccion ?? '—' }}</div>
        </div>

        <div class="col-6">
            <label class="d-block text-muted small mb-0">Teléfono</label>
            <div class="text-dark">{{ $socio->telefono ?? '—' }}</div>
        </div>
        <div class="col-6">
            <label class="d-block text-muted small mb-0">Email</label>
            <div class="text-dark">{{ $socio->email ?? '—' }}</div>
        </div>

        {{-- AUDITORÍA (Footer interno) --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Registrado por: <strong>{{ $socio->creador?->name ?? 'Sistema' }}</strong></span>
            <span>Fecha: {{ $socio->created_at ? $socio->created_at->format('d/m/Y H:i') : '—' }}</span>
        </div>
    </div>
</div>

{{-- PIE DEL MODAL --}}
<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>
