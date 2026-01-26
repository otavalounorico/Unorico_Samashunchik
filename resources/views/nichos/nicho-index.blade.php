<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        /* ESTILO ALERTAS (VERDE PASTEL) */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-weight: 400 !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }

        /* Estilos inputs */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 180px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Nichos</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $nichos->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administración de espacios, capacidad y estados.</p>
                </div>

                {{-- Botón Nuevo --}}
                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('nichos.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Nicho
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
            <form action="{{ route('nichos.reports') }}" method="POST" id="reportForm">
                @csrf
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="bloque_id" value="{{ request('bloque_id') }}">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- Generar Reporte --}}
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto" 
                                style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" 
                                type="button" id="dropdownGenerate" data-bs-toggle="dropdown">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    {{-- Filtros --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <select id="bloqueFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Todos los bloques</option>
                            @foreach($bloques as $b)
                                <option value="{{ $b->id }}" @selected(request('bloque_id') == $b->id)>{{ $b->nombre }}</option>
                            @endforeach
                        </select>

                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" placeholder="Buscar código..." id="searchInput" value="{{ request('q') }}">
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
                                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                                        <th style="width: 50px;">#</th>
                                        <th>Código</th>
                                        <th>Bloque</th>
                                        {{-- NUEVA COLUMNA: TIPO --}}
                                        <th>Tipo</th>
                                        <th>Capacidad</th>
                                        <th>Estado</th>
                                        <th>Disp.</th>
                                        <th style="width:170px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($nichos as $n)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $n->id }}" class="check-item"></td>
                                            <td class="fw-bold text-secondary">{{ $nichos->firstItem() + $loop->index }}</td>
                                            <td class="fw-bold text-dark">{{ $n->codigo }}</td>
                                            <td>
                                                <span class="d-block text-sm fw-bold">{{ $n->bloque?->codigo }}</span>
                                                <small class="text-muted" style="font-size: 10px;">{{ Str::limit($n->bloque?->nombre, 15) }}</small>
                                            </td>

                                            {{-- MOSTRAR EL TIPO DE NICHO --}}
                                            <td>
                                                @if($n->tipo_nicho === 'PROPIO')
                                                    <span class="badge bg-gradient-info" style="font-size: 0.7rem; letter-spacing: 0.5px;">PROPIO</span>
                                                @else
                                                    <span class="badge bg-gradient-primary" style="font-size: 0.7rem; letter-spacing: 0.5px;">COMPARTIDO</span>
                                                @endif
                                            </td>

                                            <td>{{ $n->capacidad }}</td>
                                            <td>
                                                @switch($n->estado)
                                                    @case('disponible') <span class="badge bg-success">Disponible</span> @break
                                                    @case('ocupado') <span class="badge bg-danger">Ocupado</span> @break
                                                    @default <span class="badge bg-warning text-dark">{{ ucfirst($n->estado) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($n->disponible) <i class="fas fa-check-circle text-success" title="Sí"></i>
                                                @else <i class="fas fa-times-circle text-secondary" title="No"></i> @endif
                                            </td>
                                            <td>
                                                {{-- BOTÓN QR (Menú Desplegable) --}}
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-dark mb-0 me-1 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Descargar QR">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('nichos.qr', ['nicho' => $n->id, 'mode' => 'text']) }}" target="_blank">
                                                                <i class="fas fa-file-alt me-2 text-secondary"></i> QR Texto (Offline)
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                
                                                {{-- Ver --}}
                                                <button type="button" class="btn btn-sm btn-info mb-0 me-1 open-modal" data-url="{{ route('nichos.show', $n) }}" title="Ver"><i class="fa fa-eye"></i></button>
                                                
                                                {{-- Editar --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0 me-1 open-modal" data-url="{{ route('nichos.edit', $n) }}" title="Editar"><i class="fa-solid fa-pen-to-square"></i></button>
                                                
                                                {{-- Eliminar --}}
                                                <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn" data-url="{{ route('nichos.destroy', $n) }}" data-item="{{ $n->codigo }}"><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center py-4 text-muted">No se encontraron nichos.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">{{ $nichos->links() }}</div>
                    </div>
                </div>
            </form>
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"></div></div></div>
        
        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(() => { document.querySelectorAll('.alert-temporal').forEach(alert => { alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0; setTimeout(() => alert.remove(), 500); }); }, 3000);

                const searchInput = document.getElementById('searchInput'); 
                const bloqueFilter = document.getElementById('bloqueFilter');
                
                function applyFilters() { window.location.href = "{{ route('nichos.index') }}?q=" + encodeURIComponent(searchInput.value) + "&bloque_id=" + bloqueFilter.value; }
                if(searchInput) searchInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyFilters(); } });
                if(bloqueFilter) bloqueFilter.addEventListener('change', applyFilters);

                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(this.getAttribute('data-url')).then(r => r.text()).then(h => { modalEl.querySelector('.modal-content').innerHTML = h; });
                    });
                });

                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({ title: '¿Eliminar?', html: `Se eliminará el nicho <b>"${this.getAttribute('data-item')}"</b>`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, eliminar' }).then((r) => { if (r.isConfirmed) { const f = document.getElementById('deleteForm'); f.action = this.getAttribute('data-url'); f.submit(); } });
                    });
                });
            });
            function toggleSelectAll() { const c = document.getElementById('selectAll').checked; document.querySelectorAll('.check-item').forEach(x => x.checked = c); }
        </script>
    </main>
</x-app-layout>