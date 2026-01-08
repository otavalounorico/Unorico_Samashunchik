<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Socios</title>
    <style>
        @page { margin: 0cm 0cm; }
        body {
            font-family: 'Arial', sans-serif;
            margin-top: 5.5cm; 
            margin-bottom: 3cm; 
            margin-left: 1cm; 
            margin-right: 1cm;
            background-color: #ffffff;
        }
        
        header { position: fixed; top: 0.5cm; left: 0cm; right: 0cm; height: 3.5cm; padding: 0 1cm; z-index: 1000; }
        footer { position: fixed; bottom: 0cm; left: 0cm; right: 0cm; height: 3cm; padding: 0 1cm; z-index: 1000; }
        
        header img, footer img { width: 100%; height: auto; }
        
        .footer-meta { width: 100%; font-size: 9px; color: #333; margin-bottom: 5px; border-top: 1px solid #ccc; padding-top: 5px; }
        .footer-contacto { text-align: center; font-size: 9px; color: #333; font-weight: bold; line-height: 1.3; margin-bottom: 5px; }
        .email-link { color: #007bff; text-decoration: none; }
        .reporte-titulo { text-align: center; font-size: 18px; font-weight: bold; color: #1c2a48; margin: 0px 0 15px 0; text-transform: uppercase; }
        .fecha-top { text-align: right; font-size: 10px; margin-bottom: 5px; color: #333; }

        /* TABLA */
        table { width: 100%; border-collapse: collapse; font-size: 8px; } 
        
        /* ESTILO DE CABECERA */
        th { 
            background-color: #1c2a48; 
            color: white; 
            padding: 6px 4px; /* Un poco más de padding lateral */
            text-align: center; 
            text-transform: uppercase; 
            font-size: 8px; 
            border: 1px solid #ffffff; /* Borde blanco */
        }
        
        td { padding: 4px 4px; border: 1px solid #ddd; text-align: center; color: #333; vertical-align: middle; word-wrap: break-word; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        .text-left { text-align: left !important; padding-left: 4px; }
        .text-xs { font-size: 7px; }
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
            <div><a href="#" class="email-link">unoricosamashunchik@gmail.com</a></div>
            <div>Calle Las Almas y Bolívar</div>
        </div>
        <img src="{{ public_path('assets/img/piedepagina.png') }}" alt="Pie de página">
    </footer>

    <main>
        <div class="fecha-top">Fecha de emisión: {{ date('d/m/Y H:i') }}</div>
        <div class="reporte-titulo">REPORTE DETALLADO DE SOCIOS</div>

        @if ($data->isEmpty())
            <div style="text-align: center; padding: 30px; font-size: 14px; color: #888;">No se encontraron socios seleccionados.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 20px;">#</th>
                        @foreach ($headings as $index => $heading)
                            @if ($index > 0) {{-- Saltamos ID --}}
                                <th 
                                    {{-- DISTRIBUCIÓN EQUILIBRADA APROVECHANDO LA HOJA HORIZONTAL --}}
                                    
                                    {{-- 1. Código: Un poco más de espacio --}}
                                    @if ($heading == 'Código') style="width: 45px;" 
                                    
                                    @elseif ($heading == 'Cédula') style="width: 55px;"
                                    
                                    {{-- 2. Nombres: Ancho generoso pero controlado (180px) --}}
                                    @elseif ($heading == 'Apellidos y Nombres') style="width: 180px;"
                                    
                                    @elseif ($heading == 'Fecha Nac.') style="width: 45px;"
                                    
                                    {{-- 3. Ubicación y Dirección: Anchos fijos iguales y suficientes --}}
                                    @elseif ($heading == 'Ubicación') style="width: 110px;"
                                    @elseif ($heading == 'Dirección') style="width: 110px;" 
                                    
                                    @elseif ($heading == 'Edad') style="width: 25px;"
                                    @elseif ($heading == 'Beneficio') style="width: 50px;"
                                    @elseif ($heading == 'Teléfono') style="width: 50px;"
                                    @elseif ($heading == 'Condición') style="width: 50px;"
                                    @elseif ($heading == 'Estatus') style="width: 40px;"
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
                            
                            @foreach (array_slice((array)$row, 1) as $key => $cell)
                                <td 
                                    {{-- Alineación izquierda para textos largos --}}
                                    @if ($key == 2 || $key == 4 || $key == 5) class="text-left" @endif
                                    
                                    {{-- Letra pequeña para Ubicación y Dirección --}}
                                    @if ($key == 4 || $key == 5) class="text-left text-xs" @endif
                                >
                                    {{ $cell }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 20px; font-size: 9px; color: #666; text-align: right;">
                Total de registros: {{ count($data) }}
            </div>
        @endif
    </main>
</body>
</html>