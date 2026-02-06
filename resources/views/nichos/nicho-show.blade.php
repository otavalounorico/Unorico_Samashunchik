<div class="modal-header bg-info text-white border-bottom-0 pb-0 py-2">
    <h5 class="modal-title fw-bold fs-6">Detalle del Nicho</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pt-2 pb-0">
    
    {{-- TARJETA DESTACADA --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-2 py-2 px-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Código</small>
            <span class="fw-bold text-primary fs-5">{{ $nicho->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Estado</small>
            @switch($nicho->estado)
                @case('BUENO') <span class="badge bg-success">Bueno</span> @break
                @case('MANTENIMIENTO') <span class="badge bg-warning text-dark">Mantenimiento</span> @break
                @case('MALO') <span class="badge bg-danger">Malo</span> @break
                @default <span class="badge bg-secondary">{{ ucfirst(strtolower($nicho->estado)) }}</span>
            @endswitch
        </div>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs nav-fill mb-2" id="showTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#info-tab">
                <i class="fas fa-info-circle me-1"></i> General
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#detalles-tab">
                <i class="fas fa-list-ul me-1"></i> Detalles
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        {{-- TAB 1: GENERAL --}}
        <div class="tab-pane fade show active" id="info-tab">
            <div class="row g-2">
                <div class="col-12">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Socio Titular</label>
                    @if($nicho->socio)
                        <div class="fw-bold text-dark border-bottom pb-1 fs-6">
                            {{ $nicho->socio->apellidos }} {{ $nicho->socio->nombres }}
                        </div>
                    @else
                        <div class="text-muted fst-italic">Sin socio asignado</div>
                    @endif
                </div>

                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Bloque</label>
                    <div class="fw-bold text-dark small">{{ $nicho->bloque->nombre ?? '-' }}</div>
                    <div class="text-muted text-xs">{{ $nicho->bloque->codigo ?? '' }}</div>
                </div>

                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Ocupación</label>
                    <div class="fw-bold {{ $nicho->ocupacion >= $nicho->capacidad ? 'text-danger' : 'text-success' }}">
                        {{ $nicho->ocupacion }} / {{ $nicho->capacidad }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: DETALLES --}}
        <div class="tab-pane fade" id="detalles-tab">
            <div class="row g-2 mt-1">
                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Clase</label>
                    @if($nicho->clase_nicho == 'TIERRA')
                        <span class="badge bg-secondary w-100 py-2"><i class="fas fa-seedling me-1"></i> TIERRA</span>
                    @else
                        <span class="badge bg-dark w-100 py-2"><i class="fas fa-dungeon me-1"></i> BÓVEDA</span>
                    @endif
                </div>
                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Tipo</label>
                    <span class="badge w-100 py-2 {{ $nicho->tipo_nicho == 'PROPIO' ? 'bg-info text-dark border' : 'bg-primary' }}">
                        {{ $nicho->tipo_nicho }}
                    </span>
                </div>

                <div class="col-12 mt-2">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Notas</label>
                    <div class="p-2 border rounded bg-light text-secondary text-xs">
                        {{ $nicho->descripcion ?: 'Sin descripción.' }}
                    </div>
                </div>
                
                <div class="col-12 mt-1">
                    <small class="text-muted text-uppercase" style="font-size: 0.6rem;">UUID QR (Interno)</small>
                    <div class="font-monospace text-xs text-muted text-truncate">{{ $nicho->qr_uuid }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER AUDITORÍA --}}
    <div class="mt-2 pt-2 border-top d-flex justify-content-between text-xs text-muted pb-2">
        <span>Reg: {{ $nicho->creador->name ?? 'Sistema' }}</span>
        <span>{{ $nicho->created_at->format('d/m/Y H:i') }}</span>
    </div>
</div>

<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary w-100 btn-sm" data-bs-dismiss="modal">Cerrar</button>
</div>