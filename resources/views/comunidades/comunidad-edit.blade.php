{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Comunidad</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('comunidades.update', $comunidad->id) }}">
    @csrf @method('PUT')
    
    <div class="modal-body">
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Calculamos el cantón actual --}}
        @php $cantonActual = $comunidad->parroquia->canton_id; @endphp

        <div class="row g-3">
            {{-- Código --}}
            <div class="col-12">
                <label class="form-label fw-bold text-muted">Código</label>
                <input value="{{ $comunidad->codigo_unico ?? $comunidad->codigo }}" class="form-control bg-light" readonly>
            </div>

            {{-- Cantón --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Cantón <span class="text-danger">*</span></label>
                <select id="canton_select" class="form-select" required>
                    @foreach(\App\Models\Canton::orderBy('nombre')->get(['id','nombre']) as $c)
                        <option value="{{ $c->id }}" @selected($c->id == $cantonActual)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Parroquia --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Parroquia <span class="text-danger">*</span></label>
                <select name="parroquia_id" id="parroquia_select" class="form-select" required>
                    {{-- Cargamos las parroquias del cantón actual con PHP para que ya aparezcan listas --}}
                    @foreach(\App\Models\Parroquia::where('canton_id', $cantonActual)->orderBy('nombre')->get() as $p)
                        <option value="{{ $p->id }}" @selected($p->id == $comunidad->parroquia_id)>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Nombre --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre Comunidad <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre', $comunidad->nombre) }}" class="form-control" required maxlength="255">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>