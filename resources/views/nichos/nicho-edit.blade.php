{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Registro de Nicho</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('nichos.update', $nicho) }}">
    @csrf @method('PUT')
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Código (Editable solo si es necesario corregir) --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Código <span class="text-danger">*</span></label>
                <input name="codigo" value="{{ old('codigo', $nicho->codigo) }}" class="form-control" required>
            </div>

            {{-- Bloque --}}
            <div class="col-md-8">
                <label class="form-label fw-bold">Bloque <span class="text-danger">*</span></label>
                <select name="bloque_id" class="form-select" required>
                    @foreach($bloques as $b)
                        <option value="{{ $b->id }}" @selected(old('bloque_id', $nicho->bloque_id)==$b->id)>
                            {{ $b->nombre }} ({{ $b->codigo }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Capacidad --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Capacidad <span class="text-danger">*</span></label>
                <input type="number" min="1" step="1" name="capacidad" value="{{ old('capacidad',$nicho->capacidad) }}" class="form-control" required>
            </div>
            
            {{-- Estado --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                <select name="estado" class="form-select" required>
                    @foreach(['disponible'=>'Disponible','ocupado'=>'Ocupado','mantenimiento'=>'Mantenimiento'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('estado',$nicho->estado)==$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>