{{-- CABECERA DEL MODAL (Negra para Crear) --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Rol</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('roles.store') }}">
    @csrf
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Mensaje estilo Parroquias/Servicios --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código del rol se genera automáticamente (ej. R001).
        </div>

        {{-- Errores --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Nombre del Rol --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre del Rol <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Ej. Supervisor de Ventas" required>
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>