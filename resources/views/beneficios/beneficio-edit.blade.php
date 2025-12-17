{{-- CABECERA DEL MODAL (Estilo Advertencia) --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Beneficio</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('beneficios.update', $beneficio) }}">
    @csrf @method('PUT')
    
    {{-- CUERPO DEL MODAL --}}
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
            {{-- Código (Editable pero con cuidado) --}}
            <div class="col-12">
                <label class="form-label fw-bold">Código <span class="text-danger">*</span></label>
                <input name="codigo" value="{{ old('codigo', $beneficio->codigo) }}" class="form-control bg-light" required>
                <small class="text-muted text-xs">Identificador único del sistema.</small>
            </div>
            
            {{-- Nombre --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre del Beneficio <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre', $beneficio->nombre) }}" class="form-control" required>
            </div>

            {{-- Fila 2: Tipo y Valor --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                <input name="tipo" value="{{ old('tipo', $beneficio->tipo) }}" class="form-control" maxlength="10" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold">Valor ($)</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" min="0" name="valor" value="{{ old('valor', $beneficio->valor) }}" class="form-control">
                </div>
            </div>

            {{-- Fila 3: Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $beneficio->descripcion) }}</textarea>
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>