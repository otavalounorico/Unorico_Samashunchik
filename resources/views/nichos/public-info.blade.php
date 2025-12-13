<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Nicho</title>
    {{-- Usamos Bootstrap simple desde CDN para que se vea bien rápido --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .card-nicho { max-width: 400px; margin: 2rem auto; border: none; shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 15px; overflow: hidden; }
        .header-bg { background-color: #1c2a48; color: white; padding: 2rem 1rem; text-align: center; }
        .nicho-codigo { font-size: 2.5rem; font-weight: bold; margin: 0; }
        .nicho-bloque { font-size: 1.1rem; opacity: 0.8; }
        .info-item { border-bottom: 1px solid #eee; padding: 1rem; display: flex; justify-content: space-between; }
        .info-item:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #555; }
        .footer-logo { text-align: center; margin-top: 2rem; color: #888; font-size: 0.8rem; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card card-nicho shadow">
            <div class="header-bg">
                <h1 class="nicho-codigo">{{ $nicho->codigo }}</h1>
                <div class="nicho-bloque">{{ $nicho->bloque->nombre }}</div>
            </div>
            
            <div class="card-body p-0 bg-white">
                
                <div class="info-item">
                    <span class="label">Estado actual</span>
                    @if($nicho->estado == 'ocupado')
                        <span class="badge bg-danger">Ocupado</span>
                    @elseif($nicho->estado == 'disponible')
                        <span class="badge bg-success">Disponible</span>
                    @else
                        <span class="badge bg-warning text-dark">{{ ucfirst($nicho->estado) }}</span>
                    @endif
                </div>

                <div class="info-item">
                    <span class="label">Capacidad</span>
                    <span>{{ $nicho->capacidad }} espacios</span>
                </div>

                {{-- AQUÍ MOSTRARÁS LOS FALLECIDOS EN EL FUTURO --}}
                <div class="p-4 text-center text-muted border-top bg-light">
                    <small>
                        <em>
                            @if($nicho->estado == 'ocupado')
                                (Información del fallecido disponible próximamente)
                            @else
                                Este espacio se encuentra disponible.
                            @endif
                        </em>
                    </small>
                </div>

            </div>
        </div>

        <div class="footer-logo">
            <p>© {{ date('Y') }} Cementerio Municipal</p>
        </div>
    </div>

</body>
</html>