<x-app-layout>
    {{-- ESTILOS PRESERVADOS DE TU REFERENCIA --}}
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
        .alert-success .btn-close:hover {
            opacity: 1;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 1. ENCABEZADO (TÍTULO Y DESCRIPCIÓN) --}}
            <div class="mb-4">
                <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Administrador de Permisos</h3>
                <p class="mb-0 text-secondary text-sm">Asigna y gestiona los permisos correspondientes a cada rol del sistema.</p>
            </div>

            {{-- MENSAJES DE SESIÓN (CON EL NUEVO ESTILO) --}}
            @if(session('ok'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="ok-msg">
                    <i class="fas fa-check-circle me-2"></i> {{ session('ok') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger text-white alert-dismissible fade show" role="alert" id="err-msg">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 2. BARRA DE ACCIONES Y BUSCADOR --}}
            <div class="d-flex justify-content-end align-items-center mb-4">
                {{-- Buscador Estilizado --}}
                <div class="position-relative">
                    <div class="input-group bg-white border rounded overflow-hidden">
                        <span class="input-group-text bg-white border-0 pe-1 text-secondary">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="perm-search" 
                               class="form-control border-0 ps-2 shadow-none" 
                               placeholder="Buscar permiso (Presiona '/')..." 
                               style="min-width: 250px;">
                    </div>
                </div>
            </div>

            {{-- TARJETA CON LA TABLA MATRIZ --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0">
                    <form id="matrix-form" action="{{ route('roles.permissions.manager.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-start" style="min-width: 280px;">Permisos</th>

                                        @foreach($roles as $role)
                                            <th style="min-width: 160px;">
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <span class="fw-semibold">
                                                        {{ $role->name }}
                                                        @if($role->name === 'Administrador')
                                                            <span class="badge bg-secondary ms-1" title="Columna bloqueada">
                                                                <i class="fa-solid fa-lock"></i>
                                                            </span>
                                                        @endif
                                                    </span>

                                                    @if($role->name !== 'Administrador')
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-secondary col-check-all" data-role="{{ $role->id }}" type="button">Todas</button>
                                                            <button class="btn btn-outline-secondary col-uncheck-all" data-role="{{ $role->id }}" type="button">Ninguna</button>
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
                                            <td class="text-start perm-name" data-name="{{ strtolower($perm->name) }}">
                                                {{ $perm->name }}
                                            </td>

                                            @foreach($roles as $role)
                                                <td>
                                                    <input type="checkbox"
                                                           class="form-check-input perm-cell"
                                                           style="cursor: pointer;"
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

                        {{-- Footer de la tarjeta para el botón de guardar --}}
                        <div class="p-3 border-top text-end bg-light">
                            <button class="btn btn-primary mb-0">
                                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <x-app.footer />
    </main>

    <script>
        // 1. Foco rápido con "/"
        document.addEventListener('keydown', e => {
            // Solo si no estamos escribiendo en otro input
            if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
                e.preventDefault();
                document.getElementById('perm-search').focus();
            }
        });

        // 2. Buscar permisos (Filtrado)
        const search = document.getElementById('perm-search');
        search.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('#perm-tbody .perm-row').forEach(row => {
                const name = row.querySelector('.perm-name').dataset.name;
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });

        // 3. Seleccionar/limpiar toda la columna de un rol
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

        // 4. Ocultar mensajes flash
        (function(){
            ['ok-msg','err-msg'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                setTimeout(() => {
                    el.style.transition = "opacity .5s";
                    el.style.opacity = 0;
                    setTimeout(() => el.remove(), 500);
                }, 4000);
            });
        })();
    </script>
</x-app-layout>