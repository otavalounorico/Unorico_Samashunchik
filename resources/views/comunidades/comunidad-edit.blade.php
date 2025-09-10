<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="alert alert-dark text-sm"><strong style="font-size:24px;">Editar Comunidad</strong></div>

            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ route('comunidades.update',$comunidad) }}" class="card card-body">
                @csrf @method('PUT')

                @php $cantonActual = $comunidad->parroquia->canton_id; @endphp

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cantón</label>
                        <select id="canton_select" class="form-select">
                            @foreach(\App\Models\Canton::orderBy('nombre')->get(['id','nombre']) as $c)
                                <option value="{{ $c->id }}" @selected($c->id == $cantonActual)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parroquia</label>
                        <select name="parroquia_id" id="parroquia_select" class="form-select" required>
                            <option value="">Cargando...</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input name="nombre" value="{{ old('nombre',$comunidad->nombre) }}" class="form-control" required maxlength="255">
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('comunidades.index') }}" class="btn btn-light">Cancelar</a>
                    <button class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>

        <x-app.footer />
    </main>

    <script>
        async function cargarParroquias(cantonId, parroquiaIdSeleccionada = null){
            const parroquiaSelect = document.getElementById('parroquia_select');
            parroquiaSelect.innerHTML = '<option value="">Cargando...</option>';
            const res = await fetch(`/cantones/${cantonId}/parroquias`);
            const data = await res.json();
            parroquiaSelect.innerHTML = '<option value="">— Selecciona —</option>';
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id; opt.textContent = p.nombre;
                if (parroquiaIdSeleccionada && Number(parroquiaIdSeleccionada) === Number(p.id)) opt.selected = true;
                parroquiaSelect.appendChild(opt);
            });
        }
        document.addEventListener('DOMContentLoaded', ()=>{
            const cantonSelect = document.getElementById('canton_select');
            const parroquiaActual = {{ $comunidad->parroquia_id }};
            cargarParroquias(cantonSelect.value, parroquiaActual);
            cantonSelect.addEventListener('change', (e)=> cargarParroquias(e.target.value));
        });
    </script>
</x-app-layout>
