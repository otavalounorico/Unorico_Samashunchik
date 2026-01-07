<x-app-layout>
    {{-- 1. ESTILOS (Iguales a los anteriores) --}}
    <style>
        .card-header-custom { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; }
        .group-title { color: #1c2a48; font-weight: 700; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Asignar Permisos</h3>
                    <span class="badge bg-warning text-dark border">
                        Rol: {{ $role->name }}
                    </span>
                </div>
                <p class="mb-0 text-secondary text-sm">Marca los permisos que deseas habilitar para este rol.</p>
            </div>

            {{-- 3. FORMULARIO --}}
            <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
                @csrf 
                @method('PUT')

                <div class="card shadow-sm border">
                    <div class="card-body p-4">
                        
                        @foreach($permissions as $grupo => $perms)
                            <div class="mb-4 border rounded overflow-hidden">
                                {{-- Cabecera del Grupo --}}
                                <div class="card-header-custom p-3 d-flex justify-content-between align-items-center">
                                    <h6 class="group-title mb-0">
                                        <i class="fas fa-layer-group me-2 text-secondary"></i> {{ $grupo }}
                                    </h6>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary js-group-check" data-group="{{ $grupo }}" style="font-size: 0.75rem;">Todas</button>
                                        <button type="button" class="btn btn-outline-secondary js-group-uncheck" data-group="{{ $grupo }}" style="font-size: 0.75rem;">Ninguna</button>
                                    </div>
                                </div>

                                {{-- Lista de Permisos --}}
                                <div class="p-3 bg-white">
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        @foreach($perms as $p)
                                            <div class="col">
                                                <div class="form-check d-flex align-items-center">
                                                    <input class="form-check-input perm-{{ $grupo }}"
                                                           type="checkbox"
                                                           id="perm-{{ $p->id }}"
                                                           name="permissions[]"
                                                           value="{{ $p->id }}"
                                                           style="width: 1.1em; height: 1.1em; cursor: pointer;"
                                                           @checked(in_array($p->id, $rolePermissionIds))>
                                                    <label class="form-check-label ms-2 text-dark" for="perm-{{ $p->id }}" style="cursor: pointer; user-select: none;">
                                                        {{ $p->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- Footer --}}
                    <div class="card-footer bg-light border-top d-flex gap-2 justify-content-end p-3">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary mb-0">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-success mb-0">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>

        </div>

        <x-app.footer />
    </main>

    <script>
        // Lógica para seleccionar/deseleccionar por grupo
        document.querySelectorAll('.js-group-check').forEach(btn => {
            btn.addEventListener('click', () => {
                const group = btn.getAttribute('data-group');
                document.querySelectorAll('.perm-' + group).forEach(cb => cb.checked = true);
            });
        });
        document.querySelectorAll('.js-group-uncheck').forEach(btn => {
            btn.addEventListener('click', () => {
                const group = btn.getAttribute('data-group');
                document.querySelectorAll('.perm-' + group).forEach(cb => cb.checked = false);
            });
        });
    </script>
</x-app-layout>