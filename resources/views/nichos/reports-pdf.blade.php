<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Nichos</title>
    <style>
        @page { margin: 0cm 0cm; }
        body {
            font-family: 'Arial', sans-serif;
            margin-top: 4cm; margin-bottom: 3cm; margin-left: 2cm; margin-right: 2cm;
            background-color: #ffffff;
        }
        header { position: fixed; top: 0.5cm; left: 0cm; right: 0cm; height: 3cm; padding: 0 1.5cm; }
        footer { position: fixed; bottom: 0cm; left: 0cm; right: 0cm; height: 4cm; padding: 0 1.5cm; }
        header img, footer img { width: 100%; height: auto; }
        
        .footer-meta { width: 100%; font-size: 9px; color: #333; margin-bottom: 5px; border-top: 1px solid #ccc; padding-top: 5px; }
        .footer-contacto { text-align: center; font-size: 9px; color: #333; font-weight: bold; line-height: 1.2; margin-bottom: 5px; }
        .reporte-titulo { text-align: center; font-size: 18px; font-weight: bold; color: #1c2a48; margin: 0 0 15px 0; text-transform: uppercase; }
        .fecha-top { text-align: right; font-size: 10px; margin-bottom: 5px; color: #333; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background-color: #1c2a48; color: white; padding: 6px 4px; text-align: center; text-transform: uppercase; font-size: 9px; }
        td { padding: 5px 4px; border: 1px solid #ddd; text-align: center; color: #333; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .text-left { text-align: left !important; padding-left: 6px; }
    </style>
</head>
<body>
    <header><img src="{{ public_path('assets/img/encabezado.png') }}" alt="Encabezado"></header>
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
        <div class="reporte-titulo">REPORTE DE NICHOS</div>

        @if ($data->isEmpty())
            <div style="text-align: center; padding: 30px; font-size: 12px; color: #888;">No hay datos para mostrar.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">#</th>
                        @foreach ($headings as $index => $heading)
                            @if ($index > 0)
                                <th 
                                    @if ($heading == 'Código') style="width: 50px;" 
                                    @elseif ($heading == 'Bloque') style="width: 80px;"
                                    @elseif ($heading == 'Estado') style="width: 70px;"
                                    @elseif ($heading == 'Responsable (Titular)') style="text-align: left;"
                                    @elseif ($heading == 'Capacidad') style="width: 50px;"
                                    @elseif ($heading == 'Fallecidos (Cant.)') style="width: 50px;"
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
                                <td @if ($cellIndex == 4) class="text-left" @endif> {{-- Index 4 es Responsable --}}
                                    {{ $cell }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>
</body>
</html>