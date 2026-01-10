<x-app-layout>
    {{-- 1. ESTILOS (Idénticos al Index de Socios) --}}
    <style>
        /* INPUTS */
        .input-group-text {
            border-color: #dee2e6;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #5ea6f7;
            box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25);
        }

        /* BADGES (Estilo Píldora con Sombra) */
        .badge-pill-custom {
            border-radius: 50rem;
            padding: 0.5em 1em;
            font-weight: 700;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">

            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <div>
                    {{-- Mismo color de título #1c2a48 --}}
                    <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Historial de Recaudación</h3>
                    <p class="text-secondary text-sm mb-0">Listado completo de pagos registrados.</p>
                </div>

                <div class="d-flex gap-3 align-items-center mt-3 mt-md-0">
                    {{-- Tarjeta de Total (Estilizada sutilmente para no desentonar) --}}
                    <div class="bg-white border rounded px-3 py-2 shadow-sm d-flex align-items-center gap-3">
                        <small class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem;">Total
                            Histórico</small>
                        <h4 class="mb-0 fw-bolder text-success">${{ number_format($totalRecaudado, 2) }}</h4>
                    </div>

                    {{-- BOTÓN REGISTRAR PAGO (Cambiado a btn-success para igualar a 'Nuevo Socio') --}}
                    <button type="button" class="btn btn-success px-4 py-2 shadow-sm mb-0 open-modal"
                        style="height: fit-content;" data-url="{{ route('pagos.create') }}">
                        <i class="fas fa-plus me-2"></i> Registrar Pago
                    </button>
                </div>
            </div>

            {{-- 3. BUSCADOR (A la derecha, pequeño y con botón) --}}
            <div class="d-flex justify-content-end mb-4">
                <form method="GET">
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <input type="text" name="search" class="form-control shadow-none" placeholder="Buscar..."
                            value="{{ request('search') }}">

                        <button class="btn btn-primary mb-0 fw-bold" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- 4. TABLA (Estilo table-dark y bordered igual al Index de Socios) --}}
            <div class="card shadow-sm border">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle text-center mb-0">
                            {{-- Encabezado OSCURO --}}
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 80px;"># Recibo</th>
                                    <th class="text-start ps-4">Socio</th>
                                    <th>Años Cancelados</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recibos as $recibo)
                                    <tr>
                                        <td class="text-secondary fw-bold">{{ $recibo->id }}</td>

                                        <td class="text-start ps-4">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $recibo->socio->apellidos }}
                                                    {{ $recibo->socio->nombres }}</span>
                                                <span class="text-xs text-secondary">{{ $recibo->socio->cedula }}</span>
                                            </div>
                                        </td>

                                        {{-- Años: Badge estilo píldora azul sólido con sombra --}}
                                        <td>
                                            <span class="badge badge-pill-custom"
                                                style="background-color: #0d6efd; color: white;">
                                                {{ $recibo->anios_desc }}
                                            </span>
                                        </td>

                                        <td class="text-secondary text-sm fw-bold">
                                            {{ $recibo->fecha_pago->format('d/m/Y') }}
                                        </td>

                                        <td>
                                            <span
                                                class="fs-6 fw-bold text-dark">${{ number_format($recibo->total, 2) }}</span>
                                        </td>

                                        <td>
                                            {{-- Botones de acción iguales al estilo anterior --}}
                                            <button type="button" class="btn btn-sm btn-info mb-0 me-1 open-modal"
                                                data-url="{{ route('pagos.historial_socio', $recibo->socio_id) }}" 
                                                title="Ver Historial Completo de este Socio">
                                                <i class="fas fa-eye text-white" style="font-size: 0.7rem;"></i>
                                            </button>

                                            <button type="button" class="btn btn-sm btn-warning mb-0 me-1 open-modal"
                                                data-url="{{ route('pagos.edit', $recibo->id) }}" title="Corregir">
                                                <i class="fas fa-pen" style="font-size: 0.7rem;"></i>
                                            </button>

                                            <form action="{{ route('pagos.destroy', $recibo->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Eliminar este recibo completo?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger mb-0" title="Eliminar">
                                                    <i class="fas fa-trash" style="font-size: 0.7rem;"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-5 text-center text-muted">No se encontraron recibos
                                            registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Paginación --}}
                <div class="card-footer py-3 d-flex justify-content-end">
                    {{ $recibos->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL CONTAINER --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    {{-- Aquí carga el AJAX --}}
                </div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS (Tu lógica intacta) --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);

                document.body.addEventListener('click', function (e) {
                    const btn = e.target.closest('.open-modal');
                    if (btn) {
                        e.preventDefault();

                        // Spinner estilo Bootstrap
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="p-5 text-center">
                                <div class="spinner-border text-primary"></div>
                                <div class="mt-2 text-muted small fw-bold">Cargando información...</div>
                            </div>`;

                        modal.show();

                        fetch(btn.getAttribute('data-url'))
                            .then(r => r.text())
                            .then(html => {
                                modalEl.querySelector('.modal-content').innerHTML = html;
                                // Reactivar scripts internos
                                modalEl.querySelectorAll("script").forEach(oldScript => {
                                    const newScript = document.createElement("script");
                                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                                    oldScript.parentNode.replaceChild(newScript, oldScript);
                                });
                            })
                            .catch(err => {
                                console.error(err);
                                modalEl.querySelector('.modal-content').innerHTML =
                                    '<div class="p-4 text-danger text-center fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Error al cargar contenido.</div>';
                            });
                    }
                });
            });
        </script>
    </main>
</x-app-layout>