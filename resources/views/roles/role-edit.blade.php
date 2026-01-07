{{-- CABECERA (Amarilla para Editar) --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Rol</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
{{-- Nota: Usamos $role->id en la ruta --}}
<form method="POST" action="{{ route('roles.update', $role->id) }}">
    @csrf 
    @method('PUT')

    <div class="modal-body">
        
        {{-- Errores --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Código (Solo lectura) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-muted">Código</label>
                {{-- Mostramos el código si existe, sino guiones --}}
                <input value="{{ $role->codigo ?? '---' }}" class="form-control bg-light" readonly>
            </div>

            {{-- Nombre del Rol --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre del Rol <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control" required>
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>