<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        /* ALERTAS */
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border-color: #e4f4db !important;
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

        .candidate-list-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
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
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">

            {{-- 2. ENCABEZADO --}}
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Socios</h3>
                        <span class="badge bg-light text-dark border">Total: {{ $socios->total() }}</span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Gestión de socios y beneficios.</p>
                </div>

                <button type="button" class="btn btn-success px-4 open-modal" style="height: fit-content;"
                    data-url="{{ route('socios.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Socio
                </button>
            </div>

            {{-- 3. ALERTAS DE SISTEMA --}}
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

            {{-- 4. ALERTA DE "CANDIDATOS A EXONERACIÓN" --}}
            @if(isset($candidatos) && $candidatos->isNotEmpty())
                <div class="alert alert-warning text-dark border-warning mb-4 shadow-sm" style="background-color: #fff3cd;">
                    <div class="d-flex align-items-start">
                        <div class="me-3 mt-1"><i class="fas fa-bell fa-lg text-warning"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading fw-bold mb-1">
                                <i class="fas fa-exclamation-triangle me-1"></i> Atención: Socios Elegibles para Exoneración
                            </h6>
                            <p class="mb-1 small">
                                Estos socios han cumplido 75 años. Verificar pagos y cambiar a "Exonerado":
                            </p>
                            <div class="mt-2 border-top border-warning pt-2">
                                <ul class="list-unstyled mb-0 row">
                                    @foreach($candidatos as $c)
                                        <li
                                            class="col-md-6 mb-1 candidate-list-item p-1 d-flex justify-content-between align-items-center">
                                            <span>
                                                • <strong>{{ $c->apellidos }} {{ $c->nombres }}</strong>
                                                <span class="text-muted small">({{ $c->edad }} años)</span>
                                            </span>
                                            <button type="button"
                                                class="btn btn-sm btn-link text-primary fw-bold p-0 m-0 open-modal"
                                                data-url="{{ route('socios.edit', $c) }}"
                                                style="text-decoration: underline; font-size: 0.85rem;">
                                                Gestionar
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            {{-- 5. FILTROS Y TABLA --}}
            <form action="{{ route('socios.reports') }}" method="POST" id="reportForm">
                @csrf

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto"
                            style="background-color: #5ea6f7;" type="button" data-bs-toggle="dropdown">Generar
                            Reporte</button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i
                                        class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i
                                        class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <select id="comunidadFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Toda Comunidad</option>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(request('comunidad_id') == $c->id)>{{ $c->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group input-group-sm bg-white border rounded compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i
                                    class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" placeholder="Buscar..."
                                id="searchInput" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="selectAll"
                                                onclick="toggleSelectAll()"></th>
                                        <th style="width: 50px;">#</th>
                                        <th>Código</th>
                                        <th>Cédula</th>
                                        <th>Nombre Completo</th>
                                        <th>Comunidad</th>
                                        <th>Edad</th>
                                        
                                        {{-- ============================== --}}
                                        {{-- NUEVA COLUMNA DE NICHOS --}}
                                        <th class="text-center" style="width: 140px;">Nichos</th> 
                                        {{-- ============================== --}}
                                        
                                        <th style="width: 130px;">Estado</th>
                                        <th style="width:140px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($socios as $s)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $s->id }}" class="check-item">
                                            </td>
                                            <td class="fw-bold text-secondary">{{ $socios->firstItem() + $loop->index }}
                                            </td>
                                            <td class="fw-bold text-dark">{{ $s->codigo }}</td>
                                            <td>{{ $s->cedula }}</td>
                                            <td class="text-start ps-4">{{ $s->apellidos }} {{ $s->nombres }}</td>
                                            <td><span
                                                    class="badge border text-dark bg-light">{{ $s->comunidad?->nombre ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $s->edad }} años</td>

                                            {{-- ============================== --}}
                                            {{-- LÓGICA DE CONTADORES DE NICHOS --}}
                                            {{-- ============================== --}}
                                            <td class="text-center align-middle">
                                                @if($s->total_nichos > 0)
                                                    <div class="d-flex flex-column gap-1 align-items-center">
                                                        
                                                        {{-- CONTADOR DE PROPIOS (AMARILLO) --}}
                                                        @if($s->propios_count > 0)
                                                            <span class="badge border text-dark bg-light w-100" style="font-weight: 600; font-size: 0.75rem;">
                                                                <i class="fas fa-crown text-warning me-1"></i> 
                                                                {{ $s->propios_count }} {{ Str::plural('Propio', $s->propios_count) }}
                                                            </span>
                                                        @endif
                                            
                                                        {{-- CONTADOR DE COMPARTIDOS (AZUL) --}}
                                                        @if($s->compartidos_count > 0)
                                                            <span class="badge border text-dark bg-white w-100" style="font-weight: 600; font-size: 0.75rem;">
                                                                <i class="fas fa-users text-info me-1"></i> 
                                                                {{ $s->compartidos_count }} {{ Str::plural('Comp.', $s->compartidos_count) }}
                                                            </span>
                                                        @endif
                                            
                                                    </div>
                                                @else
                                                    {{-- SI NO TIENE NINGUNO --}}
                                                    <span class="text-muted small" style="font-size: 0.8rem;">—</span>
                                                @endif
                                            </td>
                                            {{-- ============================== --}}

                                            <td style="vertical-align: middle;">
                                                @if($s->tipo_beneficio === 'exonerado')
                                                    <span class="badge rounded-pill fw-bold px-3 py-2"
                                                        style="background-color: #198754; color: white; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                        EXONERADO
                                                    </span>

                                                @elseif($s->tipo_beneficio === 'con_subsidio')
                                                    <span class="badge rounded-pill fw-bold px-3 py-2"
                                                        style="background-color: #0d6efd; color: white; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                        CON SUBSIDIO
                                                    </span>

                                                @else
                                                    <span class="badge rounded-pill fw-bold px-3 py-2"
                                                        style="background-color: #495057; color: white; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                        SIN SUBSIDIO
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info mb-0 me-1 open-modal"
                                                    data-url="{{ route('socios.show', $s) }}"><i
                                                        class="fa fa-eye" style="font-size: 0.7rem;"></i></button>
                                                <button type="button" class="btn btn-sm btn-warning mb-0 me-1 open-modal"
                                                    data-url="{{ route('socios.edit', $s) }}"><i
                                                        class="fa-solid fa-pen-to-square"style="font-size: 0.7rem;"></i></button>
                                                <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn"
                                                    data-url="{{ route('socios.destroy', $s) }}"
                                                    data-item="{{ $s->apellidos }}"><i
                                                        class="fa-solid fa-trash"style="font-size: 0.7rem;"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4 text-muted">No se encontraron socios.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">{{ $socios->links() }}</div>
                    </div>
                </div>
            </form>

            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content"></div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function initModalLogic() {
                const selectBeneficio = document.getElementById('select_beneficio');
                const divFechaExo = document.getElementById('div_fecha_exo');
                if (selectBeneficio && divFechaExo) {
                    const toggle = () => {
                        if (selectBeneficio.value === 'exonerado') {
                            divFechaExo.style.display = 'block';
                            const input = divFechaExo.querySelector('input');
                            if (input && !input.value) input.focus();
                        } else {
                            divFechaExo.style.display = 'none';
                        }
                    };
                    selectBeneficio.addEventListener('change', toggle);
                    toggle();
                }
            }

            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(() => { document.querySelectorAll('.alert-temporal').forEach(a => a.remove()); }, 3000);

                const searchInput = document.getElementById('searchInput');
                const comunidadFilter = document.getElementById('comunidadFilter');
                function applyFilters() { window.location.href = "{{ route('socios.index') }}?search=" + encodeURIComponent(searchInput.value) + "&comunidad_id=" + comunidadFilter.value; }
                if (searchInput) searchInput.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); applyFilters(); } });
                if (comunidadFilter) comunidadFilter.addEventListener('change', applyFilters);

                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.body.addEventListener('click', function (e) {
                    if (e.target.closest('.open-modal')) {
                        const btn = e.target.closest('.open-modal');
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(btn.getAttribute('data-url')).then(r => r.text()).then(h => {
                            modalEl.querySelector('.modal-content').innerHTML = h;
                            initModalLogic();
                        });
                    }
                });

                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        Swal.fire({
                            title: '¿Eliminar Socio?', html: `¿Deseas eliminar al socio <b>"${this.getAttribute('data-item')}"</b>?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
                        }).then((r) => { if (r.isConfirmed) { const f = document.getElementById('deleteForm'); f.action = this.getAttribute('data-url'); f.submit(); } });
                    });
                });
            });

            function toggleSelectAll() { const c = document.getElementById('selectAll').checked; document.querySelectorAll('.check-item').forEach(x => x.checked = c); }
        </script>
    </main>
</x-app-layout>