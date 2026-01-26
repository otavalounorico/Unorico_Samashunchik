{{-- 1. CABECERA DEL MODAL (Agregada para que tenga título y botón de cerrar) --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Registro de Nicho</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- 2. FORMULARIO --}}
<form method="POST" action="{{ route('nichos.update', $nicho) }}">
    @csrf @method('PUT')
    
    <div class="modal-body">
        
        <div class="row g-3">
            {{-- Código --}}
            <div class="col-md-12">
                <label class="form-label fw-bold">Código</label>
                <input name="codigo" value="{{ old('codigo', $nicho->codigo) }}" class="form-control" required>
            </div>

            {{-- Bloque --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Bloque <span class="text-danger">*</span></label>
                <select name="bloque_id" class="form-select" required>
                    @foreach($bloques as $b)
                        <option value="{{ $b->id }}" @selected(old('bloque_id', $nicho->bloque_id)==$b->id)>{{ $b->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Socio (CON PROTECCIÓN DE ERRORES) --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Socio Titular</label>
                <select name="socio_id" class="form-select">
                    <option value="">-- Sin asignar --</option>
                    @foreach($socios as $s)
                        <option value="{{ $s->id }}" @selected(old('socio_id', $nicho->socio_id)==$s->id)>
                             {{-- Usamos ?? para que no falle si cambia el nombre de la columna --}}
                             {{ $s->apellidos ?? $s->apellido ?? '' }} {{ $s->nombres ?? $s->nombre ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                <select name="tipo_nicho" class="form-select" required>
                    <option value="PROPIO" @selected(old('tipo_nicho', $nicho->tipo_nicho) == 'PROPIO')>PROPIO</option>
                    <option value="COMPARTIDO" @selected(old('tipo_nicho', $nicho->tipo_nicho) == 'COMPARTIDO')>COMPARTIDO</option>
                </select>
            </div>

            {{-- Capacidad --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Capacidad <span class="text-danger">*</span></label>
                <input type="number" min="1" name="capacidad" value="{{ old('capacidad',$nicho->capacidad) }}" class="form-control" required>
            </div>
            
            {{-- Estado --}}
            <div class="col-md-12">
                <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                <select name="estado" class="form-select" required>
                    @foreach(['disponible','ocupado','mantenimiento'] as $e)
                        <option value="{{ $e }}" @selected(old('estado',$nicho->estado)==$e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $nicho->descripcion) }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>