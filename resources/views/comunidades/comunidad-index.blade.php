<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        /* ALERTAS (Verde Pastel) */
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

        .alert-danger .btn-close {
            filter: none !important;
            opacity: 0.5;
            color: #cf304a;
        }

        /* INPUTS */
        .input-group-text {
            border-color: #dee2e6;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #5ea6f7;
            box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25);
        }

        .compact-filter {
            width: auto;
            min-width: 140px;
            max-width: 180px;
        }

        .code-badge {
            font-size: 0.85rem;
            font-weight: 600;
            background-color: #f0f2f5;
            color: #344767;
            border: 1px solid #dee2e6;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-block;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">

            {{-- 2. ENCABEZADO --}}
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Comunidades</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $comunidades->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administra el catálogo de comunidades.</p>
                </div>

                {{-- Botón Nuevo (AJAX) --}}
                <button type="button" class="btn btn-success px-4 open-modal" style="height: fit-content;"
                    data-url="{{ route('comunidades.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nueva Comunidad
                </button>
            </div>

            {{-- 3. ALERTAS --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show alert-temporal mb-3">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show alert-temporal mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 4. FORMULARIO REPORTES Y FILTROS --}}
            <form action="{{ route('comunidades.reports') }}" method="POST" id="reportForm">
                @csrf

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">

                    {{-- Generar Reporte --}}
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto"
                            style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" type="button"
                            data-bs-toggle="dropdown">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i
                                        class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i
                                        class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    {{-- Filtros --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        {{-- Filtro por Parroquia --}}
                        <select id="parroquiaFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Toda Parroquia</option>
                            {{-- Asumo que pasas $parroquias desde el controlador para el filtro --}}
                            @foreach($parroquias as $p)
                                <option value="{{ $p->id }}" @selected(request('parroquia_id') == $p->id)>{{ $p->nombre }}
                                </option>
                            @endforeach
                        </select>

                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i
                                    class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" placeholder="Buscar..."
                                id="searchInput" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                {{-- 5. TABLA --}}
                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="selectAll"
                                                onclick="toggleSelectAll()" style="cursor: pointer;"></th>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 20%;">Código</th>
                                        <th class="text-start ps-4">Nombre</th>
                                        <th>Parroquia</th>
                                        <th>Cantón</th>
                                        <th style="width:180px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($comunidades as $c)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $c->id }}" class="check-item"
                                                    style="cursor: pointer;"></td>
                                            <td class="fw-bold text-secondary">
                                                {{ $comunidades->firstItem() + $loop->index }}
                                            </td>

                                            {{-- Asumo que usas 'codigo' o 'codigo_unico'. Ajusta según tu DB --}}
                                            <td><span
                                                    class="code-badge">{{ $c->codigo_unico ?? $c->codigo ?? 'N/A' }}</span>
                                            </td>

                                            <td class="fw-bold text-start ps-4 text-dark">{{ $c->nombre }}</td>

                                            <td><span
                                                    class="badge border text-dark bg-light">{{ $c->parroquia->nombre ?? 'N/A' }}</span>
                                            </td>
                                            <td><span
                                                    class="badge border text-secondary bg-white">{{ $c->parroquia->canton->nombre ?? 'N/A' }}</span>
                                            </td>

                                            <td>
                                                {{-- Ver (Ajax Modal) --}}
                                                <button type="button" class="btn btn-sm btn-info mb-0 me-1 open-modal"
                                                    data-url="{{ route('comunidades.show', $c->id) }}" title="Ver">
                                                    <i class="fa-solid fa-eye text-white"></i>
                                                </button>

                                                {{-- Editar (Ajax Modal) --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0 me-1 open-modal"
                                                    data-url="{{ route('comunidades.edit', $c->id) }}" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                {{-- Eliminar (SweetAlert) --}}
                                                <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn"
                                                    data-url="{{ route('comunidades.destroy', $c) }}"
                                                    data-item="{{ $c->nombre }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">No se encontraron
                                                comunidades.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">{{ $comunidades->links() }}</div>
                    </div>
                </div>
            </form>

            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO (VACÍO) --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    {{-- Aquí carga el contenido --}}
                </div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // 1. Alertas temporales
                setTimeout(() => { document.querySelectorAll('.alert-temporal').forEach(alert => { alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0; setTimeout(() => alert.remove(), 500); }); }, 3000);

                // 2. Filtros
                const searchInput = document.getElementById('searchInput');
                const parroquiaFilter = document.getElementById('parroquiaFilter');

                function applyFilters() {
                    window.location.href = "{{ route('comunidades.index') }}?search=" + encodeURIComponent(searchInput.value) + "&parroquia_id=" + parroquiaFilter.value;
                }

                if (searchInput) searchInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyFilters(); } });
                if (parroquiaFilter) parroquiaFilter.addEventListener('change', applyFilters);

                // 3. Modal Dinámico
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(this.getAttribute('data-url')).then(r => r.text()).then(h => { modalEl.querySelector('.modal-content').innerHTML = h; });
                    });
                });

                // 4. SweetAlert (Colorido)
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        Swal.fire({
                            title: '¿Eliminar Comunidad?',
                            html: `¿Deseas eliminar la comunidad <b>"${this.getAttribute('data-item')}"</b>?`,
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
                // ---------------------------------------------------------
                // 5. LÓGICA DE CARGA EN CASCADA (Cantón -> Parroquias)
                // ---------------------------------------------------------
                // Usamos "delegación de eventos" (document.body) porque el select
                // "canton_select" no existe al cargar la página, aparece después con el modal.

                document.body.addEventListener('change', async function (e) {
                    // Detectamos si el elemento que cambió tiene el ID 'canton_select'
                    if (e.target && e.target.id === 'canton_select') {

                        const cantonId = e.target.value;
                        const parroquiaSelect = document.getElementById('parroquia_select');

                        // Si no existe el select de parroquia (por seguridad), no hacemos nada
                        if (!parroquiaSelect) return;

                        // Mensaje de carga
                        parroquiaSelect.innerHTML = '<option value="">Cargando...</option>';

                        if (!cantonId) {
                            parroquiaSelect.innerHTML = '<option value="">— Selecciona Cantón —</option>';
                            return;
                        }

                        try {
                            // IMPORTANTE: Ruta correcta para obtener parroquias
                            // Asegúrate de que esta ruta '/cantones/{id}/parroquias' exista en tu web.php
                            const response = await fetch("{{ url('cantones') }}/" + cantonId + "/parroquias");

                            if (!response.ok) throw new Error('Error en la red');

                            const data = await response.json();

                            // Llenamos el select
                            parroquiaSelect.innerHTML = '<option value="">— Selecciona —</option>';
                            data.forEach(p => {
                                const opt = document.createElement('option');
                                opt.value = p.id;
                                opt.textContent = p.nombre;
                                parroquiaSelect.appendChild(opt);
                            });

                        } catch (err) {
                            console.error(err);
                            parroquiaSelect.innerHTML = '<option value="">Error al cargar</option>';
                        }
                    }
                });
            });

            function toggleSelectAll() {
                const c = document.getElementById('selectAll').checked;
                document.querySelectorAll('.check-item').forEach(x => x.checked = c);
            }
        </script>
    </main>
</x-app-layout>