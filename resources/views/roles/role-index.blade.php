<x-app-layout>
    {{-- 1. ESTILOS ADAPTADOS (Formato Asignaciones) --}}
    <style>
        /* ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        .alert-danger { background-color: #fde1e1 !important; color: #cf304a !important; border-color: #fde1e1 !important; font-size: 14px !important; }
        .alert-danger .btn-close { filter: none !important; opacity: 0.5; color: #cf304a; }

        /* INPUTS Y FILTROS COMPACTOS */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 250px; } 
        
        /* ESTILOS DE TABLA (Formato Asignaciones) */
        .table thead th {
            font-size: 14px !important;    
            text-transform: uppercase;    
            letter-spacing: 0.05rem;      
            font-weight: 700 !important;  
            padding-top: 15px !important; 
            padding-bottom: 15px !important; 
        }

        /* BADGES ESPECÍFICOS */
        .code-badge { font-size: 0.75rem; font-weight: 700; background-color: #f0f2f5; color: #1c2a48; border: 1px solid #dee2e6; padding: 4px 8px; border-radius: 4px; }
        .permission-badge { font-size: 0.7rem; font-weight: 600; background-color: #e9ecef; color: #495057; border: 1px solid #dee2e6; padding: 2px 6px; border-radius: 4px; display: inline-block; margin: 1px; }
        .btn-action { margin-right: 4px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Roles</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $roles->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administra los niveles de acceso y permisos del sistema.</p>
                </div>

                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('roles.create') }}">
                    <i class="fas fa-plus me-2"></i> Nuevo Rol
                </button>
            </div>

            {{-- 3. ALERTAS --}}
            @if (session('ok'))
                <div class="alert alert-success alert-dismissible fade show alert-temporal mb-3">
                    <i class="fas fa-check-circle me-2"></i> {{ session('ok') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show alert-temporal mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 4. BARRA DE BÚSQUEDA --}}
            <div class="d-flex justify-content-end mb-4">
                <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                    <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0 ps-1 shadow-none" 
                           placeholder="Buscar rol..." id="searchInput" 
                           value="{{ request('search') }}">
                </div>
            </div>

            {{-- 5. TABLA ESTILO ASIGNACIONES (Sin bordes innecesarios, header oscuro) --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10" style="width: 60px;">#</th>
                                    <th class="opacity-10" style="width: 15%;">Código</th>
                                    <th class="opacity-10 text-start ps-4">Nombre del Rol</th>
                                    <th class="opacity-10 text-start ps-4">Permisos Asignados</th>
                                    <th class="opacity-10" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td class="text-sm fw-bold text-secondary">{{ $roles->firstItem() + $loop->index }}</td>

                                        <td>
                                            <span class="code-badge">{{ $role->codigo ?? 'N/A' }}</span>
                                        </td>

                                        <td class="text-start ps-4">
                                            <span class="text-sm font-weight-bold text-dark">{{ $role->name }}</span>
                                        </td>

                                        <td class="text-start ps-4">
                                            @if($role->permissions->isEmpty())
                                                <span class="text-muted small fst-italic">-- Sin permisos asignados --</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($role->permissions as $perm)
                                                        <span class="permission-badge">{{ $perm->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal" 
                                                        data-url="{{ route('roles.edit', $role->id) }}"
                                                        title="Editar Rol">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                </button>

                                                @if($role->name !== 'Administrador')
                                                    <button type="button" class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                            data-url="{{ route('roles.destroy', $role->id) }}"
                                                            data-item="{{ $role->name }}"
                                                            title="Eliminar Rol">
                                                        <i class="fa-solid fa-trash" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-secondary mb-0 btn-action" disabled title="Rol Protegido">
                                                        <i class="fa-solid fa-lock" style="font-size:0.7rem;"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            No se encontraron roles registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(method_exists($roles, 'links'))
                        <div class="mt-3 px-3 d-flex justify-content-end">
                            {{ $roles->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content"></div>
            </div>
        </div>

        <x-app.footer />

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                
                // 1. Alertas temporales
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; 
                        alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3000);

                // 2. Buscador con Enter
                const searchInput = document.getElementById('searchInput'); 
                if(searchInput) {
                    searchInput.addEventListener('keypress', function (e) { 
                        if (e.key === 'Enter') { 
                            e.preventDefault(); 
                            window.location.href = "{{ route('roles.index') }}?search=" + encodeURIComponent(this.value); 
                        } 
                    });
                }

                // 3. Modal Dinámico
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="p-5 text-center">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2 text-secondary text-sm">Cargando...</p>
                            </div>`;
                        modal.show();
                        
                        fetch(this.getAttribute('data-url'))
                            .then(r => r.text())
                            .then(h => { modalEl.querySelector('.modal-content').innerHTML = h; })
                            .catch(e => {
                                modalEl.querySelector('.modal-content').innerHTML = `<div class="p-4 text-center text-danger">Error al cargar.</div>`;
                            });
                    });
                });

                // 4. SweetAlert Eliminar
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: '¿Eliminar Rol?',
                            html: `¿Estás seguro de eliminar el rol <b>"${this.getAttribute('data-item')}"</b>?`,
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
        </script>
    </main>
</x-app-layout>