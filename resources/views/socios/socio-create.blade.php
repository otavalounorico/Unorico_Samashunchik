<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Socio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('socios.store') }}">
    @csrf
    
    <div class="modal-body">
        
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código de socio se genera automáticamente.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Fila 1: Nombres y Apellidos --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                <input name="nombres" value="{{ old('nombres') }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Apellidos <span class="text-danger">*</span></label>
                <input name="apellidos" value="{{ old('apellidos') }}" class="form-control" required>
            </div>

            {{-- Fila 2: Cédula y Fecha Nac --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Cédula <span class="text-danger">*</span></label>
                <input name="cedula" value="{{ old('cedula') }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Fecha de nacimiento <span class="text-danger">*</span></label>
                <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" class="form-control" required>
            </div>

            {{-- Fila 3: Selects --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Género</label>
                <select name="genero_id" class="form-select">
                    <option value="">Seleccionar...</option>
                    @foreach($generos as $g)
                        <option value="{{ $g->id }}" @selected(old('genero_id')==$g->id)>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Estado Civil <span class="text-danger">*</span></label>
                <select name="estado_civil_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    @foreach($estados as $e)
                        <option value="{{ $e->id }}" @selected(old('estado_civil_id')==$e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Comunidad <span class="text-danger">*</span></label>
                <select name="comunidad_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    @foreach($comunidades as $c)
                        <option value="{{ $c->id }}" @selected(old('comunidad_id')==$c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fila 4: Contacto --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Dirección</label>
                <input name="direccion" value="{{ old('direccion') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Teléfono</label>
                <input name="telefono" value="{{ old('telefono') }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control">
            </div>

            <div class="col-12"><hr class="my-2 text-muted"></div>

            {{-- Fila 5: NUEVOS CAMPOS --}}
            <div class="col-md-3">
                <label class="form-label fw-bold text-primary">Fecha Inscripción <span class="text-danger">*</span></label>
                <input type="date" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', date('Y-m-d')) }}" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold text-primary">Beneficio Inicial <span class="text-danger">*</span></label>
                <select name="tipo_beneficio" class="form-select" required>
                    <option value="sin_subsidio">Sin Subsidio</option>
                    <option value="con_subsidio">Con Subsidio</option>
                    <option value="exonerado">Exonerado (Solo mayores de 75)</option>
                </select>
            </div>

            {{-- CAMPOS NUEVOS SOLICITADOS --}}
            <div class="col-md-3">
                <label class="form-label fw-bold text-dark">Condición <span class="text-danger">*</span></label>
                <select name="condicion" class="form-select" required>
                    <option value="ninguna">Ninguna</option>
                    <option value="discapacidad">Discapacidad</option>
                    <option value="enfermedad_terminal">Enfermedad Terminal</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold text-dark">Estatus <span class="text-danger">*</span></label>
                <select name="estatus" class="form-select" required>
                    <option value="vivo">Vivo</option>
                    <option value="fallecido">Fallecido</option>
                </select>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>