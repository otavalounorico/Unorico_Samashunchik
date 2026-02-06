{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Cantón</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('cantones.store') }}">
    @csrf
    
    <div class="modal-body">
        
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código se genera automáticamente.
        </div>

        {{-- Alerta general (Opcional, ya que pondremos error abajo) --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                Por favor corrige los errores abajo.
            </div>
        @endif

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold">Nombre del Cantón <span class="text-danger">*</span></label>
                
                {{-- AQUI ESTA LA MAGIA: is-invalid pinta el borde rojo --}}
                <input name="nombre" 
                       value="{{ old('nombre') }}" 
                       class="form-control @error('nombre') is-invalid @enderror" 
                       required maxlength="255" 
                       placeholder="Ingrese el nombre">

                {{-- Mensaje de error en rojo debajo del input --}}
                @error('nombre')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>