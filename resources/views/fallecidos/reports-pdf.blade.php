<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Fallecidos</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA */
        @page {
            margin: 0cm 0cm;
        }

        /* 2. CUERPO */
        body {
            font-family: 'Arial', sans-serif;
            margin-top: 5cm;    
            margin-bottom: 4.5cm; 
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            background-color: #ffffff;
        }

        /* 3. ENCABEZADO */
        header {
            position: fixed;
            top: 0.5cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
            padding-left: 1.5cm;
            padding-right: 1.5cm;
            z-index: 1000;
        }

        /* 4. PIE DE PÁGINA */
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 4.2cm; 
            padding-left: 1.5cm;
            padding-right: 1.5cm;
            z-index: 1000;
        }

        header img, footer img {
            width: 100%;
            height: auto;
        }

        /* --- ELEMENTOS DEL PIE DE PÁGINA --- */
        .footer-meta {
            width: 100%;
            font-size: 10px;
            color: #333;
            margin-bottom: 5px;
            border-top: 1px solid #ccc; 
            padding-top: 5px;
        }

        .footer-contacto {
            text-align: center;
            font-size: 10px;
            color: #333;
            font-weight: bold;
            line-height: 1.3;
            margin-bottom: 5px;
        }

        .email-link {
            color: #007bff;
            text-decoration: none;
        }

        /* --- CONTENIDO DEL REPORTE --- */
        .reporte-titulo {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .fecha-top {
            text-align: right;
            font-size: 12px;
            margin-bottom: 10px;
            color: #333;
        }

        /* TABLA */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px; /* Tamaño ajustado para que quepan las columnas */
        }
        th {
            background-color: #1c2a48;
            color: white;
            padding: 8px 4px; /* Padding reducido */
            text-align: center;
            text-transform: uppercase;
            font-size: 9px;
        }
        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            text-align: center;
            color: #333;
            vertical-align: middle;
        }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
    </style>
</head>
<body>

    <header>
        {{-- Asegúrate de que esta imagen exista en public/assets/img/ --}}
        <img src="{{ public_path('assets/img/encabezado.png') }}" alt="Encabezado">
    </header>

    <footer>
        <div class="footer-meta">
            <div style="float: left; width: 50%;">
                Generado por: {{ auth()->user()->name ?? 'Sistema' }}
            </div>
            <div style="float: right; width: 50%; text-align: right;">
                Otavalo, {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY') }}
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer-contacto">
            <div>06) 2-927-663</div>
            <div><a href="#" class="email-link">unoricosamashunchik@gmail.com</a></div>
            <div>Calle Las Almas y Bolívar</div>
        </div>

        {{-- Asegúrate de que esta imagen exista en public/assets/img/ --}}
        <img src="{{ public_path('assets/img/piedepagina.png') }}" alt="Pie de página">
    </footer>

    <main>
        <div class="fecha-top">
            Otavalo, {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY') }}
        </div>

        <div class="reporte-titulo">
            REPORTE GENERAL DE FALLECIDOS
        </div>

        @if ($data->isEmpty())
            <div style="text-align: center; padding: 30px; font-size: 14px; color: #888; border: 2px dashed #ccc;">
                No se encontraron registros para este reporte.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        {{-- Definimos los anchos específicos para Fallecidos --}}
                        <th style="width: 25px;">#</th>
                        <th style="width: 50px;">Código</th>
                        <th style="width: 65px;">Cédula</th>
                        <th>Apellidos y Nombres</th>
                        <th style="width: 70px;">Comunidad</th>
                        <th style="width: 55px;">F. Nac.</th>
                        <th style="width: 55px;">F. Fall.</th>
                        <th style="width: 35px;">Edad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            {{-- 1. Contador --}}
                            <td style="font-weight: bold; background-color: #f9f9f9;">
                                {{ $index + 1 }}
                            </td>

                            {{-- 2. Datos (Accedemos al array $row preparado en el controller) --}}
                            <td>{{ $row['codigo'] }}</td>
                            <td>{{ $row['cedula'] }}</td>
                            <td style="text-align: left; padding-left: 8px;">{{ $row['nombres_completos'] }}</td>
                            <td>{{ $row['comunidad'] }}</td>
                            <td>{{ $row['fecha_nac'] }}</td>
                            <td>{{ $row['fecha_fall'] }}</td>
                            <td>{{ $row['edad'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 20px; font-size: 10px; color: #666; text-align: right;">
                Total de registros: {{ count($data) }}
            </div>
        @endif
    </main>

</body>
</html>