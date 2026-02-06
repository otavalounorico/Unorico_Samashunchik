<x-app-layout>
    {{-- 1. ESTILOS ADAPTADOS --}}
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
        .btn-action { margin-right: 4px; }

        /* BADGES DE ESTADO USUARIO */
        .badge-activo { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .badge-inactivo { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Administración de Usuarios</h3>
                    <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                        Total: {{ $users->total() }}
                    </span>
                </div>
                <p class="mb-0 text-secondary text-sm">Aquí puedes gestionar los reportes y permisos de usuarios.</p>
            </div>

            {{-- 3. ALERTAS --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show alert-temporal mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger text-white alert-dismissible fade show alert-temporal mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('users.generateReports') }}" method="POST" id="reportForm">
                @csrf

                {{-- 4. BOTONES DE REPORTE Y BUSCADOR COMPACTO --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto" 
                                style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" 
                                type="button" id="dropdownGenerate" data-bs-toggle="dropdown" aria-expanded="false">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownGenerate">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" 
                                   placeholder="Buscar usuario..." id="searchInput" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                {{-- 5. TABLA ESTILO ASIGNACIONES --}}
                <div class="card shadow-sm border">
                    <div class="card-body p-0"> {{-- Cambiado a p-0 para que la tabla toque los bordes --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th class="opacity-10" style="width: 50px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;"></th>
                                        <th class="opacity-10">#</th>
                                        <th class="opacity-10">Código</th>
                                        <th class="opacity-10 text-start ps-3">Nombre</th>
                                        <th class="opacity-10">Email</th>
                                        <th class="opacity-10">Ubicación</th>
                                        <th class="opacity-10">Rol</th>
                                        <th class="opacity-10">Estado</th>
                                        <th class="opacity-10" style="width:120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td><input type="checkbox" name="users[]" value="{{ $user->id }}" style="cursor: pointer;"></td>
                                            
                                            <td class="text-sm fw-bold text-secondary">
                                                {{ $users->firstItem() + $loop->index }}
                                            </td>

                                            <td class="fw-bold text-dark">{{ $user->codigo_usuario }}</td>
                                            
                                            <td class="text-start ps-3">
                                                <div class="d-flex flex-column">
                                                    <span class="text-sm font-weight-bold">{{ $user->name }}</span>
                                                    <span class="text-xs text-secondary">{{ $user->phone ?? 'Sin teléfono' }}</span>
                                                </div>
                                            </td>
                                            
                                            <td class="text-sm">{{ $user->email }}</td>
                                            
                                            <td class="text-sm">{{ $user->location ?? 'N/A' }}</td>

                                            <td>
                                                <span class="badge border" style="background-color: #f8f9fa; color: #343a40; font-size: 0.75rem; font-weight: 700;">
                                                    {{ $user->getRoleNames()->first() ?? 'Sin Rol' }}
                                                </span>
                                            </td>

                                            <td>
                                                @if($user->status)
                                                    <span class="badge badge-activo">Activo</span>
                                                @else
                                                    <span class="badge badge-inactivo">Inactivo</span>
                                                @endif
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-warning mb-0 btn-action" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editUserModal"
                                                            data-id="{{ $user->id }}"
                                                            title="Editar Usuario">
                                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center py-4 text-muted">No se encontraron usuarios.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if(method_exists($users, 'links'))
                            <div class="mt-3 px-3 d-flex justify-content-end">
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content" id="modal-content-wrapper">
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-app.footer />

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                
                // 1. Ocultar alertas automáticas
                setTimeout(function () {
                    document.querySelectorAll('.alert-temporal').forEach(alert => {
                        alert.style.transition = "opacity 0.5s";
                        alert.style.opacity = 0;
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 3000);

                // 2. Buscador (Enter para filtrar)
                const searchInput = document.getElementById('searchInput');
                if(searchInput){
                    searchInput.addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault(); 
                            const searchTerm = this.value;
                            window.location.href = "{{ route('users-management') }}?search=" + encodeURIComponent(searchTerm);
                        }
                    });
                }

                // 3. Lógica del Modal FETCH
                var editUserModal = document.getElementById('editUserModal');
                editUserModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var userId = button.getAttribute('data-id');
                    var modalContent = document.getElementById('modal-content-wrapper');

                    var url = "/user/" + userId + "/edit"; 

                    modalContent.innerHTML = `
                        <div class="modal-body text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-secondary">Cargando formulario...</p>
                        </div>
                    `;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Error al cargar');
                            return response.text();
                        })
                        .then(html => {
                            modalContent.innerHTML = html;
                        })
                        .catch(error => {
                            modalContent.innerHTML = `
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Error</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">No se pudo cargar la información del usuario.</div>
                            `;
                        });
                });
            });

            function toggleSelectAll() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('input[name="users[]"]');
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            }
        </script>
    </main>
</x-app-layout>