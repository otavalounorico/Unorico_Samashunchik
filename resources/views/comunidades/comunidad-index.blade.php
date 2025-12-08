<x-app-layout>
    <style>
        /* 1. ESTILO ALERTAS (VERDE PASTEL) */
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border-color: #e4f4db !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }

        /* Ajuste para inputs de búsqueda y filtro */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus {
            border-color: #5ea6f7;
            box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25);
        }

        /* Clase para hacer los inputs "delgados" como botones */
        .compact-filter {
            width: auto; 
            min-width: 140px; 
            max-width: 180px;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 1. ENCABEZADO Y BOTÓN NUEVO (Adaptado a Comunidades) --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Comunidades</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $comunidades->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Aquí puedes gestionar y generar reportes de las comunidades.</p>
                </div>

                {{-- Botón Nueva Comunidad con Modal --}}
                <button type="button" class="btn btn-success px-4" 
                        style="height: fit-content;"
                        data-bs-toggle="modal" data-bs-target="#createComunidadModal">
                    <i class="fa-solid fa-plus me-2"></i> Nueva Comunidad
                </button>
            </div>

            {{-- Manejo de Alertas (Incluye el mensaje de validación del Reporte) --}}
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

            {{-- FORMULARIO DE REPORTE/FILTRO (Adaptado a Comunidades) --}}
            {{-- Se usa la ruta comunidades.reports y se añade lógica JS para enviar filtros --}}
            <form action="{{ route('comunidades.reports') }}" method="POST" id="reportForm">
                @csrf

                {{-- Campos ocultos para enviar los filtros actuales al controlador para el reporte --}}
                <input type="hidden" name="parroquia_id" id="hiddenParroquiaId" value="{{ request('parroquia_id') }}">
                <input type="hidden" name="search" id="hiddenSearch" value="{{ request('search') }}">
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- BOTÓN GENERAR REPORTE --}}
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

                    {{-- FILTROS Y BÚSQUEDA (Adaptado a Parroquia en lugar de Cantón) --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        
                        {{-- Filtro por Parroquia (Dropdown) --}}
                        <select id="parroquiaFilter" class="form-select form-select-sm compact-filter ps-2" title="Filtrar por Parroquia">
                            <option value="">Toda Parroquia</option>
                            {{-- Usamos $parroquias pasada desde el controller --}}
                            @foreach($parroquias as $p)
                                <option value="{{ $p->id }}" @selected(request('parroquia_id') == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>

                        {{-- Búsqueda en tiempo real --}}
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" 
                                   placeholder="Buscar..." id="searchInput" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                {{-- TABLA DE DATOS (Adaptada a Comunidades) --}}
                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;"></th>
                                        <th style="width: 50px;">#</th>
                                        <th>Código Único</th>
                                        <th>Nombre</th>
                                        <th>Parroquia</th>
                                        <th>Cantón</th>
                                        <th style="width:140px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="comunidades-table-body">
                                    @forelse ($comunidades as $com)
                                        <tr data-name="{{ Str::lower($com->nombre) }}" data-parroquia-id="{{ $com->parroquia_id }}">
                                            <td><input type="checkbox" name="ids[]" value="{{ $com->id }}" style="cursor: pointer;"></td>
                                            <td class="fw-bold text-secondary">{{ $comunidades->firstItem() + $loop->index }}</td>
                                            <td class="fw-bold text-dark">{{ $com->codigo_unico }}</td>
                                            <td class="text-start ps-4">{{ $com->nombre }}</td>
                                            <td><span class="badge border text-dark bg-light">{{ $com->parroquia->nombre }}</span></td>
                                            <td><span class="badge border text-dark bg-light">{{ $com->parroquia->canton->nombre }}</span></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning mb-0 me-1" 
                                                        data-bs-toggle="modal" data-bs-target="#editComunidadModal"
                                                        data-id="{{ $com->id }}" 
                                                        data-nombre="{{ $com->nombre }}"
                                                        data-parroquia="{{ $com->parroquia_id }}" 
                                                        data-codigo="{{ $com->codigo_unico }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn"
                                                        data-url="{{ route('comunidades.destroy', $com) }}"
                                                        data-item="{{ $com->nombre }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron comunidades.</td></tr>
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

        {{-- MODALES (Deben estar definidos aquí, similar a la plantilla de Parroquias) --}}

        {{-- Modal Crear Comunidad (similar al de Parroquias) --}}
        <div class="modal fade" id="createComunidadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title text-white">Nueva Comunidad</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('comunidades.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info py-2 mb-3 text-xs"><i class="fas fa-info-circle me-1"></i> Código automático (Ej: COM001).</div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Parroquia</label>
                                <select name="parroquia_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($parroquias as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option> @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre Comunidad</label>
                                <input type="text" name="nombre" class="form-control" required placeholder="Ej: San Francisco">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Editar Comunidad (similar al de Parroquias) --}}
        <div class="modal fade" id="editComunidadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title font-weight-bold" id="editModalTitle">Editar Comunidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editComunidadForm" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3"><label class="form-label fw-bold text-muted">Código</label><input type="text" id="editCodigo" class="form-control bg-light" readonly></div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Parroquia</label>
                                <select name="parroquia_id" id="editParroquia" class="form-select" required>
                                    @foreach($parroquias as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option> @endforeach
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label fw-bold">Nombre</label><input type="text" name="nombre" id="editNombre" class="form-control" required></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <x-app.footer />

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Lógica para ocultar alertas (Igual que Parroquias)
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; 
                        alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3000);

                const searchInput = document.getElementById('searchInput'); 
                const parroquiaFilter = document.getElementById('parroquiaFilter');
                
                const hiddenSearch = document.getElementById('hiddenSearch');
                const hiddenParroquiaId = document.getElementById('hiddenParroquiaId');

                // Función que recarga la página con los filtros (para el dropdown de Parroquia)
                function applyFilters() { 
                    // Cuando el filtro de parroquia cambia, recargamos la página
                    window.location.href = "{{ route('comunidades.index') }}?search=" + encodeURIComponent(searchInput.value) + "&parroquia_id=" + parroquiaFilter.value; 
                }
                
                // 1. Manejo del Filtro de Parroquia (Recarga la página)
                parroquiaFilter.addEventListener('change', applyFilters);

                // 2. Manejo de la Búsqueda (Actualiza campos ocultos para reporte y filtra tabla visible)
                searchInput.addEventListener('input', function() {
                    // Actualiza el campo oculto para que el reporte tome este valor
                    hiddenSearch.value = this.value;

                    // Lógica para el filtrado en tiempo real de la tabla visible (similar a la lógica de Parroquias que tenías con JS)
                    const tableBody = document.getElementById('comunidades-table-body');
                    const rows = tableBody.querySelectorAll('tr[data-name]');
                    const searchTerm = this.value.toLowerCase().trim();
                    const filterParroquia = parroquiaFilter.value; // Obtiene el filtro activo

                    let resultsFound = 0;
                    let hasNoResultsRow = false; // Indica si la fila de 'No resultados' es la que puso Laravel

                    rows.forEach(row => {
                        const name = row.getAttribute('data-name');
                        const parroquiaId = row.getAttribute('data-parroquia-id');
                        
                        // Si la fila es la de 'No se encontraron comunidades.' de Laravel
                        if (!name) { 
                            hasNoResultsRow = true;
                            return; 
                        }

                        const nameMatches = name.includes(searchTerm);
                        // Filtro de Parroquia: si no hay filtro (valor vacío) O si coincide el ID
                        const parroquiaMatches = filterParroquia === '' || parroquiaId === filterParroquia;

                        if (nameMatches && parroquiaMatches) {
                            row.style.display = '';
                            resultsFound++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Si se hizo un filtrado JS y no hubo resultados, y no hay una fila de 'no results' de Laravel, puedes añadir una temporal.
                    // (Omitimos esta parte compleja de crear la fila temporal para mantenernos en la validación simple del controlador)
                    // La validación clave es que si la tabla tiene 0 resultados filtrados en JS, se recomienda que el usuario haga ENTER para recargar la página y obtener resultados del servidor.
                });
                
                // Opcional: Recargar la página si se presiona ENTER en la búsqueda
                searchInput.addEventListener('keypress', function (e) { 
                    if (e.key === 'Enter') { 
                        e.preventDefault(); 
                        applyFilters(); // Aplica los filtros del servidor
                    } 
                });

                // 3. Manejo de Modales (Editar Comunidad)
                var editModal = document.getElementById('editComunidadModal');
                editModal.addEventListener('show.bs.modal', function (event) {
                    var b = event.relatedTarget;
                    
                    document.getElementById('editNombre').value = b.getAttribute('data-nombre');
                    document.getElementById('editParroquia').value = b.getAttribute('data-parroquia'); // Adaptado a Parroquia
                    document.getElementById('editCodigo').value = b.getAttribute('data-codigo');
                    
                    document.getElementById('editComunidadForm').action = "{{ route('comunidades.update', 'ID_PLACEHOLDER') }}".replace('ID_PLACEHOLDER', b.getAttribute('data-id'));
                });

                // 4. Lógica de Eliminación (Igual que Parroquias)
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const url = this.getAttribute('data-url');
                        const item = this.getAttribute('data-item');
                        
                        if(confirm('¿Eliminar ' + item + '?')) { 
                            const f = document.getElementById('deleteForm'); 
                            f.action = url; 
                            f.submit(); 
                        }
                    });
                });
            });

            // Función de selección de todos los checkboxes
            function toggleSelectAll() { 
                const isChecked = document.getElementById('selectAll').checked; 
                document.querySelectorAll('input[name="ids[]"]').forEach(x => x.checked = isChecked); 
            }
        </script>
    </main>
</x-app-layout>