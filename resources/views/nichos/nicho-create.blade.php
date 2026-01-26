{{-- 1. CABECERA (Necesaria para cerrar el modal) --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Registro de Nicho</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- 2. FORMULARIO --}}
<form method="POST" action="{{ route('nichos.store') }}">
    @csrf
    <div class="modal-body">
        
        <div class="row g-3">
            {{-- Bloque --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Bloque <span class="text-danger">*</span></label>
                <select name="bloque_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($bloques as $b)
                        <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Socio --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Socio Titular</label>
                <select name="socio_id" class="form-select">
                    <option value="">-- Sin asignar --</option>
                    @foreach($socios as $s)
                        <option value="{{ $s->id }}">
                            {{-- Usamos ?? para evitar errores si falta el campo --}}
                            {{ $s->apellidos ?? $s->apellido ?? 'Socio' }} {{ $s->nombres ?? $s->nombre ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                <select name="tipo_nicho" class="form-select" required>
                    <option value="PROPIO">PROPIO</option>
                    <option value="COMPARTIDO">COMPARTIDO</option>
                </select>
            </div>

            {{-- Capacidad --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Capacidad <span class="text-danger">*</span></label>
                <input type="number" name="capacidad" min="1" value="1" class="form-control" required>
            </div>

            {{-- Estado --}}
            <div class="col-md-12">
                <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                <select name="estado" class="form-select" required>
                    <option value="disponible">Disponible</option>
                    <option value="ocupado">Ocupado</option>
                    <option value="mantenimiento">Mantenimiento</option>
                </select>
            </div>

            {{-- Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>