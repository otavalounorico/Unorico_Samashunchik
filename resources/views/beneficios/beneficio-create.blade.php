{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Registro de Beneficio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('beneficios.store') }}">
    @csrf
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Mensaje informativo --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código (Ej: BEN001) se genera automáticamente.
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
            {{-- Fila 1: Nombre --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre del Beneficio <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre') }}" class="form-control" required placeholder="Ej. Mantenimiento Anual">
            </div>

            {{-- Fila 2: Tipo y Valor --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                <input name="tipo" value="{{ old('tipo') }}" class="form-control" maxlength="10" required placeholder="Ej. FEE, DESC">
                <small class="text-muted text-xs">Código corto (Máx 10 chars)</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold">Valor ($)</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" min="0" name="valor" value="{{ old('valor') }}" class="form-control" placeholder="0.00">
                </div>
            </div>

            {{-- Fila 3: Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>