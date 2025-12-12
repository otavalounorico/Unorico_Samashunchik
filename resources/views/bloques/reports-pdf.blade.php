<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Bloques</title>
    <style>
        @page { margin: 0cm 0cm; }
        body {
            font-family: 'Arial', sans-serif;
            /* Márgenes ajustados para vertical */
            margin-top: 4cm; 
            margin-bottom: 3cm; 
            margin-left: 2cm; 
            margin-right: 2cm;
            background-color: #ffffff;
        }

        /* Encabezado y Pie de página fijos */
        header { position: fixed; top: 0.5cm; left: 0cm; right: 0cm; height: 3cm; padding: 0 1.5cm; z-index: 1000; }
        footer { position: fixed; bottom: 0cm; left: 0cm; right: 0cm; height: 4cm; padding: 0 1.5cm; z-index: 1000; }
        header img, footer img { width: 100%; height: auto; }
        
        /* Textos del footer */
        .footer-meta { width: 100%; font-size: 9px; color: #333; margin-bottom: 5px; border-top: 1px solid #ccc; padding-top: 5px; }
        .footer-contacto { text-align: center; font-size: 9px; color: #333; font-weight: bold; line-height: 1.2; margin-bottom: 5px; }
        
        .reporte-titulo { text-align: center; font-size: 18px; font-weight: bold; color: #1c2a48; margin: 0 0 15px 0; text-transform: uppercase; }
        .fecha-top { text-align: right; font-size: 10px; margin-bottom: 5px; color: #333; }

        /* TABLA */
        table { width: 100%; border-collapse: collapse; font-size: 10px; } /* Fuente un poco más pequeña para que entre todo */
        th { background-color: #1c2a48; color: white; padding: 6px 4px; text-align: center; text-transform: uppercase; font-size: 9px; }
        td { padding: 5px 4px; border: 1px solid #ddd; text-align: center; color: #333; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        .text-left { text-align: left !important; padding-left: 6px; }
    </style>
</head>
<body>

    <header>
        <img src="{{ public_path('assets/img/encabezado.png') }}" alt="Encabezado">
    </header>

    <footer>
        <div class="footer-meta">
            <div style="float: left; width: 50%;">Generado por: <b>{{ auth()->user()->name ?? 'Sistema' }}</b></div>
            <div style="float: right; width: 50%; text-align: right;">Otavalo, {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY') }}</div>
            <div style="clear: both;"></div>
        </div>
        <div class="footer-contacto">
            <div>06) 2-927-663</div>
            <div>unoricosamashunchik@gmail.com</div>
            <div>Calle Las Almas y Bolívar</div>
        </div>
        <img src="{{ public_path('assets/img/piedepagina.png') }}" alt="Pie de página">
    </footer>

    <main>
        <div class="fecha-top">Fecha: {{ date('d/m/Y H:i') }}</div>
        <div class="reporte-titulo">REPORTE DE BLOQUES</div>

        @if ($data->isEmpty())
            <div style="text-align: center; padding: 30px; font-size: 12px; color: #888;">No hay datos para mostrar.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">#</th>
                        @foreach ($headings as $index => $heading)
                            @if ($index > 0) {{-- Saltamos ID --}}
                                <th 
                                    @if ($heading == 'Código') style="width: 50px;" 
                                    @elseif ($heading == 'Nombre del Bloque') style="width: 100px; text-align: left;"
                                    {{-- Descripción sin width fijo para que ocupe el resto --}}
                                    @elseif ($heading == 'Descripción') style="text-align: left;"
                                    @elseif ($heading == 'Área (m2)') style="width: 60px;"
                                    @elseif ($heading == 'Fecha Creación') style="width: 65px;"
                                    @endif
                                >
                                    {{ $heading }}
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td style="font-weight: bold; background-color: #e9ecef;">{{ $index + 1 }}</td>
                            
                            @foreach (array_slice((array)$row, 1) as $cellIndex => $cell)
                                <td 
                                    {{-- Nombre (1) y Descripción (2) a la izquierda --}}
                                    @if ($cellIndex == 1 || $cellIndex == 2) class="text-left" @endif
                                >
                                    {{ $cell }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 10px; font-size: 9px; color: #666; text-align: right;">
                Registros: {{ count($data) }}
            </div>
        @endif
    </main>
</body>
</html>