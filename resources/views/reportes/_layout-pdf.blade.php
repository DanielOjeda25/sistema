{{-- Layout compartido de los reportes PDF: sigue el patron de la materia.
     1 IDENTIDAD: logo + datos de la organizacion en cada pagina.
     2 ALCANCE: caja con fecha desde/hasta, filtros aplicados.
     3 TRAZABILIDAD: codigo de emision, usuario y fecha y hora.
     4 RESULTADOS: banner de cantidad, tabla numerada y criterio.
     5 NAVEGACION: la cabecera se repite y el pie muestra pagina X de Y. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        @page {
            margin: 130px 36px 56px 36px;
            @bottom-left { content: "CRUZNEGRA · Uso interno"; font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #9ca3af; }
            @bottom-right { content: "{{ $codigo }} · Página " counter(page) " de " counter(pages); font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #6b7280; font-weight: bold; }
        }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #374151; }
        table.detalle { width: 100%; border-collapse: collapse; }
        table.detalle th { background: #008c63; color: #ffffff; border: 1px solid #e5e7eb; padding: 5px 6px; font-size: 8px; text-transform: uppercase; text-align: left; letter-spacing: 0.5px; }
        table.detalle td { border: 1px solid #e5e7eb; padding: 5px 6px; }
        table.detalle tr:nth-child(even) td { background: #f8fafc; }
        .mostrando { margin-top: 6px; font-size: 9px; color: #6b7280; }
        h2.seccion { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #008c63; margin: 14px 0 4px; }
        .criterio { font-size: 9.5px; color: #374151; }
        .criterio p { margin: 0 0 3px; }

        /* Metricas: fila sobria con lineas finas, sin cajas de color */
        table.metricas { width: 100%; border-collapse: collapse; margin-bottom: 12px; border-top: 2px solid #008c63; border-bottom: 1px solid #e5e7eb; }
        table.metricas td { padding: 9px 8px; text-align: center; border-right: 1px solid #e5e7eb; }
        table.metricas td:last-child { border-right: none; }
        table.metricas .valor { font-size: 14px; font-weight: bold; color: #008c63; }
        table.metricas .valor.alerta { color: #dc2626; }
        table.metricas .rotulo { font-size: 7.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-top: 2px; }
    </style>
</head>
<body>
    {{-- Cabecera institucional: se repite en todas las paginas --}}
    <div style="position: fixed; top: -108px; left: -36px; right: -36px; background: #ffffff; padding: 18px 36px 10px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle; width: 60%;">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td style="padding-right: 10px;"><img src="{{ public_path('images/cruznegra-logo.png') }}" alt="CRUZNEGRA" style="height: 44px;"></td>
                            <td style="vertical-align: middle;">
                                <span style="font-size: 18px; font-weight: bold; color: #374151; letter-spacing: 1px;">CRUZNEGRA</span><br>
                                <span style="font-size: 9px; color: #008c63;">Gestión de clientes, proyectos y facturación</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: middle; text-align: right; font-size: 9px; color: #6b7280; line-height: 1.5;">
                    Posadas, Misiones<br>
                    Cel. (0376) 400-0000<br>
                    informes@cruznegra.example
                </td>
            </tr>
        </table>
        <div style="border-bottom: 2px solid #00b87d; margin-top: 10px;"></div>
    </div>

    {{-- Titulo del reporte + codigo de emision --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
        <tr>
            <td style="vertical-align: bottom;">
                <span style="font-size: 20px; font-weight: bold; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">{{ $titulo }}</span>
            </td>
            <td style="vertical-align: bottom; text-align: right;">
                <span style="font-size: 11px; font-weight: bold; color: #6b7280;">{{ $codigo }}</span>
            </td>
        </tr>
    </table>
    <p style="margin: 0 0 10px; font-size: 10px; color: #6b7280;">{{ $subtitulo }}</p>

    {{-- Caja de alcance: periodo, emisor y filtros --}}
    <div style="border: 1px solid #e5e7eb; border-top: 2px solid #008c63; padding: 12px 14px; margin-bottom: 12px;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
            <tr>
                <td style="width: 25%;"><div style="font-size: 8px; font-weight: bold; color: #008c63; text-transform: uppercase; letter-spacing: 0.5px;">Fecha desde</div><div style="font-size: 11px; font-weight: bold;">{{ $desde }}</div></td>
                <td style="width: 25%;"><div style="font-size: 8px; font-weight: bold; color: #008c63; text-transform: uppercase; letter-spacing: 0.5px;">Fecha hasta</div><div style="font-size: 11px; font-weight: bold;">{{ $hasta }}</div></td>
                <td style="width: 25%;"><div style="font-size: 8px; font-weight: bold; color: #008c63; text-transform: uppercase; letter-spacing: 0.5px;">Emitido por</div><div style="font-size: 11px; font-weight: bold;">{{ $emitidoPor }}</div></td>
                <td><div style="font-size: 8px; font-weight: bold; color: #008c63; text-transform: uppercase; letter-spacing: 0.5px;">Fecha y hora</div><div style="font-size: 11px; font-weight: bold;">{{ $fechaHora }}</div></td>
            </tr>
        </table>
        <div style="border-top: 1px solid #e5e7eb; padding-top: 6px;">
            <span style="font-size: 8px; font-weight: bold; color: #008c63; text-transform: uppercase; letter-spacing: 0.5px;">Filtros aplicados</span><br>
            <span style="font-size: 9.5px; color: #374151;">{{ $filtrosTexto }}</span>
        </div>
    </div>

    {{-- Banner de resultados --}}
    <div style="background: #008c63; color: #ffffff; padding: 10px 14px; margin-bottom: 12px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="font-size: 13px; font-weight: bold;">{{ $banner }}</td>
                <td style="text-align: right; font-size: 9px; color: #cbd5e1;">{{ $orden }}</td>
            </tr>
        </table>
    </div>

    {{-- Contenido del reporte --}}
    @yield('contenido')

    @if (isset($criterio))
        <h2 class="seccion">{{ $criterioTitulo ?? 'Criterio del reporte' }}</h2>
        <div class="criterio">{!! $criterio !!}</div>
    @endif
</body>
</html>
