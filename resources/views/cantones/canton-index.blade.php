<x-app-layout>
    {{-- 1. ESTILOS (Idénticos al Index de Usuarios) --}}
    <style>
        /* ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        .alert-danger { background-color: #fde1e1 !important; color: #cf304a !important; border-color: #fde1e1 !important; font-size: 14px !important; }
        .alert-danger .btn-close { filter: none !important; opacity: 0.5; color: #cf304a; }

        /* BUSCADOR Y FILTROS */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 180px; } 

        /* ESTILOS DE TABLA (Formato Usuarios/Asignaciones) */
        .table thead th {
            font-size: 14px !important;    
            text-transform: uppercase;    
            letter-spacing: 0.05rem;      
            font-weight: 700 !important;  
            padding-top: 15px !important; 
            padding-bottom: 15px !important; 
        }
        .btn-action { margin-right: 4px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Cantones</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $cantones->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administra el catálogo geográfico de cantones.</p>
                </div>

                {{-- Botón Nuevo Cantón --}}
                @can('crear canton')
                <button type="button" class="btn btn-success px-4 open-modal" style="height: fit-content;"
                    data-url="{{ route('cantones.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Cantón
                </button>
                @endcan
            </div>

            {{-- 3. ALERTAS (MODIFICADO PARA DETECTAR ERRORES DE VALIDACIÓN) --}}
            <div class="mb-3">
                
                {{-- A. Éxito --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show alert-temporal" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- B. Errores Generales (Try/Catch) --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- C. Errores de Validación (AQUÍ SALDRÁ "El nombre ya está registrado") --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>No se pudo guardar:</strong>
                                <ul class="mb-0 ps-3" style="list-style-type: none; padding-left: 0;">
                                    @foreach ($errors->all() as $error)
                                        <li>- {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

            </div>

            {{-- 4. BUSCADOR --}}
            <div class="d-flex justify-content-end mb-4">
                <form method="GET" action="{{ route('cantones.index') }}">
                    <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter shadow-sm">
                        <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control border-0 ps-1 shadow-none"
                            placeholder="Buscar...">
                    </div>
                </form>
            </div>

            {{-- 5. TABLA --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0 pb-2"> 
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10" style="width: 40px;">
                                        @if(auth()->user()->can('eliminar canton') || auth()->user()->can('reportar canton'))
                                            <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;">
                                        @endif
                                    </th>
                                    <th class="opacity-10" style="width: 50px;">#</th>
                                    <th class="opacity-10" style="width: 20%;">Código</th>
                                    <th class="opacity-10 text-start ps-4">Nombre</th>
                                    <th class="opacity-10" style="width:180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cantones as $canton)
                                    <tr>
                                        <td>
                                            @if(auth()->user()->can('eliminar canton') || auth()->user()->can('reportar canton'))
                                                <input type="checkbox" name="ids[]" value="{{ $canton->id }}" class="check-item" style="cursor: pointer;">
                                            @endif
                                        </td>
                                        
                                        <td class="text-sm fw-bold text-secondary">
                                            {{ $cantones->firstItem() + $loop->index }}
                                        </td>
                                        
                                        <td class="fw-bold text-dark">{{ $canton->codigo ?? 'N/A' }}</td>
                                        
                                        <td class="text-start ps-4">
                                            <span class="text-sm font-weight-bold">{{ $canton->nombre }}</span>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                @can('ver canton')
                                                <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal"
                                                    data-url="{{ route('cantones.show', $canton->id) }}" title="Ver">
                                                    <i class="fa-solid fa-eye text-white" style="font-size: 0.7rem;"></i>
                                                </button>
                                                @endcan

                                                @can('editar canton')
                                                <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal"
                                                    data-url="{{ route('cantones.edit', $canton->id) }}" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                </button>
                                                @endcan

                                                @can('eliminar canton')
                                                <button type="button" class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                    data-url="{{ route('cantones.destroy', $canton) }}"
                                                    data-item="{{ $canton->nombre }}" title="Eliminar">
                                                    <i class="fa-solid fa-trash" style="font-size: 0.7rem;"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            No se encontraron cantones registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    @if($cantones->hasPages())
                        <div class="mt-3 px-3 d-flex justify-content-end">
                            {{ $cantones->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"></div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Alertas (incluye la nueva de validación)
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 4000); // Subí a 4 segundos para que dé tiempo a leer el error

                // Modal
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(this.getAttribute('data-url')).then(r => r.text()).then(h => { modalEl.querySelector('.modal-content').innerHTML = h; });
                    });
                });

                // Delete
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        Swal.fire({
                            title: '¿Eliminar Cantón?',
                            html: `¿Deseas eliminar el cantón <b>"${this.getAttribute('data-item')}"</b>?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33', 
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((r) => {
                            if (r.isConfirmed) {
                                const f = document.getElementById('deleteForm');
                                f.action = this.getAttribute('data-url');
                                f.submit();
                            }
                        });
                    });
                });
            });

            function toggleSelectAll() { 
                const selectAll = document.getElementById('selectAll');
                if(selectAll){
                    const c = selectAll.checked; 
                    document.querySelectorAll('.check-item').forEach(x => x.checked = c); 
                }
            }
        </script>
    </main>
</x-app-layout>