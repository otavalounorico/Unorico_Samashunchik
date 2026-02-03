<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Corregir Datos</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('asignaciones.update', $nicho->id) }}">
    @csrf @method('PUT')

    @php
        $socioActual = $nicho->socios->first();
        $fallecidoActual = $nicho->fallecidos->where('pivot.fecha_exhumacion', null)->first();
    @endphp

    {{-- Mantenemos los IDs anteriores para que el controlador haga el swap --}}
    <input type="hidden" name="socio_anterior_id" value="{{ $socioActual->id ?? '' }}">
    <input type="hidden" name="fallecido_anterior_id" value="{{ $fallecidoActual->id ?? '' }}">

    <div class="modal-body">
        <div class="alert alert-warning py-2 mb-3 text-xs">
            <i class="fas fa-exclamation-triangle me-1"></i> Use esto para corregir errores de selección.
        </div>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold text-muted">Nicho</label>
                <input value="{{ $nicho->codigo }} - Bloque {{ $nicho->bloque->descripcion ?? '' }}" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-bold">Socio Responsable</label>
                <select name="socio_id" class="form-select">
                    @foreach($socios as $s)
                        <option value="{{ $s->id }}" @selected($socioActual && $s->id == $socioActual->id)>
                            {{ $s->apellidos }} {{ $s->nombres }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Rol</label>
                <select name="rol" class="form-select">
                    <option value="TITULAR" @selected($socioActual && $socioActual->pivot->rol == 'TITULAR')>TITULAR</option>
                    <option value="RESPONSABLE" @selected($socioActual && $socioActual->pivot->rol == 'RESPONSABLE')>RESPONSABLE</option>
                </select>
            </div>

            <div class="col-12"><hr class="my-1 text-muted"></div>

            <div class="col-12">
                <label class="fw-bold text-primary mb-2">Fallecido Asignado</label>
                @if($fallecidoActual)
                    <select name="fallecido_id" class="form-select mb-2">
                        @foreach($fallecidos as $f)
                            <option value="{{ $f->id }}" @selected($f->id == $fallecidoActual->id)>
                                {{ $f->apellidos }} {{ $f->nombres }}
                            </option>
                        @endforeach
                    </select>
                    
                    <div class="col-md-6 mt-2">
                        <label class="form-label fw-bold">Fecha Inhumación</label>
                        <input type="date" name="fecha_inhumacion" 
                               value="{{ $fallecidoActual->pivot->fecha_inhumacion ? $fallecidoActual->pivot->fecha_inhumacion->format('Y-m-d') : '' }}" 
                               class="form-control" required>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-label fw-bold">Observación</label>
                        <textarea name="observacion" class="form-control" rows="2">{{ $fallecidoActual->pivot->observacion }}</textarea>
                    </div>
                @else
                    <div class="text-muted">No hay fallecido activo para editar.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Guardar Cambios</button>
    </div>
</form>