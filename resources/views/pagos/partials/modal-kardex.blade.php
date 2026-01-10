{{-- CABECERA (Estilo idéntico a tu modal de cobro) --}}
<div class="modal-header bg-info text-white">
    <h5 class="modal-title fw-bold">
        <i class="fas fa-history me-2"></i>Historial de Recaudación
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body bg-light">

    {{-- 1. RESUMEN DEL SOCIO (Igual que en tu modal de cobro para mantener consistencia) --}}
    <div class="row mb-3">
        <div class="col-12 bg-white p-3 rounded border d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <small class="text-secondary d-block fw-bold">SOCIO</small>
                <span class="fs-5 fw-bolder text-info">{{ $socio->apellidos }} {{ $socio->nombres }}</span>
                <div class="small text-muted mt-1">Cédula: <span class="text-dark fw-bold">{{ $socio->cedula }}</span>
                </div>
            </div>
            <div class="text-end">
                <small class="text-secondary d-block fw-bold">ESTADO ACTUAL</small>
                @if(count($aniosPendientes) > 0)
                    <span class="badge bg-danger fs-6 px-3 py-2 shadow-sm">
                        Debe {{ count($aniosPendientes) }} años
                    </span>
                @else
                    <span class="badge fs-6 px-3 py-2 shadow-sm text-white"
                        style="background-color: #54bc51ff; border: 1px solid #146c43;">
                        <i class="fas fa-check-circle me-1"></i> ¡Al día!
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. DATOS DE RESUMEN (Total aportado) --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-4 border-info">
                <div class="card-body py-2 d-flex align-items-center">
                    <div class="me-3 text-info">
                        <i class="fas fa-piggy-bank fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-uppercase small fw-bold text-secondary">Total Histórico Pagado</div>
                        <div class="fs-4 fw-bold text-dark">${{ number_format($totalHistorico, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. LA TABLA DE RECIBOS (Lo que reemplaza al formulario) --}}
    <div class="card border shadow-sm">
        <div class="card-header bg-white fw-bold text-secondary border-bottom py-2">
            <i class="fas fa-list-ul me-1"></i> Detalle de Pagos Realizados
        </div>

        <div class="card-body p-0 table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0 text-center align-middle" style="font-size: 0.9rem;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="text-secondary small text-uppercase"># Recibo</th>
                        <th class="text-secondary small text-uppercase">Años Cancelados</th>
                        <th class="text-secondary small text-uppercase">Fecha Pago</th>
                        <th class="text-secondary small text-uppercase">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recibos as $recibo)
                        <tr>
                            <td class="fw-bold text-muted">#{{ $recibo->id }}</td>

                            {{-- Columna de Años (Badges Azules) --}}
                            <td>
                                @foreach($recibo->pagos as $pago)
                                    <span
                                        class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 me-1 mb-1">
                                        {{ $pago->anio_pagado }}
                                    </span>
                                @endforeach
                            </td>

                            <td class="text-dark fw-bold">
                                {{ \Carbon\Carbon::parse($recibo->fecha_pago)->format('d/m/Y') }}
                            </td>

                            <td class="fw-bold text-success">
                                ${{ number_format($recibo->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0 fw-bold">No existen pagos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal-footer bg-light">
    <button type="button" class="btn btn-success fw-bold px-4 open-modal"
        data-url="{{ route('pagos.index', $socio->id) }}">
        <i class="fas fa-wallet me-2"></i> Ir a Cobrar
    </button>
</div>