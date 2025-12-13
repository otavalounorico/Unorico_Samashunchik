{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Registro de Nicho</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('nichos.store') }}">
    @csrf
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código (Ej: N0001) y el QR se generarán automáticamente.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Bloque --}}
            <div class="col-md-12">
                <label class="form-label fw-bold">Bloque <span class="text-danger">*</span></label>
                <select name="bloque_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($bloques as $b)
                        <option value="{{ $b->id }}" @selected(old('bloque_id')==$b->id)>
                            {{ $b->nombre }} ({{ $b->codigo }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Capacidad --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Capacidad <span class="text-danger">*</span></label>
                <input type="number" name="capacidad" min="1" step="1" value="{{ old('capacidad',1) }}" class="form-control" required>
            </div>

            {{-- Estado --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                <select name="estado" class="form-select" required>
                    @foreach(['disponible','ocupado','mantenimiento'] as $e)
                        <option value="{{ $e }}" @selected(old('estado','disponible')==$e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>