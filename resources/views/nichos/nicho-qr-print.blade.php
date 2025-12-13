<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>QR {{ $nicho->codigo }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #e9ecef; 
            display: flex; flex-direction: column; align-items: center; justify-content: center; 
            min-height: 100vh; padding: 20px; 
        }
        .preview-card {
            background: white; width: 100%; max-width: 350px; padding: 30px; 
            border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            text-align: center; border: 1px solid #dee2e6;
        }
        h2 { color: #1c2a48; margin-bottom: 5px; font-size: 28px; }
        p.subtitle { color: #6c757d; margin-bottom: 20px; font-size: 14px; }
        img.qr-img { width: 100%; height: auto; display: block; border: 1px solid #eee; border-radius: 8px; margin-bottom: 20px; }
        .data-box {
            background: #f8f9fa; border: 1px dashed #ced4da; padding: 15px; 
            border-radius: 8px; text-align: left; font-size: 12px; color: #495057; 
            white-space: pre-wrap; line-height: 1.4;
        }
        /* BOTÓN DE DESCARGA */
        .btn-download {
            display: block; width: 100%; max-width: 350px; margin-top: 20px; 
            background-color: #28a745; color: white; text-align: center; padding: 15px; 
            border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 16px; 
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2); transition: background-color 0.3s ease;
        }
        .btn-download:hover { background-color: #218838; }
    </style>
    {{-- Icono de descarga (opcional) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="preview-card">
        <h2>{{ $nicho->codigo }}</h2>
        <p class="subtitle">{{ $nicho->bloque->nombre }}</p>
        
        <img src="{{ $qrUrl }}" class="qr-img" alt="QR Code">

        <div class="data-box">
            <strong>CONTENIDO DEL QR:</strong><br><br>
            {{ $textoQR }}
        </div>
    </div>

    {{-- Aquí llamamos a la ruta nueva que descarga la imagen --}}
    <a href="{{ route('nichos.qr.image', $nicho) }}" class="btn-download">
        <i class="fas fa-download"></i> DESCARGAR IMAGEN PNG
    </a>

</body>
</html>