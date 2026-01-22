<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detalle e Historial</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row g-3">
        <div class="col-12 bg-light p-3 rounded border mb-2 d-flex justify-content-between align-items-center">
             <div><small class="text-muted d-block">Código Nicho</small><span class="fw-bold text-primary fs-5">{{ $nicho->codigo }}</span></div>
             <div class="text-end"><span class="badge {{ $nicho->estado == 'DISPONIBLE' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $nicho->estado }}</span></div>
        </div>

        <div class="col-12 border-bottom pb-2 mb-2"><h6 class="fw-bold mb-0">Responsable</h6></div>
        @if($nicho->socios->isNotEmpty())
            @php $socio = $nicho->socios->first(); @endphp
            <div class="col-md-8 fw-semibold">{{ $socio->nombre_completo }}</div>
            <div class="col-md-4 text-end badge bg-secondary">{{ $socio->pivot->rol }}</div>
        @else
            <div class="col-12 text-muted">-- Sin responsable --</div>
        @endif

        <div class="col-12 mt-3">
            <h6 class="fw-bold border-bottom pb-2">Historial</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered text-center align-middle" style="font-size: 0.8rem;">
                    <thead class="table-light"><tr><th>Nombre</th><th>Inhumación</th><th>Exhumación</th><th>Doc</th></tr></thead>
                    <tbody>
                        @forelse($nicho->fallecidos as $f)
                            <tr>
                                <td class="text-start ps-2">{{ $f->apellidos }} {{ $f->nombres }}</td>
                                <td>{{ optional($f->pivot->fecha_inhumacion)->format('d/m/Y') }}</td>
                                <td>
                                    @if($f->pivot->fecha_exhumacion)
                                        <span class="text-danger fw-bold">{{ $f->pivot->fecha_exhumacion->format('d/m/Y') }}</span>
                                    @else - @endif
                                </td>
                                <td>
                                    @if($f->pivot->fecha_exhumacion)
                                        <a href="{{ route('asignaciones.pdf.certificado', [$nicho->id, $f->id]) }}" target="_blank" class="btn btn-link text-dark p-0 m-0" title="Certificado PDF"><i class="fas fa-file-pdf"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">Sin historial.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer justify-content-between">
    @if(isset($nicho->bloque))
        <a href="https://URL-DE-TU-COMPANERA.com/mapa?bloque={{ $nicho->bloque }}" 
           target="_blank" 
           class="btn btn-info text-white">
            <i class="fas fa-map-marked-alt"></i> Ver Bloque en Mapa
        </a>
    @else
        <button class="btn btn-secondary" disabled>Sin Ubicación</button>
    @endif

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>