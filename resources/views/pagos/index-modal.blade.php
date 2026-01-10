<div class="modal-header bg-primary text-white">
    <h5 class="modal-title fw-bold">
        <i class="fas fa-hand-holding-usd me-2"></i>Gestión de Pagos
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    
    {{-- 1. RESUMEN DEL SOCIO --}}
    <div class="row mb-3">
        <div class="col-12 bg-light p-3 rounded border d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <small class="text-secondary d-block fw-bold">SOCIO</small>
                <span class="fs-5 fw-bolder text-primary">{{ $socio->apellidos }} {{ $socio->nombres }}</span>
                <div class="small text-muted mt-1">Cédula: <span class="text-dark fw-bold">{{ $socio->cedula }}</span></div>
            </div>
            <div class="text-end">
                <small class="text-secondary d-block fw-bold">ESTADO DE CUENTA</small>
                @if(count($aniosPendientes) > 0)
                    <span class="badge bg-danger fs-6 px-3 py-2 shadow-sm">
                        Debe {{ count($aniosPendientes) }} años
                    </span>
                @else
                    <span class="badge bg-success fs-6 px-3 py-2 shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> ¡Al día!
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- 2. COLUMNA IZQUIERDA: FORMULARIO DE COBRO --}}
        <div class="col-md-5">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-white fw-bold text-dark border-bottom py-2">
                    <i class="fas fa-cash-register me-1 text-primary"></i> Registrar Nuevo Pago
                </div>
                <div class="card-body bg-light">
                    <form action="{{ route('pagos.store', $socio) }}" method="POST">
                        @csrf
                        
                        {{-- LISTA DE AÑOS (Checkboxes) --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Seleccione Años a Pagar:</label>
                            
                            <div class="border rounded p-2 bg-white" style="max-height: 200px; overflow-y: auto;">
                                @if(count($aniosPendientes) > 0)
                                    @foreach($aniosPendientes as $anio)
                                        <div class="form-check border-bottom pb-2 mb-2">
                                            {{-- Checkbox normal --}}
                                            <input class="form-check-input" type="checkbox" name="anios_pagados[]" value="{{ $anio }}" id="anio_{{ $anio }}" style="cursor: pointer; transform: scale(1.1);">
                                            
                                            <label class="form-check-label fw-bold text-dark w-100 ps-1" for="anio_{{ $anio }}" style="cursor: pointer;">
                                                Año {{ $anio }}
                                                <span class="float-end badge bg-danger text-white" style="font-size: 0.65rem;">PENDIENTE</span>
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-success small fw-bold text-center py-4 bg-light rounded border border-success border-opacity-25">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                        No tiene deudas pendientes.
                                    </div>
                                @endif
                                
                                {{-- AQUÍ QUITAMOS EL BLOQUE DE ADELANTO QUE HABÍA ANTES --}}

                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Fecha de Pago</label>
                            <input type="date" name="fecha_pago" value="{{ date('Y-m-d') }}" class="form-control fw-bold text-dark" required>
                        </div>
                        
                        <div class="mb-3">
                             <label class="form-label small fw-bold text-secondary">Observación</label>
                             <input type="text" name="observacion" class="form-control" placeholder="Ej: Recibo N° 123...">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Guardar Pagos
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. COLUMNA DERECHA: HISTORIAL DE PAGOS (Solo lectura) --}}
        <div class="col-md-7">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-white fw-bold text-secondary border-bottom py-2">
                    <i class="fas fa-history me-1"></i> Historial de Pagos
                </div>
                <div class="card-body p-0 table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0 text-center align-middle" style="font-size: 0.85rem;">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="text-secondary small text-uppercase">Año</th>
                                <th class="text-secondary small text-uppercase">Monto</th>
                                <th class="text-secondary small text-uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($socio->pagos as $pago)
                                <tr>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold border border-success border-opacity-25 px-2 py-1">
                                            {{ $pago->anio_pagado }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">${{ number_format($pago->monto, 2) }}</td>
                                    <td class="text-muted small fw-bold">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted py-5 text-center">
                                        <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-25"></i><br>
                                        <span class="small fw-bold">Sin historial de pagos registrados.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
</div>

{{-- SCRIPT PARA QUE EL BOTÓN ABRA EL MODAL --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Inicializar el modal de Bootstrap
        const modalEl = document.getElementById('dynamicModal');
        // Asegurarse de que el modal exista en el layout (si no está, lo creamos al vuelo para prevenir errores)
        if(!modalEl) {
             console.error("Falta el div #dynamicModal en tu layout principal.");
             return;
        }
        const modal = new bootstrap.Modal(modalEl);

        // Escuchar clics en cualquier botón con clase .open-modal
        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('.open-modal');
            
            if (btn) {
                e.preventDefault();
                
                // 1. Poner un spinner de carga mientras busca la info
                modalEl.querySelector('.modal-content').innerHTML = `
                    <div class="modal-body text-center p-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 fw-bold text-secondary">Cargando...</p>
                    </div>`;
                
                // 2. Mostrar el modal
                modal.show();

                // 3. Pedir la información al servidor (AJAX)
                const url = btn.getAttribute('data-url');
                
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Error al cargar');
                        return response.text();
                    })
                    .then(html => {
                        // 4. Poner el contenido dentro del modal
                        modalEl.querySelector('.modal-content').innerHTML = html;
                        
                        // Si hay scripts dentro del modal cargado, ejecutarlos manualmente
                        const scripts = modalEl.querySelectorAll("script");
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement("script");
                            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });
                    })
                    .catch(err => {
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Error</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center p-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <p>No se pudo cargar la ventana.</p>
                                <small class="text-muted">${err.message}</small>
                            </div>`;
                    });
            }
        });
    });
</script>
