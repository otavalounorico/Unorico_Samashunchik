<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        .alert-danger { background-color: #f8d7da !important; color: #842029 !important; border-color: #f5c2c7 !important; }
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 180px; }
        .badge-disponible { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .badge-ocupado { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
        .badge-lleno { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .btn-action { margin-right: 4px; }
        
        /* Alineación de filas internas */
        .fallecido-row { border-bottom: 1px solid #f0f0f0; padding: 4px 0; }
        .fallecido-row:last-child { border-bottom: none; }
        /* Estilo para los títulos de las columnas */
    .table thead th {
        font-size: 14px !important;    /* Tamaño más grande */
        text-transform: uppercase;    /* Mantener en mayúsculas para orden */
        letter-spacing: 0.05rem;      /* Separación ligera entre letras */
        font-weight: 700 !important;  /* Ponerlo más negrita */
        padding-top: 15px !important; /* Más espacio arriba */
        padding-bottom: 15px !important; /* Más espacio abajo */
    }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Asignaciones</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total Nichos: {{ $nichos->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administra la ocupación de nichos, códigos de acta y responsables.</p>
                </div>

                @can('crear asignacion')
                <button type="button" class="btn btn-success px-4 open-modal" style="height: fit-content;" data-url="{{ route('asignaciones.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nueva Asignación
                </button>
                @endcan
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

            {{-- 4. FORMULARIO Y FILTROS --}}
            <form action="{{ route('asignaciones.index') }}" method="GET" id="filterForm">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    @can('reportar asignacion')
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto" 
                                style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" 
                                type="button" id="dropdownGenerate" data-bs-toggle="dropdown" aria-expanded="false"> Reportes
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownGenerate">
                            <li><a href="{{ route('asignaciones.pdf.general') }}" target="_blank" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i> Reporte General</a></li>
                            <li><a href="{{ route('asignaciones.pdf.exhumados') }}" target="_blank" class="dropdown-item"><i class="fas fa-scroll text-dark me-2"></i> Ver Exhumados</a></li>
                        </ul>
                    </div>
                    @endcan

                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <select name="estado" class="form-select form-select-sm compact-filter ps-2" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Todos los Estados</option>
                            <option value="OCUPADO" @selected(request('estado') == 'OCUPADO')>Ocupados</option>
                            <option value="LLENO" @selected(request('estado') == 'LLENO')>Llenos</option>
                            <option value="DISPONIBLE" @selected(request('estado') == 'DISPONIBLE')>Disponibles</option>
                        </select>

                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-0 ps-1 shadow-none" placeholder="Código, Socio..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </form>

            {{-- 5. TABLA REORDENADA --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10">#</th> 
                                    <th class="opacity-10">Código</th>
                                    <th class="opacity-10 text-start ps-3">Fallecido</th>
                                    <th class="opacity-10 text-start ps-3">Responsable</th>
                                    <th class="opacity-10">Nicho</th>
                                    <th class="opacity-10">Estado</th>
                                    <th class="opacity-10">Ocupación</th>
                                    <th class="opacity-10" style="width:170px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($nichos as $nicho)
                                    @php 
                                        $ocupantes = $nicho->fallecidos->where('pivot.fecha_exhumacion', null);
                                    @endphp
                                    <tr>
                                        {{-- 1. # --}}
                                        <td class="text-sm fw-bold text-secondary">{{ $nichos->firstItem() + $loop->index }}</td>
                                        
                                        {{-- 2. CÓDIGO ASIGNACIÓN --}}
                                        <td>
                                            @forelse($ocupantes as $f)
                                                <div class="fallecido-row">
                                                    <span class="badge bg-light text-dark border" style="font-size: 0.75rem;">
                                                        {{ $f->pivot->codigo ?? 'S/C' }}
                                                    </span>
                                                </div>
                                            @empty
                                                <span class="text-muted text-xs">-</span>
                                            @endforelse
                                        </td>

                                        {{-- 3. FALLECIDO --}}
                                        <td class="text-start ps-3">
                                            @forelse($ocupantes as $f)
                                                <div class="fallecido-row text-sm">
                                                    {{ $f->apellidos }} {{ $f->nombres }}
                                                </div>
                                            @empty
                                                <span class="text-muted small fst-italic">-- Vacío --</span>
                                            @endforelse
                                        </td>

                                        {{-- 4. SOCIO RESPONSABLE --}}
                                        <td class="text-start ps-3">
                                            @if($nicho->socios->isNotEmpty())
                                                @php $socio = $nicho->socios->first(); @endphp
                                                <div class="text-sm font-weight-bold text-dark">{{ $socio->apellidos }} {{ $socio->nombres }}</div>
                                            @else
                                                <span class="badge bg-secondary">Sin Asignar</span>
                                            @endif
                                        </td>

                                        {{-- 5. NICHO (Ubicación) --}}
                                        <td class="fw-bold text-dark">
                                            {{ $nicho->codigo }}
                                            <div class="text-xs font-weight-normal text-secondary">{{ $nicho->bloque->descripcion ?? '' }}</div>
                                        </td>
                                        
                                        {{-- 6. ESTADO --}}
                                        <td>
                                            @php
                                                $clase = match($nicho->estado) {
                                                    'DISPONIBLE' => 'badge-disponible',
                                                    'OCUPADO' => 'badge-ocupado',
                                                    'LLENO' => 'badge-lleno',
                                                    default => 'bg-secondary text-white'
                                                };
                                            @endphp
                                            <span class="badge {{ $clase }}">{{ $nicho->estado }}</span>
                                        </td>

                                        {{-- 7. OCUPACIÓN --}}
                                        <td class="text-sm font-weight-bold">
                                            <span class="{{ $ocupantes->count() >= 3 ? 'text-danger' : 'text-dark' }}">
                                                {{ $ocupantes->count() }} / 3
                                            </span>
                                        </td>
                                        
                                        {{-- 8. ACCIONES --}}
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                @can('ver asignacion')
                                                <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal" 
                                                        data-url="{{ route('asignaciones.show', $nicho->id) }}" title="Ver Detalles">
                                                    <i class="fa fa-eye"style="font-size: 0.7rem;"></i>
                                                </button>
                                                @endcan

                                                @if($ocupantes->count() > 0 || $nicho->socios->count() > 0)
                                                    @can('editar asignacion')
                                                    <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal" 
                                                            data-url="{{ route('asignaciones.edit', $nicho->id) }}" title="Editar/Corregir">
                                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    @endcan
                                                @endif

                                                @if($ocupantes->count() > 0)
                                                    @can('exhumar cuerpo')
                                                    <button type="button" class="btn btn-sm btn-dark mb-0 btn-action open-modal" 
                                                            data-url="{{ route('asignaciones.exhumarForm', $nicho->id) }}" title="Registrar Exhumación">
                                                        <i class="fas fa-person-digging"style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    @endcan
                                                @endif

                                                @if($nicho->fallecidos->count() > 0)
                                                    @can('eliminar asignacion')
                                                    <button type="button" class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                            data-url="{{ route('asignacion.destroy', [$nicho->id, $nicho->fallecidos->last()->id]) }}"
                                                            data-item="Asignación {{ $nicho->codigo }}" title="Eliminar Registro (Error)">
                                                        <i class="fa-solid fa-trash" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron asignaciones registradas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 d-flex justify-content-end">{{ $nichos->appends(request()->query())->links() }}</div>
                </div>
            </div>

            {{-- Formulario oculto --}}
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"></div></div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Alertas Temporales
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3000);

                // Modal AJAX
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(this.getAttribute('data-url')).then(r => r.text()).then(h => { modalEl.querySelector('.modal-content').innerHTML = h; });
                    });
                });

                // SweetAlert Eliminar
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: '¿Eliminar Registro?', 
                            html: `¿Deseas eliminar el último registro de <b>"${this.getAttribute('data-item')}"</b>?<br><small class="text-danger">Esta acción borrará el historial de asignación.</small>`, 
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