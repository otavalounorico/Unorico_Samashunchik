<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Gestión de Cantones</strong>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="ok-msg">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="err-msg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="d-flex gap-2 mb-3">
                        <a href="{{ route('cantones.create') }}" class="btn btn-success">
                            <i class="fa-solid fa-plus"></i> Nuevo Cantón
                        </a>
                        <form method="GET" class="d-flex gap-2 ms-auto" style="max-width: 420px;">
                            <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar nombre...">
                            <button class="btn btn-outline-secondary">Buscar</button>
                            <a href="{{ route('cantones.index') }}" class="btn btn-link">Limpiar</a>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width:90px;">ID</th>
                                            <th>Nombre</th>
                                            <th style="width:180px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cantones as $canton)
                                            <tr>
                                                <td>{{ $canton->id }}</td>
                                                <td class="fw-semibold">
                                                    <a href="{{ route('cantones.show',$canton) }}">{{ $canton->nombre }}</a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('cantones.edit',$canton) }}"
                                                       class="btn btn-sm btn-warning" title="Editar">
                                                       <i class="fa-solid fa-pen-to-square" style="font-size:.9rem;"></i>
                                                    </a>
                                                    <form action="{{ route('cantones.destroy',$canton) }}" method="POST"
                                                          class="d-inline js-delete-form">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger js-delete"
                                                                data-item="{{ $canton->nombre }}" title="Eliminar">
                                                            <i class="fa-solid fa-trash" style="font-size:.9rem;"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-muted py-4">No hay cantones.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $cantones->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    {{-- mensajes flash + confirmación --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            ['ok-msg','err-msg'].forEach(id=>{
                const el=document.getElementById(id); if(!el) return;
                setTimeout(()=>{ el.style.transition="opacity .5s"; el.style.opacity=0; setTimeout(()=>el.remove(),500); }, 5000);
            });
        });
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-delete'); if (!btn) return;
            const form = btn.closest('form'); const item = btn.getAttribute('data-item') || 'este registro';
            Swal.fire({
                title: '¿Eliminar cantón?',
                html: `¿Está seguro de eliminar <b>"${item}"</b>?`,
                icon: 'warning', showCancelButton: true, reverseButtons: true,
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', focusCancel: true,
            }).then((r)=>{ if(r.isConfirmed) form.submit(); });
        });
    </script>
</x-app-layout>
