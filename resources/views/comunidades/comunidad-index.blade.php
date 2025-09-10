<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="alert alert-dark text-sm"><strong style="font-size:24px;">Gestión de Comunidades</strong></div>

            @if(session('success')) <div class="alert alert-success" id="ok-msg">{{ session('success') }}</div> @endif
            @if(session('error'))   <div class="alert alert-danger" id="err-msg">{{ session('error') }}</div> @endif

            <div class="d-flex gap-2 mb-3">
                <a href="{{ route('comunidades.create') }}" class="btn btn-success">
                    <i class="fa-solid fa-plus"></i> Nueva Comunidad
                </a>
                <form method="GET" class="d-flex gap-2 ms-auto" style="max-width: 640px;">
                    <input name="parroquia_id" value="{{ request('parroquia_id') }}" class="form-control" placeholder="ID Parroquia...">
                    <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar nombre...">
                    <button class="btn btn-outline-secondary">Filtrar</button>
                    <a href="{{ route('comunidades.index') }}" class="btn btn-link">Limpiar</a>
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
                                    <th>Parroquia</th>
                                    <th>Cantón</th>
                                    <th style="width:180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comunidades as $com)
                                    <tr>
                                        <td>{{ $com->id }}</td>
                                        <td class="fw-semibold"><a href="{{ route('comunidades.show',$com) }}">{{ $com->nombre }}</a></td>
                                        <td>{{ $com->parroquia->nombre }}</td>
                                        <td>{{ $com->parroquia->canton->nombre }}</td>
                                        <td>
                                            <a href="{{ route('comunidades.edit',$com) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('comunidades.destroy',$com) }}" method="POST"
                                                  class="d-inline js-delete-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger js-delete"
                                                        data-item="{{ $com->nombre }}" title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted py-4">No hay comunidades.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $comunidades->links() }}</div>
                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            ['ok-msg','err-msg'].forEach(id=>{ const el=document.getElementById(id); if(!el) return;
                setTimeout(()=>{ el.style.transition="opacity .5s"; el.style.opacity=0; setTimeout(()=>el.remove(),500); }, 5000);
            });
        });
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-delete'); if (!btn) return;
            const form = btn.closest('form'); const item = btn.getAttribute('data-item') || 'este registro';
            Swal.fire({
                title: '¿Eliminar comunidad?', html:`¿Eliminar <b>"${item}"</b>?`,
                icon:'warning', showCancelButton:true, reverseButtons:true,
                confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar',
                confirmButtonColor:'#d33', cancelButtonColor:'#3085d6', focusCancel:true,
            }).then(r=>{ if(r.isConfirmed) form.submit(); });
        });
    </script>
</x-app-layout>
