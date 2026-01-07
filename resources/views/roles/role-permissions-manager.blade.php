<x-app-layout>
    {{-- 1. ESTILOS (Formato Parroquias) --}}
    <style>
        /* ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-weight: 400 !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        .alert-danger { background-color: #fde1e1 !important; color: #cf304a !important; border-color: #fde1e1 !important; font-weight: 400 !important; font-size: 14px !important; }
        .alert-danger .btn-close { filter: none !important; opacity: 0.5; color: #cf304a; }

        /* INPUTS Y BADGES */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 250px; } 
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Matriz de Permisos</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Roles: {{ $roles->count() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Gestiona masivamente los permisos de todos los roles.</p>
                </div>
            </div>

            {{-- 3. ALERTAS --}}
            @if(session('ok'))
                <div class="alert alert-success alert-dismissible fade show alert-temporal mb-3" role="alert" id="ok-msg">
                    <i class="fas fa-check-circle me-2"></i> {{ session('ok') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger text-white alert-dismissible fade show alert-temporal mb-3" role="alert" id="err-msg">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 4. BARRA DE HERRAMIENTAS (Buscador a la derecha) --}}
            <div class="d-flex justify-content-end align-items-center mb-4">
                <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter" style="min-width: 300px;">
                    <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                    <input type="text" id="perm-search" 
                           class="form-control border-0 ps-1 shadow-none" 
                           placeholder="Buscar permiso ...">
                </div>
            </div>

            {{-- 5. TABLA MATRIZ --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0">
                    <form id="matrix-form" action="{{ route('roles.permissions.manager.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-start ps-4" style="min-width: 280px;">Nombre del Permiso</th>

                                        @foreach($roles as $role)
                                            <th style="min-width: 160px;">
                                                <div class="d-flex flex-column align-items-center gap-1 py-2">
                                                    <span class="fw-semibold text-white">
                                                        {{ $role->name }}
                                                        @if($role->name === 'Administrador')
                                                            <i class="fa-solid fa-lock ms-1 text-white-50" title="Bloqueado"></i>
                                                        @endif
                                                    </span>

                                                    @if($role->name !== 'Administrador')
                                                        <div class="btn-group btn-group-sm mt-1" role="group">
                                                            <button class="btn btn-outline-light btn-xs col-check-all" data-role="{{ $role->id }}" type="button" style="font-size: 0.7rem; padding: 2px 5px;">Todas</button>
                                                            <button class="btn btn-outline-light btn-xs col-uncheck-all" data-role="{{ $role->id }}" type="button" style="font-size: 0.7rem; padding: 2px 5px;">Ninguna</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody id="perm-tbody">
                                    @foreach($permissions as $perm)
                                        <tr class="perm-row">
                                            <td class="text-start ps-4 perm-name fw-bold text-secondary" data-name="{{ strtolower($perm->name) }}">
                                                {{ $perm->name }}
                                            </td>

                                            @foreach($roles as $role)
                                                <td class="{{ $role->name === 'Administrador' ? 'bg-light' : '' }}">
                                                    <input type="checkbox"
                                                           class="form-check-input perm-cell"
                                                           style="cursor: pointer; width: 1.2em; height: 1.2em;"
                                                           name="permission_role[{{ $perm->id }}][{{ $role->id }}]"
                                                           @checked(in_array($perm->id, $rolePerms[$role->id] ?? []))
                                                           @disabled($role->name === 'Administrador')>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Footer con Botón Guardar --}}
                        <div class="card-footer bg-white border-top d-flex justify-content-end p-3">
                            <button class="btn btn-primary mb-0 px-4">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <x-app.footer />
    </main>

    <script>
        // 1. Foco rápido
        document.addEventListener('keydown', e => {
            if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
                e.preventDefault();
                document.getElementById('perm-search').focus();
            }
        });

        // 2. Buscador
        const search = document.getElementById('perm-search');
        search.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('#perm-tbody .perm-row').forEach(row => {
                const name = row.querySelector('.perm-name').dataset.name;
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });

        // 3. Seleccionar columnas
        document.querySelectorAll('.col-check-all').forEach(btn => {
            btn.addEventListener('click', function () {
                const roleId = this.getAttribute('data-role');
                document.querySelectorAll(`input[name^="permission_role["][name$="[${roleId}]"]`).forEach(cb => {
                    if (!cb.disabled) cb.checked = true;
                });
            });
        });
        document.querySelectorAll('.col-uncheck-all').forEach(btn => {
            btn.addEventListener('click', function () {
                const roleId = this.getAttribute('data-role');
                document.querySelectorAll(`input[name^="permission_role["][name$="[${roleId}]"]`).forEach(cb => {
                    if (!cb.disabled) cb.checked = false;
                });
            });
        });

        // 4. Alertas temporales
        setTimeout(function () {
            document.querySelectorAll('.alert-temporal').forEach(alert => {
                alert.style.transition = "opacity 0.5s";
                alert.style.opacity = 0;
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    </script>
</x-app-layout>