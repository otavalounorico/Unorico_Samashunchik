<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Corregir Recibo #{{ $recibo->id }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="{{ route('pagos.update', $recibo->id) }}" method="POST">
    @csrf @method('PUT')
    
    <div class="modal-body bg-light">
        <div class="alert alert-warning small">
            <i class="fas fa-info-circle"></i> Marca los años correctos para este recibo.
        </div>
        
        <div class="card">
            <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                
                @foreach($aniosDisponibles as $anio)
                    
                    {{-- CAMBIO: Filtramos para que SOLO muestre años menores o iguales al actual --}}
                    @if($anio <= date('Y'))
                        <div class="form-check border-bottom py-2">
                            <input class="form-check-input" type="checkbox" name="anios_pagados[]" 
                                   value="{{ $anio }}" id="edit_{{ $anio }}"
                                   @checked(in_array($anio, $aniosMarcados))>
                            <label class="form-check-label fw-bold" for="edit_{{ $anio }}">Año {{ $anio }}</label>
                        </div>
                    @endif

                @endforeach

            </div>
        </div>

        <div class="mt-3">
             <label class="form-label small fw-bold">Observación</label>
             <input type="text" name="observacion" class="form-control" value="{{ $recibo->observacion }}">
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning fw-bold">Guardar Corrección</button>
    </div>
</form>