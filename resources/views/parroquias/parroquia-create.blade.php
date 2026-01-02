{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nueva Parroquia</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('parroquias.store') }}">
    @csrf
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Mensaje informativo --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código se genera automáticamente.
        </div>

        {{-- Mostrar errores --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Select Cantón --}}
            <div class="col-12">
                <label class="form-label fw-bold">Cantón <span class="text-danger">*</span></label>
                <select name="canton_id" class="form-select" required>
                    <option value="">Seleccione...</option>
                    @foreach($cantones as $c)
                        <option value="{{ $c->id }}" @selected(old('canton_id') == $c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Campo Nombre --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre de la Parroquia <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre') }}" class="form-control" required maxlength="255" placeholder="Ej: San Pablo">
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>