<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />
        <div class="px-5 py-4 container-fluid">
            <div class="alert alert-dark text-sm"><strong style="font-size:24px;">Cantón: {{ $canton->nombre }}</strong></div>

            <div class="card mb-3">
                <div class="card-body">
                    <div><b>ID:</b> {{ $canton->id }}</div>
                    <div><b>Nombre:</b> {{ $canton->nombre }}</div>
                </div>
            </div>

            <h5>Parroquias</h5>
            <div class="card">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($canton->parroquias as $p)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $p->nombre }}</span>
                                <a href="{{ route('parroquias.edit',$p) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Sin parroquias</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <x-app.footer />
    </main>
</x-app-layout>
