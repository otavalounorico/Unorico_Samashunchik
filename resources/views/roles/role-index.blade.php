<x-app-layout>
    {{-- 1. ESTILOS PERSONALIZADOS (TUS COLORES EXACTOS) --}}
    <style>
        /* Alerta Verde Pastel */
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
        .alert-success .btn-close:hover { opacity: 1; }
        
        /* Alerta Roja */
        .alert-danger {
            background-color: #fde1e1 !important;
            color: #cf304a !important;
            border-color: #fde1e1 !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        .alert-danger .btn-close {
            filter: none !important;
            opacity: 0.5;
            color: #cf304a;
        }

        /* Badges de permisos */
        .permission-badge {
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #f0f2f5;
            color: #6c757d;
            border: 1px solid #dee2e6;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            margin: 2px;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Roles</h3>
                    <p class="mb-0 text-secondary text-sm">Aquí puedes gestionar los roles y permisos del sistema.</p>
                </div>
                
                {{-- BOTÓN NUEVO ROL (ABRE MODAL FLOTANTE) --}}
                <button type="button" class="btn btn-success mb-0 px-4 shadow-sm" style="font-weight: 600;" 
                        data-bs-toggle="modal" data-bs-target="#createRoleModal">
                    <i class="fas fa-plus me-2"></i> Nuevo Rol
                </button>
            </div>

            {{-- 3. ALERTAS --}}
            @if (session('ok'))
                <div class="alert alert-success alert-dismissible fade show alert-temporal mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('ok') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show alert-temporal mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 4. TABLA DE ROLES --}}
            <div class="card shadow-sm border">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 15%;">Código</th>
                                    <th style="width: 25%;">Nombre del Rol</th>
                                    <th>Permisos Asignados</th>
                                    <th style="width: 15%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        {{-- CÓDIGO R00X --}}
                                        <td>
                                            <span class="badge bg-light text-dark border" style="font-size: 0.85rem;">
                                                {{ $role->codigo ?? '---' }}
                                            </span>
                                        </td>

                                        {{-- NOMBRE --}}
                                        <td class="fw-bold text-start ps-4" style="color: #344767;">
                                            {{ $role->name }}
                                        </td>

                                        {{-- PERMISOS --}}
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

                                        {{-- ACCIONES --}}
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- BOTÓN EDITAR (ABRE MODAL Y PASA DATOS) --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editRoleModal"
                                                        data-id="{{ $role->id }}"
                                                        data-name="{{ $role->name }}"
                                                        data-codigo="{{ $role->codigo }}"
                                                        title="Editar">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:.9rem;"></i>
                                                </button>

                                                {{-- BOTÓN ELIMINAR --}}
                                                @if($role->name !== 'Administrador')
                                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline js-delete-form">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger mb-0 js-delete" 
                                                                data-item="{{ $role->name }}" title="Eliminar">
                                                            <i class="fa-solid fa-trash" style="font-size:.9rem;"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-secondary mb-0" disabled title="Protegido">
                                                        <i class="fa-solid fa-lock" style="font-size:.9rem;"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                                                <p class="mb-0">No hay roles registrados.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginación --}}
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= VENTANAS FLOTANTES (MODALES) ================= --}}

        {{-- MODAL 1: CREAR NUEVO ROL --}}
        <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white fw-bold">
                            <i class="fas fa-plus-circle me-2"></i> Nuevo Rol
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Nombre del Rol</label>
                                <input type="text" name="name" class="form-control form-control-lg" placeholder="Ej: Supervisor de Ventas" required>
                            </div>
                            {{-- Nota Informativa --}}
                            <div class="alert alert-light border d-flex align-items-center mb-0 p-3">
                                <i class="fas fa-info-circle text-primary fs-4 me-3"></i>
                                <div>
                                    <small class="text-dark fw-bold d-block">Código Automático</small>
                                    <small class="text-muted">El sistema generará un código único (ej: <span class="badge bg-dark">R00X</span>) al guardar.</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success px-4">Guardar Rol</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL 2: EDITAR ROL --}}
        <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-pen-to-square me-2"></i> Editar Rol
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- El formulario se actualiza dinámicamente con JS --}}
                    <form id="editRoleForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            {{-- Campo Código (Solo lectura) --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Código</label>
                                <input type="text" id="editCodigo" class="form-control bg-light text-muted fw-bold" readonly>
                            </div>
                            {{-- Campo Nombre --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Nombre del Rol</label>
                                <input type="text" name="name" id="editName" class="form-control form-control-lg" required>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning px-4">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-app.footer />

        {{-- ================= SCRIPTS ================= --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                
                // 1. Auto-cerrar alertas (4 seg)
                setTimeout(function () {
                    document.querySelectorAll('.alert-temporal').forEach(alert => {
                        alert.style.transition = "opacity 0.5s";
                        alert.style.opacity = 0;
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 4000);

                // 2. Lógica del Modal Editar (Rellena los datos)
                var editRoleModal = document.getElementById('editRoleModal');
                editRoleModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget; // Botón que abrió el modal
                    
                    // Extraer datos
                    var id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');
                    var codigo = button.getAttribute('data-codigo');
                    
                    // Rellenar inputs
                    document.getElementById('editName').value = name;
                    document.getElementById('editCodigo').value = codigo ? codigo : '---';
                    
                    // Actualizar la ruta del formulario (Action)
                    var form = document.getElementById('editRoleForm');
                    var actionUrl = "{{ route('roles.update', 'ID_PLACEHOLDER') }}";
                    form.action = actionUrl.replace('ID_PLACEHOLDER', id);
                });

                // 3. SweetAlert para Eliminar
                document.addEventListener('click', function (e) {
                    const btn = e.target.closest('.js-delete');
                    if (!btn) return;
                    
                    const form = btn.closest('form');
                    const item = btn.getAttribute('data-item') || 'este registro';

                    Swal.fire({
                        title: '¿Eliminar rol?',
                        html: `¿Deseas eliminar el rol <b>"${item}"</b>?<br>Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        </script>
    </main>
</x-app-layout>