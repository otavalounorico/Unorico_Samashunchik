<x-app-layout>
    {{-- 1. ESTILOS (Formato Parroquias) --}}
    <style>
        /* ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-weight: 400 !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        .alert-danger { background-color: #fde1e1 !important; color: #cf304a !important; border-color: #fde1e1 !important; font-weight: 400 !important; font-size: 14px !important; }
        .alert-danger .btn-close { filter: none !important; opacity: 0.5; color: #cf304a; }

        /* INPUTS Y FILTROS COMPACTOS */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 250px; } 
        
        /* ESTILOS ESPECÍFICOS DE ROLES */
        .code-badge { font-size: 0.85rem; font-weight: 600; background-color: #f0f2f5; color: #344767; border: 1px solid #dee2e6; padding: 5px 10px; border-radius: 6px; display: inline-block; }
        .permission-badge { font-size: 0.75rem; font-weight: 500; background-color: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; padding: 3px 6px; border-radius: 4px; display: inline-block; margin: 1px; }
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
                    <p class="mb-0 text-secondary text-sm">Administra los roles y permisos del sistema.</p>
                </div>

                {{-- Botón Nuevo Rol (Carga AJAX) --}}
                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('roles.create') }}">
                    <i class="fas fa-plus me-2"></i> Nuevo Rol
                </button>
            </div>

            {{-- 3. ALERTAS --}}
            @if (session('ok')) {{-- Usas 'ok' en tu controlador según vi antes --}}
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

            {{-- 4. BARRA DE BÚSQUEDA (Derecha) --}}
            {{-- Como no hay reporte aquí, solo ponemos el buscador alineado a la derecha --}}
            <div class="d-flex justify-content-end mb-4">
                <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                    <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0 ps-1 shadow-none" 
                           placeholder="Buscar rol..." id="searchInput" 
                           value="{{ request('search') }}">
                </div>
            </div>

            {{-- 5. TABLA --}}
            <div class="card shadow-sm border">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="width: 15%;">Código</th>
                                    <th class="text-start ps-4" style="width: 25%;">Nombre del Rol</th>
                                    <th>Permisos Asignados</th>
                                    <th style="width: 15%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td class="fw-bold text-secondary">{{ $roles->firstItem() + $loop->index }}</td>

                                        <td>
                                            <span class="code-badge">{{ $role->codigo ?? '---' }}</span>
                                        </td>

                                        <td class="fw-bold text-start ps-4" style="color: #344767;">
                                            {{ $role->name }}
                                        </td>

                                        <td class="text-start ps-3">
                                            @if($role->permissions->isEmpty())
                                                <span class="text-muted fst-italic small">Sin permisos</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($role->permissions as $perm)
                                                        <span class="permission-badge">{{ $perm->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- Editar (Ajax Modal) --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0 open-modal" 
                                                        data-url="{{ route('roles.edit', $role->id) }}"
                                                        title="Editar">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:.8rem;"></i>
                                                </button>

                                                {{-- Eliminar (SweetAlert) --}}
                                                @if($role->name !== 'Administrador')
                                                    <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn"
                                                            data-url="{{ route('roles.destroy', $role->id) }}"
                                                            data-item="{{ $role->name }}">
                                                        <i class="fa-solid fa-trash" style="font-size:.8rem;"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-secondary mb-0" disabled title="Rol Protegido">
                                                        <i class="fa-solid fa-lock" style="font-size:.8rem;"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                                            <p class="mb-0">No hay roles registrados.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(method_exists($roles, 'links'))
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $roles->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- FORMULARIO OCULTO PARA ELIMINAR --}}
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO (VACÍO - Estilo Parroquias) --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    {{-- Aquí carga el contenido vía AJAX --}}
                </div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS UNIFICADOS --}}
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

                // 2. Buscador
                const searchInput = document.getElementById('searchInput'); 
                function applyFilters() { 
                    window.location.href = "{{ route('roles.index') }}?search=" + encodeURIComponent(searchInput.value); 
                }
                
                if(searchInput) {
                    searchInput.addEventListener('keypress', function (e) { 
                        if (e.key === 'Enter') { 
                            e.preventDefault(); 
                            applyFilters(); 
                        } 
                    });
                }

                // 3. Modal Dinámico (Carga Create y Edit)
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        // Limpiar y mostrar spinner
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="p-5 text-center">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2 text-secondary">Cargando...</p>
                            </div>`;
                        modal.show();
                        
                        // Fetch a la URL del botón
                        fetch(this.getAttribute('data-url'))
                            .then(r => {
                                if (!r.ok) throw new Error('Error al cargar');
                                return r.text();
                            })
                            .then(h => { 
                                modalEl.querySelector('.modal-content').innerHTML = h; 
                            })
                            .catch(e => {
                                console.error(e);
                                modalEl.querySelector('.modal-content').innerHTML = `
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Error</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">No se pudo cargar el formulario.</div>
                                `;
                            });
                    });
                });

                // 4. SweetAlert (Eliminar)
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: '¿Eliminar Rol?',
                            html: `¿Deseas eliminar el rol <b>"${this.getAttribute('data-item')}"</b>?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
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