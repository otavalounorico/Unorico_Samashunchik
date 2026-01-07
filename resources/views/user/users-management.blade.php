<x-app-layout>
    <style>
        /* 1. ESTILOS DE ALERTAS (Originales) */
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border-color: #e4f4db !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        .alert-success .btn-close {
            filter: none !important; 
            opacity: 0.5;
            color: #708736;
        }
        .alert-success .btn-close:hover {
            opacity: 1;
        }
        .alert-danger {
            background-color: #fde1e1 !important; 
            color: #cf304a !important; 
            border-color: #fde1e1 !important; 
            font-weight: 400 !important; 
            font-size: 14px !important; 
        }
        .alert-danger .btn-close { filter: none !important; opacity: 0.5; color: #cf304a; }

        /* 2. ESTILOS DEL BUSCADOR (Formato Parroquias) */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        /* Esta clase controla el tamaño compacto */
        .compact-filter { width: auto; min-width: 140px; max-width: 250px; } 
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Administración de Usuarios</h3>
                    <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                        Total: {{ $users->total() }}
                    </span>
                </div>
                <p class="mb-0 text-secondary text-sm">Aquí puedes gestionar los reportes de usuarios.</p>
            </div>

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

                {{-- AQUÍ ESTÁ EL CAMBIO: FORMATO FLEX DE PARROQUIAS --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- Lado Izquierdo: Botón Reporte (Igual que antes pero con w-100 responsive) --}}
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

                    {{-- Lado Derecho: Buscador Compacto (Formato Parroquias) --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" 
                                   placeholder="Buscar usuario..." id="searchInput" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;"></th>
                                        <th style="width: 50px;">#</th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Ubicación</th>
                                        <th>Roles</th>
                                        <th>Estado</th>
                                        <th style="width:120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td><input type="checkbox" name="users[]" value="{{ $user->id }}" style="cursor: pointer;"></td>
                                            
                                            <td class="fw-bold text-secondary">
                                                {{ $users->firstItem() + $loop->index }}
                                            </td>

                                            <td class="fw-bold">{{ $user->codigo_usuario }}</td>
                                            <td class="text-start ps-3">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone ?? 'N/A' }}</td>
                                            <td>{{ $user->location ?? 'N/A' }}</td>

                                            <td>
                                                <span class="badge border" style="background-color: #e9ecef; color: #343a40; font-size: 0.85rem; font-weight: 600;">
                                                    {{ $user->getRoleNames()->first() }}
                                                </span>
                                            </td>

                                            <td>
                                                @if($user->status)
                                                    <span class="badge" style="background-color: #19cf2bff; color: white; font-size: 0.85rem;">Activo</span>
                                                @else
                                                    <span class="badge" style="background-color: #ef1b30ff; color: white; font-size: 0.85rem;">Inactivo</span>
                                                @endif
                                            </td>
                                            
                                            <td>
                                                {{-- BOTÓN EDITAR (Tu código original) --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editUserModal"
                                                        data-id="{{ $user->id }}"
                                                        title="Editar">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:.8rem;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="10" class="text-center py-4 text-muted">No se encontraron usuarios.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if(method_exists($users, 'links'))
                            <div class="mt-3 d-flex justify-content-end">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- MODAL VACÍO (Tu código original) --}}
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content" id="modal-content-wrapper">
                    {{-- Aquí se cargará la vista externa --}}
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
                
                // 1. Ocultar alertas
                setTimeout(function () {
                    document.querySelectorAll('.alert-temporal').forEach(alert => {
                        alert.style.transition = "opacity 0.5s";
                        alert.style.opacity = 0;
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 3000);

                // 2. Buscador
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

                // 3. Lógica del Modal (AJAX / FETCH)
                var editUserModal = document.getElementById('editUserModal');
                
                editUserModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var userId = button.getAttribute('data-id');
                    var modalContent = document.getElementById('modal-content-wrapper');

                    // URL: Ajusta '/users/' si tu ruta prefix es diferente
                    var url = "/user/" + userId + "/edit"; 

                    // Mostrar cargando mientras llega la respuesta
                    modalContent.innerHTML = `
                        <div class="modal-body text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-secondary">Cargando formulario...</p>
                        </div>
                    `;

                    // Petición al servidor
                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Error al cargar');
                            return response.text();
                        })
                        .then(html => {
                            // Pegar el HTML que devuelve el controlador
                            modalContent.innerHTML = html;
                        })
                        .catch(error => {
                            console.error(error);
                            modalContent.innerHTML = `
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Error</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">No se pudo cargar la información.</div>
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