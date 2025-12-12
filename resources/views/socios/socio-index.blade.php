<x-app-layout>
    {{-- 1. ESTILOS (Idénticos a Parroquias) --}}
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
            
            {{-- 2. ENCABEZADO Y BOTÓN NUEVO (Formato Parroquias) --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Socios</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $socios->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Aquí puedes gestionar y generar reportes de los socios.</p>
                </div>

                {{-- Botón Nuevo (Mismo estilo verde) --}}
                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('socios.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Socio
                </button>
            </div>

            {{-- 3. ALERTAS (Mismo estilo visual) --}}
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

            {{-- 4. FORMULARIO DE REPORTES Y FILTROS --}}
            <form action="{{ route('socios.reports') }}" method="POST" id="reportForm">
                @csrf

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- BOTÓN GENERAR REPORTE (Azul) --}}
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

                    {{-- FILTROS (Compactos y limpios) --}}
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

                {{-- 5. TABLA (Formato IDÉNTICO a Parroquias: Bordeada, Cabecera oscura, Centrada) --}}
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
                                        <th>Nombre Completo</th>
                                        <th>Comunidad</th>
                                        <th style="width:140px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($socios as $s)
                                        <tr>
                                            {{-- Checkbox --}}
                                            <td><input type="checkbox" name="ids[]" value="{{ $s->id }}" class="check-item" style="cursor: pointer;"></td>
                                            
                                            {{-- Índice --}}
                                            <td class="fw-bold text-secondary">{{ $socios->firstItem() + $loop->index }}</td>
                                            
                                            {{-- Código (Negrita oscura) --}}
                                            <td class="fw-bold text-dark">{{ $s->codigo }}</td>
                                            
                                            {{-- Cédula --}}
                                            <td>{{ $s->cedula }}</td>
                                            
                                            {{-- Nombre (Alineado a izquierda con padding, igual que Parroquias) --}}
                                            <td class="text-start ps-4">{{ $s->apellidos }} {{ $s->nombres }}</td>
                                            
                                            {{-- Comunidad (Badge estilo Cantón) --}}
                                            <td><span class="badge border text-dark bg-light">{{ $s->comunidad?->nombre ?? 'N/A' }}</span></td>
                                            
                                            {{-- Acciones --}}
                                            <td>
                                                {{-- Botón Ver (Opcional, estilo info) --}}
                                                <button type="button" class="btn btn-sm btn-info mb-0 me-1 open-modal" 
                                                        data-url="{{ route('socios.show', $s) }}" title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </button>

                                                {{-- Botón Editar (Amarillo) --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0 me-1 open-modal" 
                                                        data-url="{{ route('socios.edit', $s) }}" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                {{-- Botón Eliminar (Rojo) --}}
                                                <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn"
                                                        data-url="{{ route('socios.destroy', $s) }}"
                                                        data-item="{{ $s->apellidos }} {{ $s->nombres }}" title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron socios.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">{{ $socios->links() }}</div>
                    </div>
                </div>
            </form>

            {{-- Formulario oculto para eliminar --}}
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO (Necesario para que funcione tu lógica de Socios) --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"></div></div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS (Combinación de la estética visual con tu lógica funcional) --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Lógica de Alerta Temporal (Igual a Parroquias)
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; 
                        alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3000);

                // Lógica de Filtros (Igual a Parroquias)
                const searchInput = document.getElementById('searchInput'); 
                const comunidadFilter = document.getElementById('comunidadFilter');
                
                function applyFilters() { 
                    window.location.href = "{{ route('socios.index') }}?search=" + encodeURIComponent(searchInput.value) + "&comunidad_id=" + comunidadFilter.value; 
                }
                
                if(searchInput) searchInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyFilters(); } });
                if(comunidadFilter) comunidadFilter.addEventListener('change', applyFilters);

                // Lógica del Modal AJAX (Mantenemos tu lógica de Socios porque es mejor para formularios grandes)
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(this.getAttribute('data-url')).then(r => r.text()).then(h => { modalEl.querySelector('.modal-content').innerHTML = h; });
                    });
                });

                // Lógica de SweetAlert para Eliminar (Mejor que el confirm nativo)
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: '¿Eliminar Socio?', 
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