<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="alert alert-dark text-sm"><strong style="font-size:24px;">Nueva Parroquia</strong></div>

            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ route('parroquias.store') }}" class="card card-body">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Cantón</label>
                    <select name="canton_id" class="form-select" required>
                        <option value="">— Selecciona —</option>
                        @foreach($cantones as $c)
                            <option value="{{ $c->id }}" @selected(old('canton_id')==$c->id)>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input name="nombre" value="{{ old('nombre') }}" class="form-control" required maxlength="255">
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('parroquias.index') }}" class="btn btn-light">Cancelar</a>
                    <button class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>

        <x-app.footer />
    </main>
</x-app-layout>
