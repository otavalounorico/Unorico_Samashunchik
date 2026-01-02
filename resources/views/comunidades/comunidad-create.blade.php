{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nueva Comunidad</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('comunidades.store') }}">
    @csrf
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código se genera automáticamente.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Cantón --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Cantón <span class="text-danger">*</span></label>
                {{-- ID "canton_select" es clave para el JS --}}
                <select id="canton_select" class="form-select" required>
                    <option value="">— Selecciona —</option>
                    {{-- Usamos la clase Canton directamente --}}
                    @foreach(\App\Models\Canton::orderBy('nombre')->get(['id','nombre']) as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Parroquia (Vacío al inicio) --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Parroquia <span class="text-danger">*</span></label>
                {{-- ID "parroquia_select" es clave para el JS --}}
                <select name="parroquia_id" id="parroquia_select" class="form-select" required>
                    <option value="">— Selecciona Cantón —</option>
                </select>
            </div>

            {{-- Nombre --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre Comunidad <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre') }}" class="form-control" required maxlength="255" placeholder="Ej: San Francisco">
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>