<x-app-layout>
    {{-- 1. ESTILOS (Formato Parroquias) --}}
    <style>
        /* ESTILO ALERTAS (VERDE PASTEL) */
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border-color: #e4f4db !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }

        /* Estilos para input groups y focus */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus {
            border-color: #5ea6f7;
            box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25);
        }

        /* Clase para inputs "delgados" como botones */
        .compact-filter {
            width: auto; 
            min-width: 140px; 
            max-width: 180px;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO (Estilo Parroquias) --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Fallecidos</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $fallecidos->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Gestión y reportes de registros de defunción.</p>
                </div>

                {{-- Botón Nuevo (Verde sólido) --}}
                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('fallecidos.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Registro
                </button>
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
            <form action="{{ route('fallecidos.reports') }}" method="POST" id="reportForm">
                @csrf
                {{-- Inputs ocultos para mantener filtros al generar reporte --}}
                <input type="hidden" name="comunidad_id" value="{{ request('comunidad_id') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- Botón Generar Reporte (Azul) --}}
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

                    {{-- Filtros (Compactos) --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <select id="comunidadFilter" class="form-select form-select-sm compact-filter ps-2" title="Filtrar por Comunidad">
                            <option value="">Toda Comunidad</option>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(request('comunidad_id') == $c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>

                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" 
                                   placeholder="Buscar..." id="searchInput" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                {{-- 5. TABLA (Estilo Parroquias: Bordeada, Dark Header, Centrada) --}}
                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;"></th>
                                        <th style="width: 50px;">#</th>
                                        <th>Código</th>
                                        <th>Cédula</th>
                                        <th>Apellidos y Nombres</th>
                                        <th>Comunidad</th>
                                        <th>Fecha Fall.</th>
                                        <th style="width:140px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($fallecidos as $f)
                                        <tr>
                                            {{-- Checkbox --}}
                                            <td><input type="checkbox" name="ids[]" value="{{ $f->id }}" class="check-item" style="cursor: pointer;"></td>
                                            
                                            {{-- # --}}
                                            <td class="fw-bold text-secondary">{{ $fallecidos->firstItem() + $loop->index }}</td>
                                            
                                            {{-- Código (Negrita oscura) --}}
                                            <td class="fw-bold text-dark">{{ $f->codigo }}</td>
                                            
                                            {{-- Cédula --}}
                                            <td>{{ $f->cedula ?? 'S/N' }}</td>
                                            
                                            {{-- Nombre (Alineado izquierda) --}}
                                            <td class="text-start ps-4">{{ $f->apellidos }} {{ $f->nombres }}</td>
                                            
                                            {{-- Comunidad (Badge) --}}
                                            <td><span class="badge border text-dark bg-light">{{ $f->comunidad?->nombre ?? 'N/A' }}</span></td>
                                            
                                            {{-- Fecha --}}
                                            <td>{{ $f->fecha_fallecimiento ? $f->fecha_fallecimiento->format('d/m/Y') : '-' }}</td>
                                            
                                            {{-- Acciones --}}
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info mb-0 me-1 open-modal" 
                                                        data-url="{{ route('fallecidos.show', $f) }}" title="Ver">
                                                    <i class="fa fa-eye"style="font-size: 0.7rem;"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning mb-0 me-1 open-modal" 
                                                        data-url="{{ route('fallecidos.edit', $f) }}" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"style="font-size: 0.7rem;"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn"
                                                        data-url="{{ route('fallecidos.destroy', $f) }}"
                                                        data-item="{{ $f->apellidos }}" title="Eliminar">
                                                    <i class="fa-solid fa-trash"style="font-size: 0.7rem;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron registros.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">{{ $fallecidos->links() }}</div>
                    </div>
                </div>
            </form>

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
                        alert.style.transition = "opacity 0.5s"; 
                        alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3000);

                // Filtros
                const searchInput = document.getElementById('searchInput'); 
                const comunidadFilter = document.getElementById('comunidadFilter');
                
                function applyFilters() { 
                    window.location.href = "{{ route('fallecidos.index') }}?search=" + encodeURIComponent(searchInput.value) + "&comunidad_id=" + comunidadFilter.value; 
                }
                
                if(searchInput) searchInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyFilters(); } });
                if(comunidadFilter) comunidadFilter.addEventListener('change', applyFilters);

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
                            html: `¿Deseas eliminar a <b>"${this.getAttribute('data-item')}"</b>?`, 
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

            function toggleSelectAll() { 
                const c = document.getElementById('selectAll').checked; 
                document.querySelectorAll('.check-item').forEach(x => x.checked = c); 
            }
        </script>
    </main>
</x-app-layout>