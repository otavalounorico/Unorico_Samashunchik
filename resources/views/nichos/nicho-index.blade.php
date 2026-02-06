<x-app-layout>
    <style>
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 180px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Nichos</h3>
                        <span class="badge bg-light text-dark border">Total: {{ $nichos->total() }}</span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administración de espacios, capacidad y estados físicos.</p>
                </div>
                <button type="button" class="btn btn-success px-4 open-modal" data-url="{{ route('nichos.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Nicho
                </button>
            </div>

            {{-- ALERTAS --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show alert-temporal mb-3">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger text-white alert-dismissible fade show alert-temporal mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- FILTROS --}}
            <form action="{{ route('nichos.reports') }}" method="POST" id="mainForm">
                @csrf
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="bloque_id" value="{{ request('bloque_id') }}">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 bg-primary" type="button" data-bs-toggle="dropdown">Generar Reporte</button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <select id="bloqueFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Todos los bloques</option>
                            @foreach($bloques as $b)
                                <option value="{{ $b->id }}" @selected(request('bloque_id') == $b->id)>{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="input-group input-group-sm bg-white border rounded compact-filter">
                            <span class="input-group-text bg-white border-0 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 shadow-none" placeholder="Buscar código..." id="searchInput" value="{{ request('q') }}">
                        </div>
                    </div>
                </div>

                {{-- TABLA --}}
                <div class="card shadow-sm border">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAll"></th>
                                    <th width="50">#</th>
                                    <th>Código</th>
                                    <th>Bloque</th>
                                    <th>Clase</th>      {{-- NUEVO --}}
                                    <th>Tipo</th>
                                    <th>Ocupación</th>  {{-- NUEVO --}}
                                    <th>Estado Físico</th>
                                    <th>Disp.</th>
                                    <th width="170">Acciones</th>
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
                                        
                                        {{-- CLASE (Nuevo) --}}
                                        <td>
                                            @if($n->clase_nicho == 'TIERRA')
                                                <span class="badge bg-secondary"><i class="fas fa-seedling me-1"></i> Tierra</span>
                                            @else
                                                <span class="badge bg-dark"><i class="fas fa-dungeon me-1"></i> Bóveda</span>
                                            @endif
                                        </td>

                                        {{-- TIPO --}}
                                        <td>
                                            @if($n->tipo_nicho === 'PROPIO')
                                                <span class="badge bg-info text-dark">PROPIO</span>
                                            @else
                                                <span class="badge bg-primary">COMPARTIDO</span>
                                            @endif
                                        </td>

                                        {{-- OCUPACIÓN (Nuevo) --}}
                                        <td>
                                            <span class="fw-bold {{ $n->ocupacion >= $n->capacidad ? 'text-danger' : 'text-success' }}">
                                                {{ $n->ocupacion }} / {{ $n->capacidad }}
                                            </span>
                                        </td>

                                        {{-- ESTADO FÍSICO --}}
                                        <td>
                                            @switch($n->estado)
                                                @case('BUENO') <span class="badge bg-success">Bueno</span> @break
                                                @case('MANTENIMIENTO') <span class="badge bg-warning text-dark">Mantenim.</span> @break
                                                @case('MALO') <span class="badge bg-danger">Malo</span> @break
                                                @case('ABANDONADO') <span class="badge bg-secondary">Abandonado</span> @break
                                                @default <span class="badge bg-light text-dark">{{ $n->estado }}</span>
                                            @endswitch
                                        </td>

                                        {{-- DISPONIBILIDAD --}}
                                        <td>
                                            @if($n->disponible)
                                                <i class="fas fa-check-circle text-success" title="Disponible"></i>
                                            @else
                                                <i class="fas fa-times-circle text-secondary" title="No disponible"></i>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-dark mb-0 me-1 dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-qrcode"></i></button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('nichos.qr', ['nicho' => $n->id, 'mode' => 'text']) }}" target="_blank"><i class="fas fa-file-alt me-2 text-secondary"></i> QR Texto</a></li>
                                                </ul>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-info mb-0 me-1 open-modal" data-url="{{ route('nichos.show', $n) }}" title="Ver"><i class="fa fa-eye"></i></button>
                                            <button type="button" class="btn btn-sm btn-warning mb-0 me-1 open-modal" data-url="{{ route('nichos.edit', $n) }}" title="Editar"><i class="fa-solid fa-pen-to-square"></i></button>
                                            <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn" data-url="{{ route('nichos.destroy', $n) }}" data-item="{{ $n->codigo }}"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="text-center py-4 text-muted">No se encontraron nichos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 d-flex justify-content-end">{{ $nichos->links() }}</div>
                </div>
            </form>
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"></div></div></div>
        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(() => document.querySelectorAll('.alert-temporal').forEach(a => { a.style.opacity = 0; setTimeout(() => a.remove(), 500); }), 3000);

                const search = document.getElementById('searchInput');
                const filter = document.getElementById('bloqueFilter');
                
                const apply = () => window.location.href = "{{ route('nichos.index') }}?q=" + encodeURIComponent(search.value) + "&bloque_id=" + filter.value;
                
                if(search) search.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); apply(); } });
                if(filter) filter.addEventListener('change', apply);
                
                document.getElementById('selectAll')?.addEventListener('click', function() {
                    document.querySelectorAll('.check-item').forEach(x => x.checked = this.checked);
                });

                const modal = new bootstrap.Modal(document.getElementById('dynamicModal'));
                document.querySelectorAll('.open-modal').forEach(btn => btn.addEventListener('click', function () {
                    document.querySelector('#dynamicModal .modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                    modal.show();
                    fetch(this.getAttribute('data-url')).then(r => r.text()).then(h => document.querySelector('#dynamicModal .modal-content').innerHTML = h);
                }));

                document.querySelectorAll('.js-delete-btn').forEach(btn => btn.addEventListener('click', function() {
                    Swal.fire({ title: '¿Eliminar?', html: `Se eliminará el nicho <b>"${this.getAttribute('data-item')}"</b>`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, eliminar' })
                    .then((r) => { if (r.isConfirmed) { const f = document.getElementById('deleteForm'); f.action = this.getAttribute('data-url'); f.submit(); } });
                }));
            });
        </script>
    </main>
</x-app-layout>