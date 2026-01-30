<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Socio</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('socios.update', $socio) }}">
    @csrf @method('PUT')

    <div class="modal-body">
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Código --}}
            <div class="col-12">
                <label class="form-label fw-bold text-muted">Código</label>
                <input value="{{ $socio->codigo }}" class="form-control bg-light" readonly>
            </div>

            {{-- Fila 1 --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                <input name="nombres" value="{{ old('nombres', $socio->nombres) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Apellidos <span class="text-danger">*</span></label>
                <input name="apellidos" value="{{ old('apellidos', $socio->apellidos) }}" class="form-control" required>
            </div>

            {{-- Fila 2 --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Cédula <span class="text-danger">*</span></label>
                <input name="cedula" value="{{ old('cedula', $socio->cedula) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Fecha Nacimiento <span class="text-danger">*</span></label>
                <input type="date" name="fecha_nac" value="{{ old('fecha_nac', optional($socio->fecha_nac)->format('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Género</label>
                <select name="genero_id" class="form-select">
                    <option value="">—</option>
                    @foreach($generos as $g)
                        <option value="{{ $g->id }}" @selected(old('genero_id', $socio->genero_id)==$g->id)>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fila 3 --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Comunidad <span class="text-danger">*</span></label>
                <select name="comunidad_id" class="form-select" required>
                    <option value="">—</option>
                    @foreach($comunidades as $c)
                        <option value="{{ $c->id }}" @selected(old('comunidad_id', $socio->comunidad_id)==$c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Dirección</label>
                <input name="direccion" value="{{ old('direccion', $socio->direccion) }}" class="form-control">
            </div>

            {{-- Fila 4 --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Teléfono</label>
                <input name="telefono" value="{{ old('telefono', $socio->telefono) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" value="{{ old('email', $socio->email) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Estado Civil <span class="text-danger">*</span></label>
                <select name="estado_civil_id" class="form-select" required>
                    <option value="">—</option>
                    @foreach($estados as $e)
                        <option value="{{ $e->id }}" @selected(old('estado_civil_id', $socio->estado_civil_id)==$e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12"><hr class="my-2 text-muted"></div>

            {{-- NUEVOS CAMPOS (Edit) --}}
            <div class="col-md-3">
                <label class="form-label fw-bold text-primary">Fecha Inscripción <span class="text-danger">*</span></label>
                <input type="date" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', optional($socio->fecha_inscripcion)->format('Y-m-d')) }}" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold text-primary">Beneficio <span class="text-danger">*</span></label>
                <select name="tipo_beneficio" id="select_beneficio" class="form-select fw-bold border-primary" required>
                    <option value="sin_subsidio" @selected($socio->tipo_beneficio == 'sin_subsidio')>Sin Subsidio</option>
                    <option value="con_subsidio" @selected($socio->tipo_beneficio == 'con_subsidio')>Con Subsidio</option>
                    <option value="exonerado" @selected($socio->tipo_beneficio == 'exonerado') class="text-success font-weight-bold">Exonerado (75+)</option>
                </select>
            </div>

            {{-- CAMPOS AGREGADOS: CONDICIÓN Y ESTATUS --}}
            <div class="col-md-3">
                <label class="form-label fw-bold text-dark">Condición <span class="text-danger">*</span></label>
                <select name="condicion" class="form-select" required>
                    <option value="ninguna" @selected($socio->condicion == 'ninguna')>Ninguna</option>
                    <option value="discapacidad" @selected($socio->condicion == 'discapacidad')>Discapacidad</option>
                    <option value="enfermedad_terminal" @selected($socio->condicion == 'enfermedad_terminal')>Enfermedad Terminal</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold text-dark">Estatus <span class="text-danger">*</span></label>
                <select name="estatus" class="form-select" required>
                    <option value="vivo" @selected($socio->estatus == 'vivo')>Vivo</option>
                    <option value="fallecido" @selected($socio->estatus == 'fallecido')>Fallecido</option>
                </select>
            </div>

            {{-- Fecha Exoneración (Controlada por JS) --}}
            <div class="col-md-12 mt-2" id="div_fecha_exo" style="display: none;">
                <label class="form-label fw-bold text-success">Fecha Exoneración <span class="text-danger">*</span></label>
                <input type="date" name="fecha_exoneracion" 
                       value="{{ old('fecha_exoneracion', optional($socio->fecha_exoneracion)->format('Y-m-d')) }}" 
                       class="form-control border-success">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>