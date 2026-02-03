<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nueva Asignación</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('asignaciones.store') }}">
    @csrf
    <div class="modal-body">
        @if ($errors->any()) 
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div> 
        @endif

        <div class="row g-3">
            {{-- 1. NICHOS DISPONIBLES --}}
            <div class="col-12">
                <label class="form-label fw-bold">1. Nicho Disponible <span class="text-danger">*</span></label>
                <select name="nicho_id" class="form-select" required>
                    <option value="">-- Seleccionar Nicho --</option>
                    @foreach($nichosDisponibles as $n)
                        <option value="{{ $n->id }}">
                            {{ $n->codigo }} - Bloque {{ optional($n->bloque)->nombre ?? 'N/A' }} 
                            ({{ $n->fallecidos_count ?? 0 }}/3 Ocupados)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12"><hr class="my-1 text-muted"></div>

            {{-- 2. SOCIO RESPONSABLE --}}
            <div class="col-md-8">
                <label class="form-label fw-bold">2. Socio Responsable <span class="text-danger">*</span></label>
                <select name="socio_id" class="form-select" required>
                    <option value="">-- Buscar Socio --</option>
                    @foreach($socios as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->apellidos }} {{ $s->nombres }} ({{ $s->cedula }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Rol</label>
                <select name="rol" class="form-select">
                    <option value="TITULAR">TITULAR</option>
                    <option value="RESPONSABLE">RESPONSABLE</option>
                    <option value="CO-TITULAR">CO-TITULAR</option>
                </select>
            </div>

            <div class="col-12"><hr class="my-1 text-muted"></div>

            {{-- 3. FALLECIDOS --}}
            <div class="col-12">
                <label class="form-label fw-bold">3. Fallecido a Inhumar <span class="text-danger">*</span></label>
                <select name="fallecido_id" class="form-select" required>
                    <option value="">-- Buscar Fallecido --</option>
                    @foreach($fallecidos as $f)
                        <option value="{{ $f->id }}">
                            {{ $f->apellidos }} {{ $f->nombres }} ({{ $f->cedula }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>